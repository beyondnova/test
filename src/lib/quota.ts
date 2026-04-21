import { db, usageCounters, subscriptions } from "@/lib/db";
import { and, eq, sql } from "drizzle-orm";
import type { Plan } from "@/lib/db/schema";

export const PLAN_LIMITS: Record<Plan, number> = {
  free: 15,
  pro: 500,
};

function monthBounds(now = new Date()) {
  const periodStart = new Date(
    Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), 1),
  );
  const periodEnd = new Date(
    Date.UTC(now.getUTCFullYear(), now.getUTCMonth() + 1, 1),
  );
  return { periodStart, periodEnd };
}

export async function getUserPlan(userId: string): Promise<Plan> {
  const rows = await db
    .select()
    .from(subscriptions)
    .where(eq(subscriptions.userId, userId))
    .limit(1);
  const sub = rows[0];
  if (!sub) return "free";
  if (sub.plan === "pro" && (sub.status === "active" || sub.status === "trialing")) {
    return "pro";
  }
  return "free";
}

export async function getUsage(userId: string) {
  const { periodStart, periodEnd } = monthBounds();
  const rows = await db
    .select()
    .from(usageCounters)
    .where(
      and(
        eq(usageCounters.userId, userId),
        eq(usageCounters.periodStart, periodStart),
      ),
    )
    .limit(1);

  if (rows.length > 0) return rows[0];

  const [created] = await db
    .insert(usageCounters)
    .values({
      userId,
      periodStart,
      periodEnd,
      generationsUsed: 0,
    })
    .returning();
  return created;
}

export async function getQuota(userId: string) {
  const [plan, usage] = await Promise.all([
    getUserPlan(userId),
    getUsage(userId),
  ]);
  const limit = PLAN_LIMITS[plan];
  const used = usage.generationsUsed;
  return {
    plan,
    limit,
    used,
    remaining: Math.max(0, limit - used),
    periodEnd: usage.periodEnd,
  };
}

export async function incrementUsage(userId: string) {
  await getUsage(userId);
  const { periodStart } = monthBounds();
  await db
    .update(usageCounters)
    .set({
      generationsUsed: sql`${usageCounters.generationsUsed} + 1`,
      updatedAt: new Date(),
    })
    .where(
      and(
        eq(usageCounters.userId, userId),
        eq(usageCounters.periodStart, periodStart),
      ),
    );
}

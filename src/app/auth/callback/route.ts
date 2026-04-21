import { NextResponse } from "next/server";
import { createClient } from "@/lib/supabase/server";
import { db, users, subscriptions, usageCounters } from "@/lib/db";
import { eq } from "drizzle-orm";

function monthBounds(now: Date) {
  const periodStart = new Date(Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), 1));
  const periodEnd = new Date(Date.UTC(now.getUTCFullYear(), now.getUTCMonth() + 1, 1));
  return { periodStart, periodEnd };
}

export async function GET(request: Request) {
  const { searchParams, origin } = new URL(request.url);
  const code = searchParams.get("code");
  const next = searchParams.get("next") ?? "/app";

  if (!code) {
    return NextResponse.redirect(`${origin}/login?error=missing_code`);
  }

  const supabase = await createClient();
  const { data, error } = await supabase.auth.exchangeCodeForSession(code);

  if (error || !data.user) {
    return NextResponse.redirect(`${origin}/login?error=auth_failed`);
  }

  const authUser = data.user;
  const existing = await db
    .select()
    .from(users)
    .where(eq(users.authProviderId, authUser.id))
    .limit(1);

  if (existing.length === 0) {
    await db.insert(users).values({
      id: authUser.id,
      email: authUser.email ?? "",
      authProviderId: authUser.id,
      displayName: authUser.user_metadata?.full_name ?? null,
    });
    await db.insert(subscriptions).values({
      userId: authUser.id,
      plan: "free",
      status: "active",
    });
    const { periodStart, periodEnd } = monthBounds(new Date());
    await db.insert(usageCounters).values({
      userId: authUser.id,
      periodStart,
      periodEnd,
      generationsUsed: 0,
    });
  }

  return NextResponse.redirect(`${origin}${next}`);
}

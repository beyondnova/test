import { NextResponse } from "next/server";
import { z } from "zod";
import { eq, desc } from "drizzle-orm";
import { getCurrentUser } from "@/lib/auth";
import { db, businessProfiles, generations } from "@/lib/db";
import { generateReplies } from "@/lib/anthropic";
import { getQuota, incrementUsage } from "@/lib/quota";

const generateSchema = z.object({
  message: z.string().min(1).max(4000),
  goal: z.enum([
    "answer_question",
    "handle_objection",
    "follow_up",
    "close_sale",
    "upsell",
  ]),
  tone: z.enum(["friendly", "professional", "luxury", "direct"]),
  language: z.enum(["en", "fr", "ar", "darija"]),
});

export async function POST(request: Request) {
  const user = await getCurrentUser();
  if (!user) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const body = await request.json();
  const parsed = generateSchema.safeParse(body);
  if (!parsed.success) {
    return NextResponse.json(
      { error: "Invalid input", issues: parsed.error.issues },
      { status: 400 },
    );
  }

  const quota = await getQuota(user.id);
  if (quota.remaining <= 0) {
    return NextResponse.json(
      { error: "Monthly generation limit reached.", quota },
      { status: 402 },
    );
  }

  const [profile] = await db
    .select()
    .from(businessProfiles)
    .where(eq(businessProfiles.userId, user.id))
    .limit(1);

  let result;
  try {
    result = await generateReplies({
      ...parsed.data,
      profile: profile ?? null,
    });
  } catch (err) {
    console.error("Generation failed", err);
    return NextResponse.json(
      { error: "Could not generate replies. Try again." },
      { status: 502 },
    );
  }

  const [saved] = await db
    .insert(generations)
    .values({
      userId: user.id,
      inputMessage: parsed.data.message,
      goal: parsed.data.goal,
      tone: parsed.data.tone,
      language: parsed.data.language,
      replies: result.replies,
      model: result.model,
      tokensIn: result.tokensIn,
      tokensOut: result.tokensOut,
      latencyMs: result.latencyMs,
    })
    .returning();

  await incrementUsage(user.id);

  return NextResponse.json({
    id: saved.id,
    replies: result.replies,
    latencyMs: result.latencyMs,
  });
}

export async function GET() {
  const user = await getCurrentUser();
  if (!user) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const rows = await db
    .select()
    .from(generations)
    .where(eq(generations.userId, user.id))
    .orderBy(desc(generations.createdAt))
    .limit(50);

  return NextResponse.json({ generations: rows });
}

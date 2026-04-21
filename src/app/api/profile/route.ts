import { NextResponse } from "next/server";
import { z } from "zod";
import { getCurrentUser } from "@/lib/auth";
import { db, businessProfiles } from "@/lib/db";
import { eq } from "drizzle-orm";

const profileSchema = z.object({
  storeName: z.string().max(200).optional().nullable(),
  category: z.string().max(200).optional().nullable(),
  deliveryZones: z.string().max(2000).optional().nullable(),
  paymentMethods: z.string().max(2000).optional().nullable(),
  returnPolicy: z.string().max(2000).optional().nullable(),
  faq: z.string().max(5000).optional().nullable(),
});

export async function GET() {
  const user = await getCurrentUser();
  if (!user) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const [profile] = await db
    .select()
    .from(businessProfiles)
    .where(eq(businessProfiles.userId, user.id))
    .limit(1);

  return NextResponse.json({ profile: profile ?? null });
}

export async function PUT(request: Request) {
  const user = await getCurrentUser();
  if (!user) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const body = await request.json();
  const parsed = profileSchema.safeParse(body);
  if (!parsed.success) {
    return NextResponse.json(
      { error: "Invalid input", issues: parsed.error.issues },
      { status: 400 },
    );
  }

  const values = {
    storeName: parsed.data.storeName ?? null,
    category: parsed.data.category ?? null,
    deliveryZones: parsed.data.deliveryZones ?? null,
    paymentMethods: parsed.data.paymentMethods ?? null,
    returnPolicy: parsed.data.returnPolicy ?? null,
    faq: parsed.data.faq ?? null,
  };

  const [existing] = await db
    .select()
    .from(businessProfiles)
    .where(eq(businessProfiles.userId, user.id))
    .limit(1);

  if (existing) {
    await db
      .update(businessProfiles)
      .set({ ...values, updatedAt: new Date() })
      .where(eq(businessProfiles.userId, user.id));
  } else {
    await db.insert(businessProfiles).values({ userId: user.id, ...values });
  }

  return NextResponse.json({ ok: true });
}

import { NextResponse } from "next/server";
import { eq } from "drizzle-orm";
import { getCurrentUser } from "@/lib/auth";
import { db, subscriptions } from "@/lib/db";
import { stripe, PRO_PRICE_ID } from "@/lib/stripe";
import { env } from "@/lib/env";

export async function POST() {
  const user = await getCurrentUser();
  if (!user) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  if (!PRO_PRICE_ID) {
    return NextResponse.json(
      { error: "Billing is not configured." },
      { status: 500 },
    );
  }

  const [sub] = await db
    .select()
    .from(subscriptions)
    .where(eq(subscriptions.userId, user.id))
    .limit(1);

  let customerId = sub?.stripeCustomerId ?? null;
  if (!customerId) {
    const customer = await stripe.customers.create({
      email: user.email,
      metadata: { userId: user.id },
    });
    customerId = customer.id;
    if (sub) {
      await db
        .update(subscriptions)
        .set({ stripeCustomerId: customerId })
        .where(eq(subscriptions.userId, user.id));
    } else {
      await db.insert(subscriptions).values({
        userId: user.id,
        stripeCustomerId: customerId,
        plan: "free",
        status: "active",
      });
    }
  }

  const session = await stripe.checkout.sessions.create({
    mode: "subscription",
    customer: customerId,
    line_items: [{ price: PRO_PRICE_ID, quantity: 1 }],
    success_url: `${env.NEXT_PUBLIC_APP_URL}/app/account?upgraded=1`,
    cancel_url: `${env.NEXT_PUBLIC_APP_URL}/app/account`,
    allow_promotion_codes: true,
    metadata: { userId: user.id },
  });

  return NextResponse.json({ url: session.url });
}

function required(name: string, value: string | undefined): string {
  if (!value || value.length === 0) {
    throw new Error(`Missing required env var: ${name}`);
  }
  return value;
}

function optional(value: string | undefined): string {
  return value ?? "";
}

export const env = {
  NEXT_PUBLIC_SUPABASE_URL: required(
    "NEXT_PUBLIC_SUPABASE_URL",
    process.env.NEXT_PUBLIC_SUPABASE_URL,
  ),
  NEXT_PUBLIC_SUPABASE_ANON_KEY: required(
    "NEXT_PUBLIC_SUPABASE_ANON_KEY",
    process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY,
  ),
  DATABASE_URL: required("DATABASE_URL", process.env.DATABASE_URL),
  ANTHROPIC_API_KEY: optional(process.env.ANTHROPIC_API_KEY),
  STRIPE_SECRET_KEY: optional(process.env.STRIPE_SECRET_KEY),
  STRIPE_WEBHOOK_SECRET: optional(process.env.STRIPE_WEBHOOK_SECRET),
  STRIPE_PRICE_PRO_MONTHLY: optional(process.env.STRIPE_PRICE_PRO_MONTHLY),
  NEXT_PUBLIC_APP_URL:
    process.env.NEXT_PUBLIC_APP_URL ?? "http://localhost:3000",
};

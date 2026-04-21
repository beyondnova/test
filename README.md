# DMCloser

AI reply assistant for WhatsApp and Instagram sellers. Paste a customer message, pick goal + tone + language, get 3 ready-to-send replies in one click.

See [docs/phase-1-mvp-spec.md](docs/phase-1-mvp-spec.md) for the full MVP spec.

## Stack

- Next.js 15 (App Router) + TypeScript
- Tailwind CSS
- Supabase (Postgres + Auth)
- Drizzle ORM
- Anthropic Claude API (`claude-haiku-4-5`) with prompt caching
- Stripe (Checkout + Customer Portal + webhooks)

## Setup

1. Copy `.env.example` to `.env.local` and fill in the values.
2. Create a Supabase project. Grab the URL, anon key, and the pooled Postgres connection string.
3. Create a Stripe product + recurring price for the Pro plan. Put the price ID in `STRIPE_PRICE_PRO_MONTHLY`.
4. Install deps and push the schema:

   ```bash
   npm install
   npm run db:push
   npm run dev
   ```

5. For Stripe webhooks in dev: `stripe listen --forward-to localhost:3000/api/billing/webhook`

## Plan limits

- Free: 15 generations/month
- Pro: 500 generations/month

Edit `src/lib/quota.ts` to change.

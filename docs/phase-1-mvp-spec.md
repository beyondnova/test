# DMCloser — Phase 1: MVP Product Spec

## 1. One-page Product Requirements Document (PRD)

**Product**: DMCloser — an AI reply assistant for WhatsApp and Instagram sellers.

**Problem**: Small sellers on WhatsApp and Instagram lose sales because they reply slowly, inconsistently, or in the wrong tone. Most are solo operators juggling dozens of chats in English, French, Arabic, and Darija.

**Solution**: Seller pastes a customer message, picks goal + tone + language, and gets 3 ready-to-send replies in one click. One-tap copy sends the reply back to WhatsApp or Instagram.

**Target users**:
- Small Instagram sellers (fashion, beauty, accessories, perfume)
- WhatsApp boutique sellers
- Primary market: Morocco and MENA

**Value proposition**:
- Reply faster (seconds instead of minutes)
- Save time (no more retyping the same answers)
- Stay consistent with store voice
- Convert more chats into sales with objection handling and closing prompts

**Core job-to-be-done**: "When a customer messages me, help me reply in the right tone and language so I close the sale without losing time."

**Success metrics (first 60 days)**:
- 500 signups
- 30% day-7 retention on generators
- >= 60% of free users who generate a reply come back next week
- 5% free-to-paid conversion
- Median time-to-first-generation < 90s after signup

**Monetization**: Freemium with monthly usage cap on free tier, paid plans for higher caps and business profile features.

**Out of scope for v1**: WhatsApp API, Instagram API, browser extensions, chatbot automation, team accounts, analytics on actual chats.

---

## 2. Prioritized MVP Scope

**P0 — must ship**
1. Email + password auth (magic link optional)
2. Dashboard with input box for customer message
3. Selectors: goal, tone, language
4. "Generate 3 replies" action calling an LLM
5. Copy-to-clipboard per reply
6. Generation history for the logged-in user
7. Business profile (single, per user): store name, category, delivery zones, payment methods, return policy, FAQ
8. Free-tier usage limit (e.g., 15 generations/month)
9. Paid subscription via Stripe (1 plan at launch: Pro)
10. Basic account/billing page

**P1 — nice to have if trivial**
- Regenerate single reply
- Edit and save a reply as a template
- Darija tone examples baked into the system prompt
- Simple admin dashboard (read-only) for the founder

**P2 — explicitly deferred**
- Teams, roles, multi-store
- Template library UI
- Analytics / reply performance
- Integrations (WhatsApp, Instagram, CRM)
- Mobile app

---

## 3. User Stories

**Auth**
- As a seller, I can sign up with email and password so I can access my workspace.
- As a returning user, I can log in and see my previous generations.

**Generation flow (core)**
- As a seller, I can paste a customer message, choose goal/tone/language, and get 3 reply options in under 10 seconds.
- As a seller, I can copy a reply with one click so I can paste it into WhatsApp or Instagram.
- As a seller, I can regenerate if none of the 3 replies fit.

**Business profile**
- As a seller, I can fill in my store name, category, delivery zones, payment methods, return policy, and common FAQ so replies match my business.
- As a seller, I can update the profile at any time and see the change reflected in the next generation.

**History**
- As a seller, I can view my last N generations with the input and the 3 outputs so I can reuse a good reply.

**Billing**
- As a free user, I see how many generations I have left this month.
- As a free user hitting the limit, I am prompted to upgrade.
- As a paid user, I can manage my subscription and cancel from the account page.

---

## 4. Recommended Tech Stack

Goal: one-language codebase, fast to ship, boring tech.

- **Frontend + backend**: Next.js 15 (App Router) with TypeScript, deployed on Vercel
- **UI**: Tailwind CSS + shadcn/ui
- **Auth**: Clerk or Supabase Auth (pick one — recommend Supabase for tighter DB integration)
- **Database**: Postgres on Supabase
- **ORM**: Prisma or Drizzle (recommend Drizzle for lighter footprint)
- **LLM**: Claude (Anthropic API) — `claude-haiku-4-5` for cost, `claude-sonnet-4-6` as fallback for quality. Multilingual performance is strong for FR/AR/Darija.
- **Payments**: Stripe (Checkout + Customer Portal + webhooks)
- **Rate limiting / quotas**: Upstash Redis or Postgres counters (Postgres is fine at MVP scale)
- **Analytics**: PostHog (self-serve, generous free tier)
- **Error tracking**: Sentry
- **Email**: Resend (transactional: welcome, receipt, limit reached)
- **Hosting**: Vercel (app) + Supabase (DB/auth/storage)

**Why**: single repo, single language (TS), no infra team, all managed services. Launchable by one engineer in 2–3 weeks.

---

## 5. Database Entities

Minimum viable schema. Timestamps omitted for brevity; all tables have `id`, `created_at`, `updated_at`.

- **users** — `id`, `email`, `auth_provider_id`, `display_name`, `locale_default`
- **business_profiles** — `id`, `user_id` (1:1), `store_name`, `category`, `delivery_zones` (text), `payment_methods` (text), `return_policy` (text), `faq` (text)
- **generations** — `id`, `user_id`, `input_message` (text), `goal` (enum), `tone` (enum), `language` (enum), `replies` (jsonb: array of 3 strings), `model`, `tokens_in`, `tokens_out`, `latency_ms`
- **subscriptions** — `id`, `user_id`, `stripe_customer_id`, `stripe_subscription_id`, `plan` (enum: free, pro), `status` (enum), `current_period_end`
- **usage_counters** — `id`, `user_id`, `period_start`, `period_end`, `generations_used` (int)

Enums:
- `goal`: answer_question, handle_objection, follow_up, close_sale, upsell
- `tone`: friendly, professional, luxury, direct
- `language`: en, fr, ar, darija
- `plan`: free, pro
- `status`: active, canceled, past_due, trialing

---

## 6. API Routes

All under `/api`, server-side in Next.js route handlers. Auth required except where noted.

- `POST /api/auth/*` — handled by Supabase/Clerk, no custom code
- `GET /api/me` — current user + plan + remaining quota
- `GET /api/profile` — fetch business profile
- `PUT /api/profile` — upsert business profile
- `POST /api/generations` — body: `{ message, goal, tone, language }`; returns 3 replies, decrements quota
- `GET /api/generations` — list user's history (paginated)
- `GET /api/generations/:id` — fetch one
- `POST /api/billing/checkout` — create Stripe Checkout session
- `POST /api/billing/portal` — Stripe Customer Portal link
- `POST /api/billing/webhook` — Stripe webhook (unauth, signature-verified)

---

## 7. Pages / Screens

Public:
- `/` — Landing page (headline, 3 feature bullets, pricing, CTA)
- `/login`, `/signup`
- `/pricing`
- `/privacy`, `/terms`

Authenticated app:
- `/app` — Dashboard: input box, selectors, "Generate" button, 3 reply cards with copy buttons, quota indicator
- `/app/history` — Paginated list of past generations with input preview
- `/app/profile` — Business profile form
- `/app/account` — Plan, upgrade/manage billing, sign out

Total: 4 app screens. Intentionally small.

---

## 8. What to Exclude from v1

Cut aggressively to ship in 2–3 weeks:
- WhatsApp Business API integration
- Instagram Graph API integration
- Browser extension or mobile app
- Auto-reply / chatbot automation
- Team accounts, multi-user workspaces, roles/permissions
- Multi-store support per account
- Template library UI (prompts only live in system prompt)
- Analytics on reply performance / A/B testing of tones
- Image/voice note input (text only at launch)
- CRM or Google Sheets export
- Custom fine-tuned models
- Mobile-native app
- Onboarding wizard beyond a single business-profile form
- Admin panel beyond a hidden read-only route

---

## Final MVP Definition

DMCloser v1 is a single-page web app where a logged-in seller fills a business profile once, then pastes customer messages and generates 3 AI replies per request in English, French, Arabic, or Darija. Free users get 15 generations/month; Pro users get 500/month for a flat fee via Stripe. No integrations, no automation, no team features. Ship in 2–3 weeks.

---

## Build Plan (in order)

**Week 1 — foundations**
1. Repo + Next.js + Tailwind + shadcn scaffold
2. Supabase project, auth, Postgres schema, Drizzle migrations
3. Landing page + signup/login
4. Dashboard shell with input + selectors (no LLM yet)

**Week 2 — core value**
5. Anthropic API integration, prompt template per goal/tone/language
6. `POST /api/generations` with business-profile injection into system prompt
7. 3-reply output UI + copy-to-clipboard
8. Generation history page
9. Business profile page

**Week 3 — monetize + polish**
10. Usage counter + quota enforcement + upgrade prompt
11. Stripe Checkout + Customer Portal + webhook
12. `/account` and `/pricing` pages
13. Sentry + PostHog wiring
14. Transactional emails (welcome, limit reached, receipt)
15. Landing-page copy pass, legal pages, OG image
16. Closed beta with 10–20 Moroccan sellers, fix feedback, launch

---

## Risk List

**Product risks**
- Darija quality: informal Moroccan Arabic varies by region; replies may sound off. Mitigation: seed prompt with 10–20 vetted Darija examples; let users edit and resubmit.
- Generic output: replies feel robotic without business context. Mitigation: force business-profile completion before first generation.
- Low willingness to pay in MENA at $10+/mo. Mitigation: price in MAD, test $5 tier, offer annual discount.

**Technical risks**
- LLM latency > 5s kills the "faster replies" pitch. Mitigation: stream output, use Haiku by default, cache business profile tokens via prompt caching.
- Cost blowout from free-tier abuse. Mitigation: hard monthly cap, rate-limit per IP + per user, require email verification.
- Multilingual safety: model may produce off-tone replies in Arabic script. Mitigation: add a light post-filter and a "report bad reply" button.

**Business risks**
- Sellers copy once, never come back. Mitigation: measure D7 retention, add "save as template" in P1 if needed.
- Competitors launching WhatsApp-integrated tools. Mitigation: v1 is a wedge; roadmap WhatsApp Business API for v2 once revenue justifies it.
- Stripe availability in Morocco: card acceptance is limited. Mitigation: start with Stripe for diaspora/EN/FR users; add local PSP (CMI, YouCan Pay) in v1.1.

**Regulatory / trust risks**
- Storing customer message text raises privacy concerns. Mitigation: clear privacy policy, retention policy (e.g., 30 days in history), allow one-click history purge.

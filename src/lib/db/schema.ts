import {
  pgTable,
  pgEnum,
  uuid,
  text,
  timestamp,
  integer,
  jsonb,
  uniqueIndex,
  index,
} from "drizzle-orm/pg-core";

export const goalEnum = pgEnum("goal", [
  "answer_question",
  "handle_objection",
  "follow_up",
  "close_sale",
  "upsell",
]);

export const toneEnum = pgEnum("tone", [
  "friendly",
  "professional",
  "luxury",
  "direct",
]);

export const languageEnum = pgEnum("language", ["en", "fr", "ar", "darija"]);

export const planEnum = pgEnum("plan", ["free", "pro"]);

export const subscriptionStatusEnum = pgEnum("subscription_status", [
  "active",
  "canceled",
  "past_due",
  "trialing",
]);

export const users = pgTable("users", {
  id: uuid("id").primaryKey(),
  email: text("email").notNull().unique(),
  authProviderId: text("auth_provider_id").notNull().unique(),
  displayName: text("display_name"),
  localeDefault: languageEnum("locale_default").notNull().default("en"),
  createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
  updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow(),
});

export const businessProfiles = pgTable(
  "business_profiles",
  {
    id: uuid("id").primaryKey().defaultRandom(),
    userId: uuid("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    storeName: text("store_name"),
    category: text("category"),
    deliveryZones: text("delivery_zones"),
    paymentMethods: text("payment_methods"),
    returnPolicy: text("return_policy"),
    faq: text("faq"),
    createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow(),
  },
  (table) => ({
    userIdx: uniqueIndex("business_profiles_user_id_idx").on(table.userId),
  }),
);

export const generations = pgTable(
  "generations",
  {
    id: uuid("id").primaryKey().defaultRandom(),
    userId: uuid("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    inputMessage: text("input_message").notNull(),
    goal: goalEnum("goal").notNull(),
    tone: toneEnum("tone").notNull(),
    language: languageEnum("language").notNull(),
    replies: jsonb("replies").$type<string[]>().notNull(),
    model: text("model").notNull(),
    tokensIn: integer("tokens_in").notNull().default(0),
    tokensOut: integer("tokens_out").notNull().default(0),
    latencyMs: integer("latency_ms").notNull().default(0),
    createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
  },
  (table) => ({
    userCreatedIdx: index("generations_user_created_idx").on(
      table.userId,
      table.createdAt,
    ),
  }),
);

export const subscriptions = pgTable(
  "subscriptions",
  {
    id: uuid("id").primaryKey().defaultRandom(),
    userId: uuid("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    stripeCustomerId: text("stripe_customer_id"),
    stripeSubscriptionId: text("stripe_subscription_id"),
    plan: planEnum("plan").notNull().default("free"),
    status: subscriptionStatusEnum("status").notNull().default("active"),
    currentPeriodEnd: timestamp("current_period_end", { withTimezone: true }),
    createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow(),
  },
  (table) => ({
    userIdx: uniqueIndex("subscriptions_user_id_idx").on(table.userId),
    stripeCustomerIdx: index("subscriptions_stripe_customer_idx").on(
      table.stripeCustomerId,
    ),
  }),
);

export const usageCounters = pgTable(
  "usage_counters",
  {
    id: uuid("id").primaryKey().defaultRandom(),
    userId: uuid("user_id")
      .notNull()
      .references(() => users.id, { onDelete: "cascade" }),
    periodStart: timestamp("period_start", { withTimezone: true }).notNull(),
    periodEnd: timestamp("period_end", { withTimezone: true }).notNull(),
    generationsUsed: integer("generations_used").notNull().default(0),
    createdAt: timestamp("created_at", { withTimezone: true }).notNull().defaultNow(),
    updatedAt: timestamp("updated_at", { withTimezone: true }).notNull().defaultNow(),
  },
  (table) => ({
    userPeriodIdx: uniqueIndex("usage_counters_user_period_idx").on(
      table.userId,
      table.periodStart,
    ),
  }),
);

export type User = typeof users.$inferSelect;
export type BusinessProfile = typeof businessProfiles.$inferSelect;
export type Generation = typeof generations.$inferSelect;
export type Subscription = typeof subscriptions.$inferSelect;
export type UsageCounter = typeof usageCounters.$inferSelect;

export type Goal = (typeof goalEnum.enumValues)[number];
export type Tone = (typeof toneEnum.enumValues)[number];
export type Language = (typeof languageEnum.enumValues)[number];
export type Plan = (typeof planEnum.enumValues)[number];

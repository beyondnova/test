import { createClient } from "@/lib/supabase/server";
import { db, users } from "@/lib/db";
import { eq } from "drizzle-orm";
import type { User } from "@/lib/db/schema";

export async function getCurrentUser(): Promise<User | null> {
  const supabase = await createClient();
  const {
    data: { user: authUser },
  } = await supabase.auth.getUser();
  if (!authUser) return null;

  const rows = await db
    .select()
    .from(users)
    .where(eq(users.id, authUser.id))
    .limit(1);
  return rows[0] ?? null;
}

export async function requireUser(): Promise<User> {
  const user = await getCurrentUser();
  if (!user) throw new Error("Unauthorized");
  return user;
}

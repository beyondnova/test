import Link from "next/link";
import { requireUser } from "@/lib/auth";
import { db, businessProfiles } from "@/lib/db";
import { eq } from "drizzle-orm";
import { Generator } from "@/components/generator";
import { getQuota } from "@/lib/quota";

export default async function DashboardPage() {
  const user = await requireUser();
  const [profile] = await db
    .select()
    .from(businessProfiles)
    .where(eq(businessProfiles.userId, user.id))
    .limit(1);

  const quota = await getQuota(user.id);
  const outOfQuota = quota.remaining === 0;

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">
          New reply
        </h1>
        <p className="mt-1 text-sm text-muted-foreground">
          Paste a customer message and get 3 ready-to-send replies.
        </p>
      </div>

      {!profile && (
        <div className="rounded-md border border-amber-300 bg-amber-50 p-4 text-sm">
          Tip: fill in your{" "}
          <Link href="/app/profile" className="underline font-medium">
            business profile
          </Link>{" "}
          to make replies match your store.
        </div>
      )}

      {outOfQuota ? (
        <div className="rounded-md border border-destructive/30 bg-destructive/5 p-4 text-sm">
          You have used all {quota.limit} generations this month.{" "}
          <Link href="/app/account" className="underline font-medium">
            Upgrade to Pro
          </Link>{" "}
          for 500/month.
        </div>
      ) : (
        <Generator defaultLanguage={user.localeDefault} />
      )}
    </div>
  );
}

import Link from "next/link";
import { redirect } from "next/navigation";
import { getCurrentUser } from "@/lib/auth";
import { getQuota } from "@/lib/quota";
import { QuotaIndicator } from "@/components/quota-indicator";
import { SignOutButton } from "@/components/sign-out-button";

export default async function AppLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const user = await getCurrentUser();
  if (!user) redirect("/login");
  const quota = await getQuota(user.id);

  return (
    <div className="min-h-screen">
      <header className="border-b">
        <div className="mx-auto flex max-w-4xl items-center justify-between px-6 py-4">
          <div className="flex items-center gap-6">
            <Link href="/app" className="font-semibold tracking-tight">
              DMCloser
            </Link>
            <nav className="flex items-center gap-4 text-sm">
              <Link href="/app" className="text-muted-foreground hover:text-foreground">
                Generate
              </Link>
              <Link
                href="/app/history"
                className="text-muted-foreground hover:text-foreground"
              >
                History
              </Link>
              <Link
                href="/app/profile"
                className="text-muted-foreground hover:text-foreground"
              >
                Profile
              </Link>
              <Link
                href="/app/account"
                className="text-muted-foreground hover:text-foreground"
              >
                Account
              </Link>
            </nav>
          </div>
          <div className="flex items-center gap-4">
            <QuotaIndicator
              used={quota.used}
              limit={quota.limit}
              plan={quota.plan}
            />
            <SignOutButton />
          </div>
        </div>
      </header>
      <main className="mx-auto max-w-4xl px-6 py-8">{children}</main>
    </div>
  );
}

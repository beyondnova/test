import { requireUser } from "@/lib/auth";
import { getQuota } from "@/lib/quota";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { BillingActions } from "./billing-actions";

export default async function AccountPage() {
  const user = await requireUser();
  const quota = await getQuota(user.id);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">Account</h1>
        <p className="mt-1 text-sm text-muted-foreground">{user.email}</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Plan</CardTitle>
        </CardHeader>
        <CardContent className="space-y-4">
          <p className="text-sm">
            <span className="font-medium capitalize">{quota.plan}</span> ·{" "}
            {quota.used} / {quota.limit} generations used this month
          </p>
          <BillingActions plan={quota.plan} />
        </CardContent>
      </Card>
    </div>
  );
}

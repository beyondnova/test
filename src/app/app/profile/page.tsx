import { requireUser } from "@/lib/auth";
import { db, businessProfiles } from "@/lib/db";
import { eq } from "drizzle-orm";
import { ProfileForm } from "./profile-form";

export default async function ProfilePage() {
  const user = await requireUser();
  const [profile] = await db
    .select()
    .from(businessProfiles)
    .where(eq(businessProfiles.userId, user.id))
    .limit(1);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">
          Business profile
        </h1>
        <p className="mt-1 text-sm text-muted-foreground">
          Fill this in once. It will be injected into every reply we generate.
        </p>
      </div>
      <ProfileForm
        initial={{
          storeName: profile?.storeName ?? "",
          category: profile?.category ?? "",
          deliveryZones: profile?.deliveryZones ?? "",
          paymentMethods: profile?.paymentMethods ?? "",
          returnPolicy: profile?.returnPolicy ?? "",
          faq: profile?.faq ?? "",
        }}
      />
    </div>
  );
}

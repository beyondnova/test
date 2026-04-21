import { NextResponse } from "next/server";
import { getCurrentUser } from "@/lib/auth";
import { getQuota } from "@/lib/quota";

export async function GET() {
  const user = await getCurrentUser();
  if (!user) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const quota = await getQuota(user.id);
  return NextResponse.json({
    user: {
      id: user.id,
      email: user.email,
      displayName: user.displayName,
      localeDefault: user.localeDefault,
    },
    quota,
  });
}

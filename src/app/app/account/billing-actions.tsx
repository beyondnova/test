"use client";

import { useState } from "react";
import { Button } from "@/components/ui/button";
import type { Plan } from "@/lib/db/schema";

export function BillingActions({ plan }: { plan: Plan }) {
  const [loading, setLoading] = useState(false);

  async function goToCheckout() {
    setLoading(true);
    const res = await fetch("/api/billing/checkout", { method: "POST" });
    const data = await res.json();
    if (data.url) window.location.href = data.url;
    else setLoading(false);
  }

  async function goToPortal() {
    setLoading(true);
    const res = await fetch("/api/billing/portal", { method: "POST" });
    const data = await res.json();
    if (data.url) window.location.href = data.url;
    else setLoading(false);
  }

  if (plan === "pro") {
    return (
      <Button onClick={goToPortal} disabled={loading}>
        {loading ? "Opening…" : "Manage subscription"}
      </Button>
    );
  }

  return (
    <Button onClick={goToCheckout} disabled={loading}>
      {loading ? "Redirecting…" : "Upgrade to Pro — $12/mo"}
    </Button>
  );
}

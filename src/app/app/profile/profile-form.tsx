"use client";

import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";

type ProfileValues = {
  storeName: string;
  category: string;
  deliveryZones: string;
  paymentMethods: string;
  returnPolicy: string;
  faq: string;
};

export function ProfileForm({ initial }: { initial: ProfileValues }) {
  const [values, setValues] = useState<ProfileValues>(initial);
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);
  const [error, setError] = useState<string | null>(null);

  function set<K extends keyof ProfileValues>(key: K, value: ProfileValues[K]) {
    setValues((v) => ({ ...v, [key]: value }));
    setSaved(false);
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setSaving(true);
    setError(null);
    try {
      const res = await fetch("/api/profile", {
        method: "PUT",
        headers: { "content-type": "application/json" },
        body: JSON.stringify(values),
      });
      if (!res.ok) {
        const data = await res.json().catch(() => ({}));
        throw new Error(data.error ?? "Save failed");
      }
      setSaved(true);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Something went wrong");
    } finally {
      setSaving(false);
    }
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-5">
      <div className="grid gap-4 sm:grid-cols-2">
        <div className="space-y-2">
          <Label htmlFor="storeName">Store name</Label>
          <Input
            id="storeName"
            value={values.storeName}
            onChange={(e) => set("storeName", e.target.value)}
            placeholder="e.g. Maison Nora"
          />
        </div>
        <div className="space-y-2">
          <Label htmlFor="category">Category</Label>
          <Input
            id="category"
            value={values.category}
            onChange={(e) => set("category", e.target.value)}
            placeholder="e.g. Fashion, beauty, perfume"
          />
        </div>
      </div>
      <div className="space-y-2">
        <Label htmlFor="deliveryZones">Delivery zones</Label>
        <Textarea
          id="deliveryZones"
          value={values.deliveryZones}
          onChange={(e) => set("deliveryZones", e.target.value)}
          placeholder="e.g. Casablanca, Rabat, Marrakech. 24–48h. Free over 500 MAD."
        />
      </div>
      <div className="space-y-2">
        <Label htmlFor="paymentMethods">Payment methods</Label>
        <Textarea
          id="paymentMethods"
          value={values.paymentMethods}
          onChange={(e) => set("paymentMethods", e.target.value)}
          placeholder="e.g. Cash on delivery, bank transfer, CMI."
        />
      </div>
      <div className="space-y-2">
        <Label htmlFor="returnPolicy">Return policy</Label>
        <Textarea
          id="returnPolicy"
          value={values.returnPolicy}
          onChange={(e) => set("returnPolicy", e.target.value)}
          placeholder="e.g. Exchange within 7 days, original packaging."
        />
      </div>
      <div className="space-y-2">
        <Label htmlFor="faq">FAQ / common answers</Label>
        <Textarea
          id="faq"
          value={values.faq}
          onChange={(e) => set("faq", e.target.value)}
          placeholder="Write down common questions and how you answer them."
          className="min-h-32"
        />
      </div>
      {error && <p className="text-sm text-destructive">{error}</p>}
      {saved && <p className="text-sm text-green-600">Saved.</p>}
      <Button type="submit" disabled={saving}>
        {saving ? "Saving…" : "Save profile"}
      </Button>
    </form>
  );
}

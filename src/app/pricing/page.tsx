import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

export default function PricingPage() {
  return (
    <main className="mx-auto max-w-4xl px-6 py-16">
      <h1 className="text-3xl font-semibold tracking-tight">Pricing</h1>
      <p className="mt-2 text-muted-foreground">
        Simple. Start free — upgrade when you need more.
      </p>
      <div className="mt-10 grid gap-6 sm:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Free</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="text-3xl font-semibold">$0</div>
            <ul className="space-y-1 text-sm text-muted-foreground">
              <li>15 generations / month</li>
              <li>All 4 languages</li>
              <li>Business profile</li>
              <li>History</li>
            </ul>
            <Link href="/signup">
              <Button variant="outline" className="w-full">
                Start free
              </Button>
            </Link>
          </CardContent>
        </Card>
        <Card className="border-primary">
          <CardHeader>
            <CardTitle>Pro</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="text-3xl font-semibold">
              $12<span className="text-base font-normal text-muted-foreground">/mo</span>
            </div>
            <ul className="space-y-1 text-sm text-muted-foreground">
              <li>500 generations / month</li>
              <li>Priority speed</li>
              <li>Everything in Free</li>
            </ul>
            <Link href="/signup">
              <Button className="w-full">Upgrade after signup</Button>
            </Link>
          </CardContent>
        </Card>
      </div>
    </main>
  );
}

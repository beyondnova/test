import Link from "next/link";
import { Button } from "@/components/ui/button";

export default function LandingPage() {
  return (
    <main className="mx-auto flex min-h-screen max-w-5xl flex-col px-6 py-10">
      <header className="flex items-center justify-between">
        <div className="text-lg font-semibold tracking-tight">DMCloser</div>
        <nav className="flex items-center gap-2 text-sm">
          <Link href="/pricing" className="text-muted-foreground hover:text-foreground">
            Pricing
          </Link>
          <Link href="/login">
            <Button variant="ghost" size="sm">
              Sign in
            </Button>
          </Link>
          <Link href="/signup">
            <Button size="sm">Get started</Button>
          </Link>
        </nav>
      </header>

      <section className="mt-20 max-w-2xl">
        <h1 className="text-4xl font-semibold tracking-tight sm:text-5xl">
          Reply to every DM in seconds.
        </h1>
        <p className="mt-4 text-lg text-muted-foreground">
          DMCloser turns any customer message into 3 ready-to-send replies — in
          English, French, Arabic, or Darija. Paste, pick the tone, copy, send.
        </p>
        <div className="mt-8 flex gap-3">
          <Link href="/signup">
            <Button size="lg">Start free</Button>
          </Link>
          <Link href="/pricing">
            <Button size="lg" variant="outline">
              See pricing
            </Button>
          </Link>
        </div>
        <p className="mt-3 text-sm text-muted-foreground">
          15 free generations/month. No credit card.
        </p>
      </section>

      <section className="mt-20 grid gap-6 sm:grid-cols-3">
        <Feature
          title="Reply in the right tone"
          body="Friendly, professional, luxury, or direct — matched to your store voice."
        />
        <Feature
          title="Built for Darija & Arabic"
          body="Model is tuned for Moroccan informal Arabic and MSA, not just English."
        />
        <Feature
          title="Your store in every reply"
          body="Fill your profile once — delivery, payment, returns — and replies stay consistent."
        />
      </section>

      <footer className="mt-auto flex flex-wrap gap-4 pt-16 text-sm text-muted-foreground">
        <Link href="/privacy">Privacy</Link>
        <Link href="/terms">Terms</Link>
        <span className="ml-auto">© {new Date().getFullYear()} DMCloser</span>
      </footer>
    </main>
  );
}

function Feature({ title, body }: { title: string; body: string }) {
  return (
    <div>
      <h3 className="font-medium">{title}</h3>
      <p className="mt-1 text-sm text-muted-foreground">{body}</p>
    </div>
  );
}

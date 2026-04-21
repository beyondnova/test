export default function PrivacyPage() {
  return (
    <main className="mx-auto max-w-3xl px-6 py-16 prose prose-sm">
      <h1>Privacy Policy</h1>
      <p>
        DMCloser stores the customer messages you paste and the replies we
        generate so you can reuse them from your history. We do not sell your
        data or share it with third parties except the providers required to
        run the service (Supabase, Anthropic, Stripe).
      </p>
      <p>
        You can delete your history at any time from your account page. When
        you delete your account, all associated data is removed within 30 days.
      </p>
      <p>Last updated: {new Date().toISOString().slice(0, 10)}</p>
    </main>
  );
}

export default function TermsPage() {
  return (
    <main className="mx-auto max-w-3xl px-6 py-16 prose prose-sm">
      <h1>Terms of Service</h1>
      <p>
        By using DMCloser, you agree to use it for legitimate customer
        communication on your own behalf or on behalf of a business you
        represent. You are responsible for the messages you send to your
        customers.
      </p>
      <p>
        We provide the service as-is. Generated replies are suggestions — you
        should review them before sending. Do not use DMCloser to send spam or
        content that violates applicable laws.
      </p>
      <p>Last updated: {new Date().toISOString().slice(0, 10)}</p>
    </main>
  );
}

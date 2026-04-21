import { AuthForm } from "@/components/auth-form";

export default function SignupPage() {
  return (
    <main className="mx-auto flex min-h-screen max-w-sm flex-col justify-center px-6 py-10">
      <h1 className="text-2xl font-semibold tracking-tight">Create account</h1>
      <p className="mt-1 text-sm text-muted-foreground">
        Start with 15 free replies/month. No credit card needed.
      </p>
      <div className="mt-8">
        <AuthForm mode="signup" />
      </div>
    </main>
  );
}

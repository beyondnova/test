export function QuotaIndicator({
  used,
  limit,
  plan,
}: {
  used: number;
  limit: number;
  plan: "free" | "pro";
}) {
  const remaining = Math.max(0, limit - used);
  return (
    <div className="flex items-center gap-2 text-sm text-muted-foreground">
      <span className="font-medium capitalize">{plan}</span>
      <span>·</span>
      <span>
        {remaining} / {limit} generations left this month
      </span>
    </div>
  );
}

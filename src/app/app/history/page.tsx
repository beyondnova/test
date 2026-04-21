import { requireUser } from "@/lib/auth";
import { db, generations } from "@/lib/db";
import { eq, desc } from "drizzle-orm";
import { Card, CardContent } from "@/components/ui/card";
import { CopyButton } from "@/components/copy-button";

const GOAL_LABEL = {
  answer_question: "Answer",
  handle_objection: "Objection",
  follow_up: "Follow-up",
  close_sale: "Close",
  upsell: "Upsell",
} as const;

export default async function HistoryPage() {
  const user = await requireUser();
  const rows = await db
    .select()
    .from(generations)
    .where(eq(generations.userId, user.id))
    .orderBy(desc(generations.createdAt))
    .limit(50);

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">History</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          Your last 50 generations.
        </p>
      </div>

      {rows.length === 0 ? (
        <p className="text-sm text-muted-foreground">
          No generations yet. Head to the dashboard to create your first reply.
        </p>
      ) : (
        <div className="space-y-4">
          {rows.map((g) => (
            <Card key={g.id}>
              <CardContent className="space-y-3 p-5">
                <div className="flex flex-wrap items-center gap-2 text-xs text-muted-foreground">
                  <span>{new Date(g.createdAt).toLocaleString()}</span>
                  <span>·</span>
                  <span>{GOAL_LABEL[g.goal]}</span>
                  <span>·</span>
                  <span className="capitalize">{g.tone}</span>
                  <span>·</span>
                  <span className="uppercase">{g.language}</span>
                </div>
                <p className="whitespace-pre-wrap text-sm text-muted-foreground">
                  <span className="font-medium text-foreground">Customer: </span>
                  {g.inputMessage}
                </p>
                <div className="space-y-2">
                  {g.replies.map((reply, i) => (
                    <div
                      key={i}
                      className="flex items-start justify-between gap-3 rounded-md border bg-muted/30 p-3"
                    >
                      <p className="whitespace-pre-wrap text-sm">{reply}</p>
                      <CopyButton text={reply} />
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      )}
    </div>
  );
}

"use client";

import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import { Select } from "@/components/ui/select";
import { Card, CardContent } from "@/components/ui/card";
import { CopyButton } from "@/components/copy-button";
import type { Goal, Tone, Language } from "@/lib/db/schema";

const GOALS: { value: Goal; label: string }[] = [
  { value: "answer_question", label: "Answer question" },
  { value: "handle_objection", label: "Handle objection" },
  { value: "follow_up", label: "Follow up" },
  { value: "close_sale", label: "Close sale" },
  { value: "upsell", label: "Upsell" },
];

const TONES: { value: Tone; label: string }[] = [
  { value: "friendly", label: "Friendly" },
  { value: "professional", label: "Professional" },
  { value: "luxury", label: "Luxury" },
  { value: "direct", label: "Direct" },
];

const LANGUAGES: { value: Language; label: string }[] = [
  { value: "en", label: "English" },
  { value: "fr", label: "Français" },
  { value: "ar", label: "العربية" },
  { value: "darija", label: "Darija (الدارجة)" },
];

export function Generator({
  defaultLanguage = "en",
  onGenerated,
}: {
  defaultLanguage?: Language;
  onGenerated?: () => void;
}) {
  const [message, setMessage] = useState("");
  const [goal, setGoal] = useState<Goal>("answer_question");
  const [tone, setTone] = useState<Tone>("friendly");
  const [language, setLanguage] = useState<Language>(defaultLanguage);
  const [replies, setReplies] = useState<string[] | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!message.trim()) return;
    setLoading(true);
    setError(null);
    setReplies(null);
    try {
      const res = await fetch("/api/generations", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify({ message, goal, tone, language }),
      });
      const data = await res.json();
      if (!res.ok) {
        throw new Error(data.error ?? "Generation failed");
      }
      setReplies(data.replies);
      onGenerated?.();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Something went wrong");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="space-y-6">
      <form onSubmit={handleSubmit} className="space-y-4">
        <div className="space-y-2">
          <Label htmlFor="message">Customer message</Label>
          <Textarea
            id="message"
            value={message}
            onChange={(e) => setMessage(e.target.value)}
            placeholder="Paste the customer's message here…"
            className="min-h-32"
            required
          />
        </div>
        <div className="grid gap-4 sm:grid-cols-3">
          <div className="space-y-2">
            <Label htmlFor="goal">Goal</Label>
            <Select
              id="goal"
              value={goal}
              onChange={(e) => setGoal(e.target.value as Goal)}
            >
              {GOALS.map((g) => (
                <option key={g.value} value={g.value}>
                  {g.label}
                </option>
              ))}
            </Select>
          </div>
          <div className="space-y-2">
            <Label htmlFor="tone">Tone</Label>
            <Select
              id="tone"
              value={tone}
              onChange={(e) => setTone(e.target.value as Tone)}
            >
              {TONES.map((t) => (
                <option key={t.value} value={t.value}>
                  {t.label}
                </option>
              ))}
            </Select>
          </div>
          <div className="space-y-2">
            <Label htmlFor="language">Language</Label>
            <Select
              id="language"
              value={language}
              onChange={(e) => setLanguage(e.target.value as Language)}
            >
              {LANGUAGES.map((l) => (
                <option key={l.value} value={l.value}>
                  {l.label}
                </option>
              ))}
            </Select>
          </div>
        </div>
        <Button type="submit" disabled={loading || !message.trim()} size="lg">
          {loading ? "Generating…" : "Generate 3 replies"}
        </Button>
        {error && <p className="text-sm text-destructive">{error}</p>}
      </form>

      {replies && (
        <div className="space-y-3">
          <h2 className="text-sm font-medium text-muted-foreground">
            Suggested replies
          </h2>
          <div className="grid gap-3">
            {replies.map((reply, i) => (
              <Card key={i}>
                <CardContent className="p-4">
                  <div className="flex items-start justify-between gap-4">
                    <p className="whitespace-pre-wrap text-sm leading-relaxed">
                      {reply}
                    </p>
                    <CopyButton text={reply} />
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}

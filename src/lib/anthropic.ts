import Anthropic from "@anthropic-ai/sdk";
import { env } from "@/lib/env";
import { buildSystemPrompt, buildUserPrompt } from "@/lib/prompts";
import type { BusinessProfile, Goal, Tone, Language } from "@/lib/db/schema";

const MODEL = "claude-haiku-4-5";

const client = new Anthropic({ apiKey: env.ANTHROPIC_API_KEY });

export type GenerateResult = {
  replies: string[];
  model: string;
  tokensIn: number;
  tokensOut: number;
  latencyMs: number;
};

const submitTool: Anthropic.Tool = {
  name: "submit_replies",
  description:
    "Submit exactly 3 reply options for the seller. Each must be the final message text, ready to send.",
  input_schema: {
    type: "object",
    properties: {
      replies: {
        type: "array",
        description: "Exactly 3 reply options, each a ready-to-send message string.",
        minItems: 3,
        maxItems: 3,
        items: { type: "string", minLength: 1 },
      },
    },
    required: ["replies"],
  },
};

export async function generateReplies(args: {
  message: string;
  goal: Goal;
  tone: Tone;
  language: Language;
  profile: BusinessProfile | null;
}): Promise<GenerateResult> {
  const started = Date.now();

  const systemPrompt = buildSystemPrompt(args.profile);
  const userPrompt = buildUserPrompt(args);

  const response = await client.messages.create({
    model: MODEL,
    max_tokens: 1024,
    system: [
      {
        type: "text",
        text: systemPrompt,
        cache_control: { type: "ephemeral" },
      },
    ],
    tools: [submitTool],
    tool_choice: { type: "tool", name: "submit_replies" },
    messages: [
      {
        role: "user",
        content: userPrompt,
      },
    ],
  });

  const toolUse = response.content.find(
    (block): block is Anthropic.ToolUseBlock => block.type === "tool_use",
  );

  if (!toolUse || toolUse.name !== "submit_replies") {
    throw new Error("Model did not return the expected tool call.");
  }

  const input = toolUse.input as { replies?: unknown };
  const replies = Array.isArray(input.replies)
    ? input.replies
        .filter((r): r is string => typeof r === "string" && r.trim().length > 0)
        .map((r) => r.trim())
    : [];

  if (replies.length !== 3) {
    throw new Error(`Expected 3 replies, got ${replies.length}.`);
  }

  return {
    replies,
    model: MODEL,
    tokensIn: response.usage.input_tokens,
    tokensOut: response.usage.output_tokens,
    latencyMs: Date.now() - started,
  };
}

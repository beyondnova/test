import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "DMCloser — Reply to every DM in seconds",
  description:
    "AI reply assistant for WhatsApp and Instagram sellers. 3 on-brand replies in one click, in English, French, Arabic, or Darija.",
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  );
}

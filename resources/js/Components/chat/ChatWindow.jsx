import { useEffect, useMemo, useRef } from "react";
import { ChatBubbleLeftRightIcon } from "@heroicons/react/24/outline";
import MessageBubble from "./MessageBubble.jsx";
import InputBar from "./InputBar.jsx";

export default function ChatWindow({
    deviceId,
    messages,
    message,
    onMessageChange,
    onSend,
    forceEmpty = false,
    disabled = false,
}) {
    const listRef = useRef(null);
    const bottomRef = useRef(null);
    const safeMessages = forceEmpty
        ? []
        : Array.isArray(messages)
        ? messages
        : [];

    useEffect(() => {
        bottomRef.current?.scrollIntoView({ behavior: "smooth" });
    }, [safeMessages]);

    const grouped = useMemo(() => {
        const byDate = {};
        for (const m of safeMessages) {
            const d = (m.date || m.created_at || "").slice(0, 10);
            byDate[d] = byDate[d] || [];
            byDate[d].push(m);
        }
        return Object.entries(byDate);
    }, [safeMessages]);

    return (
        <div className="grow w-full flex flex-col">
            <div ref={listRef} className="flex-1 px-2 py-4 overflow-y-auto">
                {grouped.length === 0 && (
                    <div className="flex items-center justify-center h-full">
                        <div className="text-center p-6 rounded-lg">
                            <ChatBubbleLeftRightIcon className="size-6 opacity-60 mx-auto" />
                            <div className="mt-2 text-sm font-medium">
                                Belum ada percakapan
                            </div>
                            <div className="text-xs opacity-60">
                                Mulai percakapan
                            </div>
                        </div>
                    </div>
                )}
                {grouped.map(([date, items]) => (
                    <div key={date}>
                        <div className="flex justify-center mt-4 mb-2">
                            <div className="badge bg-white/90 text-medium">
                                {date}
                            </div>
                        </div>
                        <div className="space-y-2">
                            {items.map((m) => {
                                return (
                                    <MessageBubble
                                        key={m.id}
                                        isMine={m.type === "user"}
                                        body={m.body}
                                    />
                                );
                            })}
                        </div>
                    </div>
                ))}
                <div ref={bottomRef} />
            </div>
            {disabled && (
                <div className="px-4 pb-1 text-xs opacity-70 text-base-content text-center">
                    Tunggu jawaban asisten sebelum mengirim pesan berikutnya
                </div>
            )}
            <InputBar
                message={message}
                onChange={onMessageChange}
                onSend={onSend}
                disabled={disabled}
            />
        </div>
    );
}

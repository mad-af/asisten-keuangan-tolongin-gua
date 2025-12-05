import { useEffect, useMemo, useRef } from "react";
import MessageBubble from "./MessageBubble.jsx";
import InputBar from "./InputBar.jsx";

export default function ChatWindow({
    deviceId,
    messages,
    message,
    onMessageChange,
    onSend,
}) {
    const listRef = useRef(null);

    useEffect(() => {
        if (listRef.current) {
            listRef.current.scrollTop = listRef.current.scrollHeight;
        }
    }, [messages]);

    const grouped = useMemo(() => {
        const byDate = {};
        for (const m of messages) {
            const d = (m.date || m.created_at || "").slice(0, 10);
            byDate[d] = byDate[d] || [];
            byDate[d].push(m);
        }
        return Object.entries(byDate);
    }, [messages]);

    return (
        <div className="h-full w-full flex flex-col">
            <div
                ref={listRef}
                className="flex-1 max-h-11/12 min-h-11/12 overflow-y-auto px-2 py-4 bg-base-100"
            >
                {grouped.length === 0 && (
                    <div className="flex items-center justify-center h-full opacity-60 text-sm">
                        Mulai percakapan
                    </div>
                )}
                {grouped.map(([date, items]) => (
                    <div key={date}>
                        <div className="flex justify-center mb-2">
                            <div className="badge badge-neutral badge-outline">
                                {date}
                            </div>
                        </div>
                        <div className="space-y-2">
                            {items.map((m) => (
                                <MessageBubble
                                    key={m.id}
                                    isMine={m.from === deviceId}
                                    body={m.body}
                                />
                            ))}
                        </div>
                    </div>
                ))}
            </div>
            <InputBar
                message={message}
                onChange={onMessageChange}
                onSend={onSend}
            />
        </div>
    );
}

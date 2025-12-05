import React, { useEffect, useMemo, useRef, useState } from "react";
import AppLayout from "../../Layouts/AppLayout.jsx";
import { v4 as uuidv4 } from "uuid";
import {
    PaperClipIcon,
    MicrophoneIcon,
    FaceSmileIcon,
} from "@heroicons/react/24/outline";
import { router } from "@inertiajs/react";

const Index = () => {
    const [deviceId, setDeviceId] = useState("");
    const [message, setMessage] = useState("");
    const [messages, setMessages] = useState([]);
    const listRef = useRef(null);

    useEffect(() => {
        const existing =
            typeof window !== "undefined"
                ? localStorage.getItem("device_id")
                : null;
        if (existing) {
            setDeviceId(existing);
        } else {
            const id = uuidv4();
            localStorage.setItem("device_id", id);
            setDeviceId(id);
        }
    }, []);

    useEffect(() => {
        if (!deviceId) return;
        fetch(`/messages/${deviceId}`)
            .then((r) => r.json())
            .then((data) => {
                if (Array.isArray(data)) setMessages(data);
            })
            .catch(() => {});
    }, [deviceId]);

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

    const onSend = () => {
        const text = message.trim();
        if (!text) return;
        const optimistic = {
            id: `local-${Date.now()}`,
            from: deviceId,
            to: "agent",
            body: text,
            created_at: new Date().toISOString(),
        };
        setMessages((prev) => [...prev, optimistic]);
        setMessage("");
        router.post(
            "/chat/send",
            { message: text, device_id: deviceId },
            { preserveScroll: true }
        );
    };

    return (
        <div className="relative h-full">
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
                            {items.map((m) => {
                                const isMine = m.from === deviceId;
                                return (
                                    <div
                                        key={m.id}
                                        className={`chat ${
                                            isMine ? "chat-end" : "chat-start"
                                        }`}
                                    >
                                        <div className="chat-bubble whitespace-pre-line">
                                            {m.body}
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                ))}
            </div>
            <div className="w-full bg-base-100 absolute bottom-0 border-t border-base-300">
                <div className="flex items-center gap-2 p-2">
                    <button className="btn btn-ghost btn-square">
                        <FaceSmileIcon className="size-5" />
                    </button>
                    <button className="btn btn-ghost btn-square">
                        <PaperClipIcon className="size-5" />
                    </button>
                    <input
                        value={message}
                        onChange={(e) => setMessage(e.target.value)}
                        onKeyDown={(e) => {
                            if (e.key === "Enter" && !e.shiftKey) {
                                e.preventDefault();
                                onSend();
                            }
                        }}
                        type="text"
                        placeholder="Ketik pesan"
                        className="input input-bordered w-full"
                    />
                    <button onClick={onSend} className="btn btn-primary">
                        <MicrophoneIcon className="size-5" />
                    </button>
                </div>
            </div>
        </div>
        </div>
    );
};

Index.layout = (page) => <AppLayout>{page}</AppLayout>;

export default Index;

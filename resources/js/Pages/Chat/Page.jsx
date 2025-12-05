import React, { useEffect, useMemo, useRef, useState } from "react";
import AppLayout from "../../Layouts/AppLayout.jsx";
import { v4 as uuidv4 } from "uuid";
import { router } from "@inertiajs/react";
import ChatWindow from "../../Components/chat/ChatWindow.jsx";

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
        <div
            className="relative h-full"
            style={{
                backgroundImage: "url(/assets/chat/whatsapp-default-bg.webp)",
                backgroundRepeat: "repeat",
                backgroundPosition: "top left",
            }}
        >
            <ChatWindow
                deviceId={deviceId}
                messages={messages}
                message={message}
                onMessageChange={setMessage}
                onSend={onSend}
            />
        </div>
    );
};

Index.layout = (page) => <AppLayout>{page}</AppLayout>;

export default Index;

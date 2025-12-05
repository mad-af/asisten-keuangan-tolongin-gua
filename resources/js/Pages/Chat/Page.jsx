import React, { useEffect, useRef, useState } from "react";
import AppLayout from "../../Layouts/AppLayout.jsx";
import { router, usePage } from "@inertiajs/react";
import ChatWindow from "../../Components/chat/ChatWindow.jsx";
import { useChatApi } from "../../Hooks/useChatApi.jsx";

const Index = () => {
    const { user } = usePage().props ?? {};
    const userId = user?.id ?? null;
    const [message, setMessage] = useState("");
    const { messages, send, fetchMessages } = useChatApi(userId);

    useEffect(() => {
        fetchMessages();
    }, []);

    const onSend = () => {
        const text = message.trim();
        if (!text) return;
        setMessage("");
        send(text);
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
                deviceId={userId}
                messages={messages}
                message={message}
                onMessageChange={setMessage}
                onSend={onSend}
                forceEmpty={false}
            />
        </div>
    );
};

Index.layout = (page) => <AppLayout>{page}</AppLayout>;

export default Index;

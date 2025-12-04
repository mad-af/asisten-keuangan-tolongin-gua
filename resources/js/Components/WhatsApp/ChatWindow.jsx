import { useEffect, useRef, useState } from "react";
import MessageBubble from "./MessageBubble";
import { router, usePage } from "@inertiajs/react";

export default function ChatWindow() {
    const { props } = usePage();
    const device = props.device;
    const initialMessages = props.messages || [];

    const [messages, setMessages] = useState(initialMessages);
    const [message, setMessage] = useState("");

    const bottomRef = useRef(null);

    // Auto update messages kalau server kirim state baru
    useEffect(() => {
        if (initialMessages.length > 0) {
            setMessages(initialMessages);
        }
    }, [initialMessages]);

    // Auto scroll ke bawah
    useEffect(() => {
        bottomRef.current?.scrollIntoView({ behavior: "smooth" });
    }, [messages]);

    const sendMessage = () => {
        if (!message.trim()) return;

        router.post(
            "chat/send",
            {
                device_id: device?.id ?? "demo-device",
                message: message,
            },
            {
                preserveScroll: true,
                preserveState: true,
                onSuccess: (e) => {
                    setMessage(""); // clear input
                    console.log(e);
                },
            }
        );
    };

    return (
        <div className="flex-1 h-full flex flex-col bg-base-100">

            {/* Header */}
            <div className="p-4 bg-base-300 border-b border-base-300 flex items-center gap-3">
                <div className="avatar">
                    <div className="w-10 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                        <img src="https://ui-avatars.com/api/?name=AsistenKeuangan" alt="avatar" />
                    </div>
                </div>
                <h2 className="font-semibold text-lg">Asisten Keuangan Tolongin Gue</h2>
            </div>

            {/* Messages */}
            <div className="flex-1 overflow-y-auto p-4 space-y-3">
                {messages.map((msg) => (
                    <MessageBubble key={msg.id} msg={msg} />
                ))}

                {/* dummy div for auto-scroll */}
                <div ref={bottomRef}></div>
            </div>

            {/* Input */}
            <div className="p-4 bg-base-200 flex gap-3 border-t border-base-300">
                <input
                    type="text"
                    className="input input-bordered w-full"
                    placeholder="Ketik pesan..."
                    value={message}
                    onChange={(e) => setMessage(e.target.value)}
                    onKeyDown={(e) => e.key === "Enter" && sendMessage()}
                />
                <button className="btn btn-primary px-6" onClick={sendMessage}>
                    Send
                </button>
            </div>
        </div>
    );
}

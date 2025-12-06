import { useEffect, useRef, useState } from "react";
import MessageBubble from "./MessageBubble";
import { usePage, useForm } from "@inertiajs/react";
import { useChat } from "../../Contexts/ChatContexts";
import useFetch from "../../Hooks/useFetch";

export default function ChatWindow() {
    const { props } = usePage();
    const { deviceId } = useChat();    

    const initialMessages = props.messages || [];

    // Polling message (fetch tiap 5 detik)
    const { data: messageData } = useFetch(
        !!deviceId,
        `/messages/${deviceId}`,
        {},
        5000
    );

    const [messages, setMessages] = useState(initialMessages);
    const bottomRef = useRef(null);

    // 🔥 useForm Inertia
    const { data, setData, post, processing, reset } = useForm({
        message: "",
        device_id: deviceId,
    });

    useEffect(() => {
        if (deviceId) {
            setData("device_id", deviceId);
        }
    }, [deviceId]);

    // Update messages dari polling
    useEffect(() => {
        if (Array.isArray(messageData) && messageData.length > 0) {
            setMessages(messageData);
        }
    }, [messageData]);

    // Auto scroll ke paling bawah tiap messages berubah
    useEffect(() => {
        bottomRef.current?.scrollIntoView({ behavior: "smooth" });
    }, [messages]);

    // Kirim pesan (pakai Inertia)
    const sendMessage = () => {
        if (!data.message.trim()) return;

        post("/chat/send", {
            preserveScroll: true,
            onSuccess: (res) => {
                // `res.props.messages` akan ter-update jika controller return via Inertia
                if (Array.isArray(res.props?.messages)) {
                    setMessages(res.props.messages);
                }

                reset("message"); // reset input
            },
        });
    };

    return (
        <div className="flex-1 h-full flex flex-col bg-base-100">

            {/* Header */}
            <div className="p-4 bg-base-300 border-b border-base-300 flex items-center gap-3">
                <div className="avatar">
                    <div className="w-10 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                        <img
                            src="https://ui-avatars.com/api/?name=Asisten+Keuangan"
                            alt="avatar"
                        />
                    </div>
                </div>
                <h2 className="font-semibold text-lg">Asisten Keuangan</h2>
            </div>

            {/* Messages */}
            <div className="flex-1 p-4 space-y-3">
                {messages.length === 0 ? (
                    <p className="text-center text-sm opacity-50">
                        Belum ada pesan...
                    </p>
                ) : (
                    messages.map((msg) => (
                        <MessageBubble key={msg.id} msg={msg} />
                    ))
                )}

                <div ref={bottomRef}></div>
            </div>

            {/* Input */}
            <div className="p-4 bg-base-200 flex gap-3 border-t border-base-300">
                <input
                    type="text"
                    className="input input-bordered w-full"
                    placeholder={processing ? "Mengirim..." : "Ketik pesan..."}
                    value={data.message}
                    onChange={(e) => setData("message", e.target.value)}
                    onKeyDown={(e) => e.key === "Enter" && sendMessage()}
                    disabled={processing}
                />
                <button
                    className="btn btn-primary px-6"
                    onClick={sendMessage}
                    disabled={processing}
                >
                    {processing ? "..." : "Send"}
                </button>
            </div>

        </div>
    );
}

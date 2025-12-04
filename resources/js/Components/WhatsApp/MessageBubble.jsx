import { useChat } from "../../Contexts/ChatContexts";

export default function MessageBubble({ msg }) {
    const { deviceId } = useChat();
    const isMe = msg.from === deviceId;

    return (
        <div className={`flex w-full ${isMe ? "justify-end" : "justify-start"}`}>
            <div
                className={`p-3 max-w-xs rounded-xl text-sm ${
                    isMe
                        ? "bg-primary text-primary-content rounded-br-none"
                        : "bg-base-300 rounded-bl-none"
                }`}
            >
                {msg.body}
            </div>
        </div>
    );
}

export default function MessageBubble({ isMine, body }) {
  return (
    <div className={`chat ${isMine ? "chat-end" : "chat-start"}`}>
      <div className="chat-bubble whitespace-pre-line">{body}</div>
    </div>
  );
}


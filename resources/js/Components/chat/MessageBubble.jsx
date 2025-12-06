import Markdown from 'react-markdown'

export default function MessageBubble({ isMine, body }) {
  return (
    <div className={`chat ${isMine ? "chat-end" : "chat-start"}`}>
      <div className={`chat-bubble markdown-body ${isMine ? "bg-[#d9fdd4]" : "bg-white"}`}>
        <Markdown>{body}</Markdown>
      </div>
    </div>
  );
}


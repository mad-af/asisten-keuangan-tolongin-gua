import { FaceSmileIcon, PaperClipIcon, MicrophoneIcon } from "@heroicons/react/24/outline";

export default function InputBar({ message, onChange, onSend }) {
  return (
    <div className="w-full absolute bottom-0">
      <div className="flex items-center gap-2 p-2">
        <button className="btn btn-ghost btn-square">
          <FaceSmileIcon className="size-5" />
        </button>
        <button className="btn btn-ghost btn-square">
          <PaperClipIcon className="size-5" />
        </button>
        <input
          value={message}
          onChange={(e) => onChange(e.target.value)}
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
  );
}


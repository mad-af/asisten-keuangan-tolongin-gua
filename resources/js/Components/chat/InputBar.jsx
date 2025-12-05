import {
    FaceSmileIcon,
    PaperClipIcon,
    MicrophoneIcon,
} from "@heroicons/react/24/outline";
import { PaperAirplaneIcon } from "@heroicons/react/24/solid";

export default function InputBar({ message, onChange, onSend }) {
    return (
        <div className="w-full absolute bottom-0">
            <div className="p-2">
                <div className="flex items-center gap-2 w-full bg-white rounded-full p-1 shadow-sm">
                    <button className="btn btn-ghost btn-circle btn-md w-10 h-10">
                        <FaceSmileIcon className="size-5" />
                    </button>
                    <button className="btn btn-ghost btn-circle btn-md w-10 h-10">
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
                        className="w-full bg-transparent focus:outline-none"
                    />
                    <button
                        onClick={onSend}
                        className="btn bg-[#1a9857] text-[#fdfdfd] btn-circle btn-md w-10 h-10"
                    >
                        <PaperAirplaneIcon className="size-5" />
                    </button>
                </div>
            </div>
        </div>
    );
}

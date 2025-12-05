import React, { useEffect, useRef } from "react";
import { FaceSmileIcon, PaperClipIcon } from "@heroicons/react/24/outline";
import { PaperAirplaneIcon } from "@heroicons/react/24/solid";

export default function InputBar({
    message,
    onChange,
    onSend,
    disabled = false,
}) {
    const areaRef = useRef(null);
    useEffect(() => {
        const el = areaRef.current;
        if (!el) return;
        el.style.height = "auto";
        const max = 140;
        const h = Math.min(el.scrollHeight, max);
        el.style.height = `${h}px`;
        el.style.overflowY = el.scrollHeight > max ? "auto" : "hidden";
    }, [message]);
    return (
        <div className="w-full">
            <div className="p-2 pt-0">
                <div className="flex items-end gap-1 w-full bg-white rounded-3xl p-1 shadow-sm">
                    <button
                        className="btn btn-ghost btn-circle btn-md w-10 h-10"
                        disabled
                    >
                        <FaceSmileIcon className="size-5" />
                    </button>
                    <button
                        className="btn btn-ghost btn-circle btn-md w-10 h-10"
                        disabled
                    >
                        <PaperClipIcon className="size-5" />
                    </button>
                    <textarea
                        ref={areaRef}
                        value={message}
                        onChange={(e) => !disabled && onChange(e.target.value)}
                        onKeyDown={(e) => {
                            if (disabled) return;
                            if (e.key === "Enter" && !e.shiftKey) {
                                e.preventDefault();
                                onSend();
                            }
                        }}
                        rows={1}
                        placeholder="Ketik pesan..."
                        className={`w-full self-center bg-transparent focus:outline-none resize-none ${
                            disabled ? "opacity-60 cursor-not-allowed" : ""
                        }`}
                        style={{ maxHeight: 140 }}
                        disabled={disabled}
                    />
                    <button
                        onClick={() => {
                            if (!disabled) onSend();
                        }}
                        className={`btn btn-circle btn-md w-10 h-10 ${
                            disabled
                                ? "bg-[#cfd8d3] text-[#fdfdfd] opacity-60 cursor-not-allowed"
                                : "bg-[#1a9857] text-[#fdfdfd]"
                        }`}
                        disabled={disabled}
                    >
                        <PaperAirplaneIcon className="size-5" />
                    </button>
                </div>
            </div>
        </div>
    );
}

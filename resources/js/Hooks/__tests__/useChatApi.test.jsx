import { renderHook, waitFor } from "@testing-library/react";
import { useChatApi } from "../useChatApi.jsx";

const store = {};

test("send appends optimistic", async () => {
    const axios = (await import("axios")).default;
    axios.get = async (url) => {
        if (url.includes("/api/messages/") && url.endsWith("/latest")) {
            return { data: store.latest ?? [] };
        }
        if (url.includes("/api/messages/")) {
            return { data: store.list ?? [] };
        }
        if (url.includes("/api/users/me")) {
            return { data: { id: "u1" } };
        }
        return { data: [] };
    };
    axios.post = async (url, body) => {
        if (url.includes("/api/chat/send")) {
            store.latest = {
                id: "m2",
                body: "ok",
                type: "assistant",
                created_at: new Date().toISOString(),
            };
            return { data: { ok: true } };
        }
        if (url.includes("/api/messages/") && url.endsWith("/fallback")) {
            return {
                data: {
                    id: "fb1",
                    body: "fallback",
                    type: "assistant",
                    created_at: new Date().toISOString(),
                },
            };
        }
        return { data: {} };
    };
    store.list = [
        {
            id: "m1",
            body: "hai",
            type: "user",
            created_at: new Date().toISOString(),
        },
    ];
    const { result } = renderHook(() => useChatApi("u1"));
    await result.current.send("halo");
    await waitFor(() => expect(result.current.messages.length).toBe(1));
});

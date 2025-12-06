import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import axios from "axios";

export function useChatApi(userId) {
    const [messages, setMessages] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("");
    const [uid, setUid] = useState(userId || null);
    const [polling, setPolling] = useState(false);

    const fetchMessages = useCallback(async () => {
        const targetId = uid || userId;
        if (!targetId) return;
        setLoading(true);
        setError("");
        try {
            const res = await axios.get(`/api/messages/${targetId}`, {
                withCredentials: true,
            });
            const data = Array.isArray(res?.data) ? res.data : [];
            setMessages(data);
        } catch (e) {
            setError("Gagal memuat pesan");
        } finally {
            setLoading(false);
        }
    }, [uid, userId]);

    useEffect(() => {
        if (!uid && !userId) {
            axios
                .get("/api/users/me", { withCredentials: true })
                .then((res) => {
                    const me = res?.data;
                    if (me?.id) setUid(me.id);
                })
                .catch(() => {});
        }
        fetchMessages();
    }, [fetchMessages, uid, userId]);

    const send = useCallback(
        async (text) => {
            const body = (text || "").trim();
            const targetId = uid || userId;
            if (!body || !targetId) return;
            const optimistic = {
                id: `local-${Date.now()}`,
                body,
                type: "user",
                created_at: new Date().toISOString(),
            };
            setMessages((prev) => [...prev, optimistic]);
            try {
                await axios.post(
                    "/api/chat/send",
                    { message: body, user_id: targetId },
                    { withCredentials: true, timeout: 60000 }
                );
            } catch (e) {}
        },
        [uid, userId]
    );

    const isBlocked = useMemo(() => {
        const last =
            messages && messages.length > 0
                ? messages[messages.length - 1]
                : null;
        if (!last) return false;
        if (last.type) return last.type === "user";
        const id = uid || userId;
        return last.from === id;
    }, [messages, uid, userId]);

    useEffect(() => {
        const targetId = uid || userId;
        if (!targetId) return;
        if (!isBlocked || polling) return;
        const attemptsRef = { current: 0 };
        const cancelRef = { current: false };
        const run = async () => {
            setPolling(true);
            try {
                const lastId =
                    messages && messages.length > 0
                        ? messages[messages.length - 1]?.id
                        : null;
                while (!cancelRef.current && attemptsRef.current < 30) {
                    const res = await axios.get(
                        `/api/messages/${targetId}/latest`,
                        { withCredentials: true }
                    );
                    const data = res?.data;
                    const empty = Array.isArray(data) && data.length === 0;
                    if (!empty) {
                        if (data?.id !== lastId && data?.type === "assistant") {
                            setMessages((prev) => [...prev, data]);
                            break;
                        }
                    }
                    attemptsRef.current++;
                    console.log(attemptsRef.current);
                    if (attemptsRef.current >= 3) {
                        try {
                            const fb = await axios.post(
                                `/api/messages/${targetId}/fallback`,
                                {},
                                { withCredentials: true }
                            );
                            const created = fb?.data;
                            if (created?.id) {
                                setMessages((prev) => [...prev, created]);
                                break;
                            }
                        } catch {}
                    }
                    await new Promise((r) => setTimeout(r, 5000));
                }
            } finally {
                setPolling(false);
            }
        };
        run();
        return () => {
            cancelRef.current = true;
        };
    }, [isBlocked, uid, userId]);

    return { messages, loading, error, fetchMessages, send, isBlocked };
}

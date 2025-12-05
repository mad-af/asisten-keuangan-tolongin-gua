import { useCallback, useEffect, useMemo, useState } from "react";
import axios from "axios";

export function useChatApi(userId) {
  const [messages, setMessages] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [uid, setUid] = useState(userId || null);

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
          { withCredentials: true }
        );
        await fetchMessages();
      } catch (e) {}
    },
    [uid, userId, fetchMessages]
  );

  const isBlocked = useMemo(() => {
    const last = messages && messages.length > 0 ? messages[messages.length - 1] : null;
    if (!last) return false;
    if (last.type) return last.type === "user";
    const id = uid || userId;
    return last.from === id;
  }, [messages, uid, userId]);

  return { messages, loading, error, fetchMessages, send, isBlocked };
}

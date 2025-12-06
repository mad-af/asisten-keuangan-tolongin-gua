import { useCallback, useEffect, useState } from "react";
import axios from "axios";

export function useUserApi({ onRegisterSuccess } = {}) {
    const [name, setName] = useState("");
    const [error, setError] = useState("");
    const [loadingEnter, setLoadingEnter] = useState(false);
    const [me, setMe] = useState(null);
    const [loadingMe, setLoadingMe] = useState(false);

    useEffect(() => {
        try {
            const existing = localStorage.getItem("user_name");
            if (existing && !name) setName(existing);
        } catch {}
    }, []);

    const register = useCallback(async () => {
        const trimmed = name.trim();
        if (!trimmed) {
            setError("Nama tidak boleh kosong");
            return;
        }
        setError("");
        setLoadingEnter(true);
        try {
            const res = await axios.post(
                "/api/users/register",
                { name: trimmed },
                { withCredentials: true }
            );
            const data = res?.data ?? {};
            try {
                localStorage.setItem("user_name", data?.name ?? trimmed);
                localStorage.setItem("setup_type", data?.setup_type ?? "");
                if (data?.token) {
                    localStorage.setItem("user_token", data.token);
                }
            } catch {}
            onRegisterSuccess?.(data);
        } catch (e) {
            setError("Gagal mendaftar pengguna");
        } finally {
            setLoadingEnter(false);
        }
    }, [name, onRegisterSuccess]);

    const fetchMe = useCallback(async () => {
        setLoadingMe(true);
        try {
            const res = await axios.get("/api/users/me", {
                withCredentials: true,
            });
            const data = res?.data ?? null;
            setMe(data);
            try {
                if (data) {
                    localStorage.setItem("user_name", data.name ?? "");
                    localStorage.setItem("setup_type", data.setup_type ?? "");
                }
            } catch {}
        } catch (e) {
            setMe(null);
        } finally {
            setLoadingMe(false);
        }
    }, []);

    const setup = useCallback(async (setupType) => {
        try {
          console.log(localStorage.getItem("user_token"))
            const res = await axios.post(
                "/api/users/setup",
                {
                    setup_type: setupType,
                    token:
                        (typeof localStorage !== "undefined" &&
                            localStorage.getItem("user_token")) ||
                        undefined,
                },
                { withCredentials: true }
            );
            const data = res?.data ?? {};
            try {
                localStorage.setItem(
                    "setup_type",
                    data?.setup_type ?? setupType
                );
                if (data?.name) localStorage.setItem("user_name", data.name);
            } catch {}
        } catch {}
    }, []);

    return {
        name,
        setName,
        error,
        loadingEnter,
        register,
        me,
        loadingMe,
        fetchMe,
        setup,
    };
}

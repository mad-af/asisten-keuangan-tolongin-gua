import { useCallback, useEffect, useState } from "react";
import axios from "axios";
import { getOrCreateDeviceId } from "../utils/device.js";

export function useRegisterDevice({ onSuccess } = {}) {
    const [name, setName] = useState("");
    const [loadingNew, setLoadingNew] = useState(false);
    const [loadingDummy, setLoadingDummy] = useState(false);
    const [error, setError] = useState("");
    const [loadingEnter, setLoadingEnter] = useState(false);

    useEffect(() => {
        try {
            const existing = localStorage.getItem("device_name");
            if (existing && !name) setName(existing);
        } catch {}
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const saveDeviceLocal = (device) => {
        try {
            if (device?.device_id)
                localStorage.setItem("device_id", device.device_id);
            if (device?.device_name)
                localStorage.setItem("device_name", device.device_name);
            if (device?.token)
                localStorage.setItem("device_token", device.token);
        } catch {}
    };

    const setTokenCookie = (token) => {
        if (!token) return;
        try {
            document.cookie = `device_token=${encodeURIComponent(
                token
            )}; path=/; SameSite=Lax`;
        } catch {}
    };

    const handleNewSetup = useCallback(async () => {
        const trimmed = name.trim();
        if (!trimmed) {
            setError("Nama tidak boleh kosong");
            return;
        }
        setError("");
        setLoadingNew(true);
        const device_id = getOrCreateDeviceId();
        try {
            const res = await axios.post(
                "/api/devices/register",
                { device_id, device_name: trimmed },
                { withCredentials: true }
            );
            const data = res?.data ?? {};
            const device = {
                device_id: data?.device_id ?? device_id,
                device_name: trimmed,
                token: data?.token ?? null,
            };
            saveDeviceLocal(device);
            if (device.token) setTokenCookie(device.token);
            onSuccess?.(device);
        } catch (e) {
            setError("Gagal mendaftar perangkat");
        } finally {
            setLoadingNew(false);
        }
    }, [name, onSuccess]);

    const handleDummySetup = useCallback(async () => {
        setError("");
        setLoadingDummy(true);
        try {
            const res = await axios.post(
                "/api/devices/dummy",
                {},
                { withCredentials: true }
            );
            const data = res?.data ?? {};
            const device = {
                device_id: data?.device_id ?? "dummy-device",
                device_name: data?.device_name ?? "Dummy User",
                token: data?.token ?? "dummy-token",
            };
            saveDeviceLocal(device);
            setTokenCookie(device.token);
            onSuccess?.(device);
        } catch (e) {
            setError("Gagal menggunakan dummy setup");
        } finally {
            setLoadingDummy(false);
        }
    }, [onSuccess]);

    const handleEnter = useCallback(() => {
        const trimmed = name.trim();
        if (!trimmed) {
            setError("Nama tidak boleh kosong");
            return;
        }
        setError("");
        setLoadingEnter(true);
        try {
            localStorage.setItem("device_name", trimmed);
            onSuccess?.({ device_name: trimmed });
        } finally {
            setLoadingEnter(false);
        }
    }, [name, onSuccess]);

    return {
        name,
        setName,
        loadingNew,
        loadingDummy,
        loading: loadingNew || loadingDummy || loadingEnter,
        error,
        handleNewSetup,
        handleDummySetup,
        handleEnter,
    };
}

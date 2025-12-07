import { renderHook, waitFor } from "@testing-library/react";
import axios from "axios";
import { useRegisterDevice } from "../useRegisterDevice.jsx";

// shim storage & cookie
globalThis.localStorage = {
    _s: {},
    getItem(k) {
        return this._s[k] ?? null;
    },
    setItem(k, v) {
        this._s[k] = String(v);
    },
    removeItem(k) {
        delete this._s[k];
    },
};
Object.defineProperty(globalThis, "document", { value: { cookie: "" } });

axios.post = async (url, body) => {
    if (url.includes("/api/devices/register")) {
        return {
            data: {
                device_id: body.device_id,
                token: "tok",
                device_name: body.device_name,
            },
        };
    }
    if (url.includes("/api/devices/dummy")) {
        return {
            data: {
                device_id: "dummy-device",
                token: "dummy-token",
                device_name: "Dummy User",
            },
        };
    }
    return { data: {} };
};

test("handleNewSetup stores device and calls onSuccess", async () => {
    const calls = [];
    const onSuccess = (d) => calls.push(d);
    const { result } = renderHook(() => useRegisterDevice({ onSuccess }));
    result.current.setName("Budi");
    await result.current.handleNewSetup();
    expect(localStorage.getItem("device_name")).toBe("Budi");
    expect(localStorage.getItem("device_token")).toBe("tok");
    expect(calls.length).toBe(1);
});

test("handleDummySetup stores dummy device", async () => {
    const { result } = renderHook(() => useRegisterDevice());
    await result.current.handleDummySetup();
    expect(localStorage.getItem("device_name")).toBe("Dummy User");
    expect(localStorage.getItem("device_token")).toBe("dummy-token");
});

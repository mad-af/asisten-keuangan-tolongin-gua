import { renderHook, waitFor } from "@testing-library/react";
// axios will be imported and mocked inside the test
import { useUserApi } from "../useUserApi.jsx";

test("register and setup update user state", async () => {
    const axios = (await import("axios")).default;
    axios.get = async (url) => ({ data: {} });
    axios.post = async (url, body) => {
        if (url.includes("/api/users/register")) {
            return {
                data: {
                    id: "u1",
                    name: body.name,
                    setup_type: null,
                    token: "t1",
                },
            };
        }
        if (url.includes("/api/users/setup")) {
            return {
                data: { id: "u1", name: "Budi", setup_type: body.setup_type },
            };
        }
        return { data: {} };
    };
    const { result } = renderHook(() => useUserApi());
    await result.current.register("Budi");
    await waitFor(() => expect(localStorage.getItem("user_token")).toBe("t1"));
    await result.current.setup("dummy");
    await waitFor(() =>
        expect(localStorage.getItem("setup_type")).toBe("dummy")
    );
});

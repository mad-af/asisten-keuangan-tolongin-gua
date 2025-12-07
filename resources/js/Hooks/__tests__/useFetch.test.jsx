import { renderHook, waitFor } from "@testing-library/react";
// axios will be imported and mocked inside the test
import useFetch from "../useFetch.jsx";

test("initial fetch loads data", async () => {
    const axios = (await import("axios")).default;
    axios.get = async () => ({ data: [{ id: 1 }] });
    const { result } = renderHook(() =>
        useFetch(true, "/api/transactions", { withCredentials: true }, 5000)
    );
    await waitFor(() => expect(Array.isArray(result.current.data)).toBe(true));
    await waitFor(() => expect(result.current.data.length).toBe(1));
    expect(result.current.error).toBe(null);
});

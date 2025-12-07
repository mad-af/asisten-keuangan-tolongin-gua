import { renderHook } from "@testing-library/react";
// axios will be imported and mocked inside the test
import { useTransactionsApi } from "../useTransactionsApi.jsx";

test("fetchAll loads transactions, cashflow, and stats", async () => {
    const axios = (await import("axios")).default;
    axios.get = async (url) => {
        if (url.includes("/api/transactions/stats-month")) {
            return {
                data: {
                    month: "2025-01",
                    in_total: 10,
                    out_total: 6,
                    net_total: 4,
                },
            };
        }
        if (url.includes("/api/transactions/cashflow")) {
            return {
                data: { labels: ["2025-01-01"], inData: [10], outData: [6] },
            };
        }
        return { data: [{ id: 1, amount: 10, note: "a", date: "2025-01-01" }] };
    };
    const { result } = renderHook(() => useTransactionsApi());
    await result.current.fetchAll();
    const { waitFor } = await import("@testing-library/react");
    await waitFor(() => expect(result.current.transactions.length).toBe(1));
    await waitFor(() =>
        expect(result.current.cashflow.labels).toEqual(["2025-01-01"])
    );
    expect(result.current.stats.net_total).toBe(4);
});

import { useCallback, useEffect, useState } from "react";
import axios from "axios";

export function useTransactionsApi() {
  const [transactions, setTransactions] = useState([]);
  const [cashflow, setCashflow] = useState({ labels: [], inData: [], outData: [] });
  const [stats, setStats] = useState({ month: "", in_total: 0, out_total: 0, net_total: 0 });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const fetchAll = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      const [tx, cf, st] = await Promise.all([
        axios.get("/api/transactions", { withCredentials: true }),
        axios.get("/api/transactions/cashflow", { withCredentials: true }),
        axios.get("/api/transactions/stats-month", { withCredentials: true }),
      ]);
      const list = Array.isArray(tx?.data) ? tx.data : [];
      const series = cf?.data ?? { labels: [], inData: [], outData: [] };
      const s = st?.data ?? { month: "", in_total: 0, out_total: 0, net_total: 0 };
      setTransactions(list);
      setCashflow(series);
      setStats(s);
    } catch (e) {
      setError("Gagal memuat data transaksi");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchAll();
  }, [fetchAll]);

  return { transactions, cashflow, stats, loading, error, fetchAll };
}

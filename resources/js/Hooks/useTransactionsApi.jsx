import { useCallback, useEffect, useState } from "react";
import axios from "axios";

export function useTransactionsApi() {
  const [transactions, setTransactions] = useState([]);
  const [cashflow, setCashflow] = useState({ labels: [], inData: [], outData: [] });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const fetchAll = useCallback(async () => {
    setLoading(true);
    setError("");
    try {
      const [tx, cf] = await Promise.all([
        axios.get("/api/transactions", { withCredentials: true }),
        axios.get("/api/transactions/cashflow", { withCredentials: true }),
      ]);
      const list = Array.isArray(tx?.data) ? tx.data : [];
      const series = cf?.data ?? { labels: [], inData: [], outData: [] };
      setTransactions(list);
      setCashflow(series);
    } catch (e) {
      setError("Gagal memuat data transaksi");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchAll();
  }, [fetchAll]);

  return { transactions, cashflow, loading, error, fetchAll };
}


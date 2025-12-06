import React, { useMemo } from "react";

export default function MonthlyStatsCards({ stats }) {
  const fmt = useMemo(
    () => new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }),
    []
  );

  const monthLabel = useMemo(() => {
    const m = stats?.month || "";
    if (!m) return "Bulan Ini";
    const [y, mm] = m.split("-");
    const monthNames = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
    const idx = Math.max(0, Math.min(11, (parseInt(mm, 10) || 1) - 1));
    return `${monthNames[idx]} ${y}`;
  }, [stats?.month]);

  return (
    <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
      <div className="card bg-base-100 shadow">
        <div className="card-body">
          <div className="text-xs opacity-60">Pemasukan • {monthLabel}</div>
          <div className="text-2xl font-semibold text-success">{fmt.format(stats?.in_total || 0)}</div>
        </div>
      </div>
      <div className="card bg-base-100 shadow">
        <div className="card-body">
          <div className="text-xs opacity-60">Pengeluaran • {monthLabel}</div>
          <div className="text-2xl font-semibold text-error">{fmt.format(stats?.out_total || 0)}</div>
        </div>
      </div>
      <div className="card bg-base-100 shadow">
        <div className="card-body">
          <div className="text-xs opacity-60">Total • {monthLabel}</div>
          <div className="text-2xl font-semibold">{fmt.format((stats?.net_total || 0))}</div>
        </div>
      </div>
    </div>
  );
}


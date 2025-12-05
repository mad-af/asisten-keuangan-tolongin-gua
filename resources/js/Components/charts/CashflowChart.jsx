import React, { useMemo } from "react";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Legend,
    Filler,
} from "chart.js";
import { Line } from "react-chartjs-2";

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Legend,
    Filler
);

export default function CashflowChart({ transactions = [], height = 240 }) {
    const sample = useMemo(() => {
        if (Array.isArray(transactions) && transactions.length)
            return transactions;
        return Array.from({ length: 20 }, (_, i) => {
            const id = i + 1;
            const isIn = id % 2 === 1;
            const day = String((i % 28) + 1).padStart(2, "0");
            return {
                id,
                device_id: "sample-device",
                type: isIn ? "IN" : "OUT",
                amount: isIn ? 100000 + i * 2000 : 60000 + i * 1200,
                note: isIn ? "Pemasukan contoh" : "Pengeluaran contoh",
                date: `2025-12-${day}`,
            };
        });
    }, [transactions]);

    const { labels, inData, outData } = useMemo(() => {
        const byDate = new Map();
        for (const t of sample) {
            const d = (t.date || "").slice(0, 10);
            if (!byDate.has(d)) byDate.set(d, { IN: 0, OUT: 0 });
            const acc = byDate.get(d);
            if (t.type === "IN") acc.IN += Number(t.amount || 0);
            else acc.OUT += Number(t.amount || 0);
        }
        const labels = Array.from(byDate.keys()).sort();
        const inData = labels.map((d) => byDate.get(d).IN);
        const outData = labels.map((d) => byDate.get(d).OUT);
        return { labels, inData, outData };
    }, [sample]);

    const data = {
        labels,
        datasets: [
            {
                label: "Pemasukan",
                data: inData,
                borderColor: "rgb(22,163,74)",
                backgroundColor: "rgba(22,163,74,0.2)",
                tension: 0.3,
                fill: true,
                pointRadius: 2,
            },
            {
                label: "Pengeluaran",
                data: outData,
                borderColor: "rgb(220,38,38)",
                backgroundColor: "rgba(220,38,38,0.2)",
                tension: 0.3,
                fill: true,
                pointRadius: 2,
            },
        ],
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: "top" },
            tooltip: { mode: "index", intersect: false },
        },
        interaction: { mode: "index", intersect: false },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: "rgba(0,0,0,0.05)" } },
        },
    };

    return (
        <div className="mb-4">
            <div className="font-medium mb-2">Cashflow</div>
            <div style={{ height }}>
                <Line data={data} options={options} />
            </div>
        </div>
    );
}

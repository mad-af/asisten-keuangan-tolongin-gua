import React, { useMemo } from "react";
import DynamicTable from "../ui/DynamicTable.jsx";

export default function TransactionTable({ data = [], headerLeft }) {
    const formatter = useMemo(
        () =>
            new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                maximumFractionDigits: 0,
            }),
        []
    );

    const columns = [
        { label: "Tanggal", key: "date" },
        {
            label: "Tipe",
            render: (row) => (
                <span
                    className={`badge badge-sm ${
                        row.type === "IN" ? "badge-success" : "badge-error"
                    }`}
                >
                    {row.type}
                </span>
            ),
        },
        {
            label: "Jumlah",
            render: (row) => (
                <span
                    className={
                        row.type === "IN" ? "text-success" : "text-error"
                    }
                >
                    {row.type === "IN" ? "+" : "-"}
                    {formatter.format(row.amount)}
                </span>
            ),
        },
        { label: "Catatan", key: "note" },
    ];

    const fallbackData = Array.from({ length: 50 }, (_, i) => {
        const id = i + 1;
        const isIn = id % 2 === 1;
        const day = String((i % 28) + 1).padStart(2, "0");
        return {
            id,
            device_id: "sample-device",
            type: isIn ? "IN" : "OUT",
            amount: isIn ? 100000 + i * 1000 : 50000 + i * 500,
            note: isIn ? "Pemasukan contoh" : "Pengeluaran contoh",
            date: `2025-12-${day}`,
        };
    });

    const rows = Array.isArray(data) && data.length ? data : fallbackData;

    return (
        <DynamicTable
            columns={columns}
            data={rows}
            rowKey="id"
            striped
            paginate
            pageSize={10}
            headerLeft={headerLeft}
        />
    );
}

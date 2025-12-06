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
                        row.type_badge ||
                        (row.type === "IN" ? "badge-success" : "badge-error")
                    }`}
                >
                    {row.type_label || row.type}
                </span>
            ),
        },
        {
            label: "Jumlah",
            render: (row) => (
                <span
                    className={
                        row.type_text_class ||
                        (row.type === "IN" ? "text-success" : "text-error")
                    }
                >
                    {row.type === "IN" ? "+" : "-"}
                    {formatter.format(row.amount)}
                </span>
            ),
        },
        { label: "Catatan", key: "note" },
    ];

    const rows = Array.isArray(data) ? data : [];

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

import React, { useEffect, useMemo, useState } from "react";

export default function DynamicTable({
    columns = [],
    data = [],
    rowKey,
    striped = false,
    compact = false,
    loading = false,
    emptyText = "Tidak ada data",
    headerLeft,
    headerRight,
    onRowClick,
    className = "",
    paginate = false,
    pageSize: pageSizeProp = 10,
}) {
    const tableCls = `table w-full rounded-box border border-base-content/10 ${
        striped ? "table-zebra" : ""
    } ${compact ? "table-xs" : ""}`;
    const getRowKey = (row, idx) => {
        if (typeof rowKey === "function") return rowKey(row, idx);
        if (typeof rowKey === "string") return row?.[rowKey] ?? idx;
        return idx;
    };
    const [page, setPage] = useState(1);
    const [pageSize, setPageSize] = useState(pageSizeProp);
    const total = data.length;
    const totalPages = useMemo(
        () => Math.max(Math.ceil(total / pageSize), 1),
        [total, pageSize]
    );
    useEffect(() => {
        if (page > totalPages) setPage(totalPages);
    }, [totalPages, page]);
    const start = (page - 1) * pageSize;
    const end = start + pageSize;
    const pagedData = paginate ? data.slice(start, end) : data;
    return (
        <div className={`w-full ${className}`}>
            {(headerLeft || headerRight) && (
                <div className="flex items-center justify-between mb-2">
                    <div>{headerLeft}</div>
                    <div>{headerRight}</div>
                </div>
            )}
            <div className="overflow-x-auto">
                <table className={tableCls}>
                    <thead>
                        <tr>
                            {columns.map((col, i) => (
                                <th key={i} className={col.className}>
                                    {col.label}
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr>
                                <td
                                    colSpan={Math.max(columns.length, 1)}
                                    className="text-center p-4"
                                >
                                    Memuat...
                                </td>
                            </tr>
                        ) : pagedData.length === 0 ? (
                            <tr>
                                <td
                                    colSpan={Math.max(columns.length, 1)}
                                    className="text-center p-4 opacity-60"
                                >
                                    {emptyText}
                                </td>
                            </tr>
                        ) : (
                            pagedData.map((row, idx) => (
                                <tr
                                    key={getRowKey(row, idx)}
                                    className={
                                        onRowClick
                                            ? "cursor-pointer"
                                            : undefined
                                    }
                                    onClick={
                                        onRowClick
                                            ? () => onRowClick(row)
                                            : undefined
                                    }
                                >
                                    {columns.map((col, i) => {
                                        const content =
                                            typeof col.render === "function"
                                                ? col.render(row, idx)
                                                : typeof col.accessor ===
                                                  "function"
                                                ? col.accessor(row, idx)
                                                : col.key
                                                ? row?.[col.key]
                                                : null;
                                        return (
                                            <td
                                                key={i}
                                                className={col.tdClassName}
                                            >
                                                {content}
                                            </td>
                                        );
                                    })}
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
            {paginate && (
                <div className="flex items-center justify-between mt-2">
                    <div className="opacity-70 text-sm">
                        Menampilkan {Math.min(end, total)} dari {total} entri
                    </div>
                    <div className="flex items-center gap-2 flex-wrap">
                        <select
                            className="select select-bordered select-sm w-24"
                            value={pageSize}
                            onChange={(e) => {
                                const v = parseInt(e.target.value, 10);
                                if (!Number.isNaN(v) && v > 0) {
                                    setPageSize(v);
                                    setPage(1);
                                }
                            }}
                        >
                            <option value={5}>5</option>
                            <option value={10}>10</option>
                            <option value={20}>20</option>
                            <option value={50}>50</option>
                        </select>
                        <div className="join">
                            <button
                                className="btn btn-sm join-item"
                                disabled={page <= 1}
                                onClick={() => setPage(Math.max(page - 1, 1))}
                            >
                                Prev
                            </button>
                            <button className="btn btn-sm join-item" disabled>
                                {page} / {totalPages}
                            </button>
                            <button
                                className="btn btn-sm join-item"
                                disabled={page >= totalPages}
                                onClick={() =>
                                    setPage(Math.min(page + 1, totalPages))
                                }
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

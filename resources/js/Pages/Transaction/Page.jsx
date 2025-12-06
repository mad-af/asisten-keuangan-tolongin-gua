import React from "react";
import AppLayout from "../../Layouts/AppLayout.jsx";
import TransactionTable from "../../Components/transaction/TransactionTable.jsx";
import CashflowChart from "../../Components/charts/CashflowChart.jsx";
import MonthlyStatsCards from "../../Components/transaction/MonthlyStatsCards.jsx";
import { useUserApi } from "../../Hooks/useUserApi.jsx";
import { useTransactionsApi } from "../../Hooks/useTransactionsApi.jsx";

const Index = () => {
    const { me } = useUserApi();
    const { transactions, cashflow, stats } = useTransactionsApi();

    return (
        <div className="h-full w-full p-4">
            <MonthlyStatsCards stats={stats} />
            <div className="divider"></div>
            <CashflowChart series={cashflow} transactions={transactions} />
            <div className="divider"></div>
            <TransactionTable
                headerLeft={
                    <div className="font-medium">
                        Riwayat Transaksi{me?.name ? ` • ${me.name}` : ""}
                    </div>
                }
                data={transactions}
            />
        </div>
    );
};

Index.layout = (page) => <AppLayout>{page}</AppLayout>;

export default Index;

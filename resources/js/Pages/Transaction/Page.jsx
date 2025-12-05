import React from "react";
import AppLayout from "../../Layouts/AppLayout.jsx";
import TransactionTable from "../../Components/transaction/TransactionTable.jsx";
import CashflowChart from "../../Components/charts/CashflowChart.jsx";

const Index = () => {
    return (
        <div className="h-full w-full p-4">
            <CashflowChart />
            <div className="divider"></div>
            <TransactionTable
                headerLeft={
                    <div className="font-medium">Riwayat Transaksi</div>
                }
            />
        </div>
    );
};

Index.layout = (page) => <AppLayout>{page}</AppLayout>;

export default Index;

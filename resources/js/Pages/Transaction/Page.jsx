import React, { useEffect } from "react";
import AppLayout from "../../Layouts/AppLayout.jsx";
import TransactionTable from "../../Components/transaction/TransactionTable.jsx";
import CashflowChart from "../../Components/charts/CashflowChart.jsx";
import { useUserApi } from "../../Hooks/useUserApi.jsx";

const Index = () => {
    const { me, loadingMe, fetchMe } = useUserApi();

    useEffect(() => {
        fetchMe();
    }, [fetchMe]);

    return (
        <div className="h-full w-full p-4">
            <CashflowChart />
            <div className="divider"></div>
            <TransactionTable
                headerLeft={
                    <div className="font-medium">
                        Riwayat Transaksi{me?.name ? ` • ${me.name}` : ""}
                    </div>
                }
            />
        </div>
    );
};

Index.layout = (page) => <AppLayout>{page}</AppLayout>;

export default Index;

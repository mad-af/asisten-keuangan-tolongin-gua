import React from "react";
import AppLayout from "../../Layouts/AppLayout.jsx";

const Index = () => {
    return (
        <div className="h-screen w-full bg-base-200 flex items-center justify-center">
            <div className="text-sm opacity-60">
                Chat akan tersedia di sini.
            </div>
        </div>
    );
};

Index.layout = (page) => <AppLayout>{page}</AppLayout>;

export default Index;

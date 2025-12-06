import { router } from "@inertiajs/react";
import { useState } from "react";
import { useUserApi } from "../../Hooks/useUserApi.jsx";

export default function ChooseSetup() {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("");
    const { setup } = useUserApi();

    const handleNewSetup = async () => {
        setError("");
        setLoading(true);
        try {
            await setup("new");
            router.visit("/chat");
        } catch (e) {
            setError("Gagal memilih setup baru");
        } finally {
            setLoading(false);
        }
    };

    const handleDummySetup = async () => {
        setError("");
        setLoading(true);
        try {
            await setup("dummy");
            router.visit("/chat");
        } catch (e) {
            setError("Gagal menggunakan dummy setup");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="card w-full max-w-sm bg-base-100 shadow">
            <div className="card-body">
                <div className="flex flex-col items-center gap-2 mb-2">
                    <h2 className="card-title text-center">
                        Mau Mulai Dari Mana?
                    </h2>
                    <p className="text-sm opacity-70">
                        Mulai baru atau gunakan data dummy
                    </p>
                </div>

                <div className="space-y-3">
                    <button
                        className="btn btn-primary w-full"
                        onClick={handleNewSetup}
                        disabled={loading}
                    >
                        {loading ? "Memproses…" : "Mulai Akun Baru"}
                    </button>
                    <button
                        className="btn w-full"
                        onClick={handleDummySetup}
                        disabled={loading}
                    >
                        {loading ? "Memproses…" : "Pakai Data Dummy"}
                    </button>
                    {error && (
                        <div className="text-error text-xs mt-1">{error}</div>
                    )}
                    <div className="p-2 bg-info/5 border border-info/10 rounded-md">
                        <span className="text-xs text-info-content font-semibold block">
                            Catatan:
                        </span>
                        <span className="text-xs text-info-content block">
                            Mode dummy akan mengisi transaksi simulasi ±6 bulan
                            ke belakang.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    );
}

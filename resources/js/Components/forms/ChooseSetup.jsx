import { router } from "@inertiajs/react";

export default function ChooseSetup() {
    const choose = (option) => {
        router.post("/setup/select", { option });
    };

    return (
        <div className="card w-full max-w-sm bg-base-100 shadow">
            <div className="card-body">
                <div className="flex flex-col items-center gap-2 mb-2">
                    <h2 className="card-title text-center">Mau Mulai Dari Mana?</h2>
                    <p className="text-sm opacity-70">
                        Mulai baru atau gunakan data dummy
                    </p>
                </div>

                <div className="space-y-3">
                    <button
                        className="btn btn-primary w-full"
                        onClick={() => choose("new")}
                    >
                        Mulai Akun Baru
                    </button>
                    <button
                        className="btn w-full"
                        onClick={() => choose("dummy")}
                    >
                        Pakai Data Dummy
                    </button>
                    <div className="p-2 bg-info/5 border border-info/10 rounded-md">
                        <span className="text-xs text-info-content font-semibold block">Catatan:</span>
                        <span className="text-xs text-info-content block">
                            Mode dummy akan mengisi transaksi simulasi
                            ±6 bulan ke belakang.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    );
}

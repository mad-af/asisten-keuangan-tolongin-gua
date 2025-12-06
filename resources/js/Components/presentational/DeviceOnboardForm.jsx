export default function DeviceOnboardForm({
    name,
    setName,
    error,
    loadingEnter,
    onEnter,
}) {
    return (
        <div className="w-full flex items-center justify-center p-4">
            <div className="card w-full max-w-xs bg-base-100 shadow">
                <div className="card-body">
                    <div className="flex flex-col items-center gap-1 mb-2">
                        <h2 className="card-title text-center">
                            Selamat Datang
                        </h2>
                        <p className="text-sm opacity-70 text-center">
                            Isi nama perangkat atau nama Anda
                        </p>
                    </div>

                    <div className="form-control mb-3">
                        <label className="label flex flex-col gap-1 items-start">
                            <span className="label-text text-sm">Nama</span>
                            <input
                                type="text"
                                className="input input-bordered w-full text-sm"
                                placeholder="Nama perangkat / pengguna"
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                            />
                        </label>
                        {error && (
                            <div className="text-error text-xs mt-1">
                                {error}
                            </div>
                        )}
                    </div>

                    <div className="flex flex-col gap-2">
                        <button
                            className="btn btn-primary w-full"
                            onClick={onEnter}
                            disabled={!!loadingEnter}
                        >
                            {loadingEnter ? "Memproses…" : "Masuk"}
                        </button>
                    </div>

                    <div className="mt-3 text-center text-xs opacity-60">
                        Semua request akan mengirim cookie secara otomatis.
                    </div>
                </div>
            </div>
        </div>
    );
}

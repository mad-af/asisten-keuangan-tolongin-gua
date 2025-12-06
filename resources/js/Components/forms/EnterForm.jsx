import { useForm } from "@inertiajs/react";

export default function EnterForm() {
  const { data, setData, post, processing } = useForm({ name: "" });

  const submit = (e) => {
    e.preventDefault();
    post("/enter");
  };

  return (
    <div className="card w-full max-w-xs bg-base-100 shadow">
      <div className="card-body">
        <div className="flex flex-col items-center gap-2 mb-2">
          <h2 className="card-title text-center">Welcome back</h2>
          <p className="text-sm opacity-70">Masuk dengan nama Anda</p>
        </div>

        <form onSubmit={submit} className="space-y-4">
          <div className="form-control">
            <label className="label flex flex-col gap-1 items-start">
              <span className="label-text text-sm">Nama</span>
              <input
                type="text"
                className="input input-bordered w-full text-sm"
                placeholder="Nama Anda"
                value={data.name}
                onChange={(e) => setData("name", e.target.value)}
              />
            </label>
          </div>

          <button type="submit" className="btn btn-primary w-full" disabled={processing}>
            {processing ? "Memproses…" : "Masuk"}
          </button>
        </form>

        <div className="mt-4 text-center text-xs opacity-60">
          Dengan menekan Masuk, Anda menyetujui ketentuan layanan.
        </div>
      </div>
    </div>
  );
}


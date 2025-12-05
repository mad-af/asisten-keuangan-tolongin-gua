export default function DeviceNameInput({ name, onChangeName, error }) {
  return (
    <div className="form-control mb-3">
      <label className="label flex flex-col gap-1 items-start">
        <span className="label-text text-sm">Nama</span>
        <input
          type="text"
          className="input input-bordered w-full text-sm"
          placeholder="Nama perangkat / pengguna"
          value={name}
          onChange={(e) => onChangeName(e.target.value)}
        />
      </label>
      {error && <div className="text-error text-xs mt-1">{error}</div>}
    </div>
  );
}


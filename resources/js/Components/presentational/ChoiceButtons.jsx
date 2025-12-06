export default function ChoiceButtons({ loading, onNew, onDummy }) {
  return (
    <div className="flex flex-col gap-2">
      <button
        className="btn btn-primary w-full"
        onClick={onNew}
        disabled={loading}
      >
        {loading ? "Memproses…" : "New Setup"}
      </button>
      <button
        className="btn w-full"
        onClick={onDummy}
        disabled={loading}
      >
        {loading ? "Memproses…" : "Dummy Setup"}
      </button>
    </div>
  );
}


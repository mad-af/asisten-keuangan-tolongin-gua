export default function Sidebar() {
    const contacts = [
        { id: 1, name: "Budi", last: "Kemarin: Oke sip..." },
        { id: 2, name: "Ayu", last: "Baru saja: Lagi di jalan" },
        { id: 3, name: "Roni", last: "3 hari lalu: Siap" },
    ];

    return (
        <div className="w-80 bg-base-100 flex flex-col">
            {/* Header */}
            <div className="p-4 bg-base-300">
                <h2 className="text-lg font-bold">WhatsApp Clone</h2>
            </div>

            {/* Search */}
            <div className="p-3">
                <input
                    type="text"
                    placeholder="Cari atau mulai chat..."
                    className="input input-sm input-bordered w-full"
                />
            </div>

            {/* Contact List */}
            <div className="overflow-y-auto flex-1">
                {contacts.map((c) => (
                    <div
                        key={c.id}
                        className="p-4 hover:bg-base-200 cursor-pointer border-b border-base-300"
                    >
                        <div className="font-semibold">{c.name}</div>
                        <div className="text-sm opacity-70">{c.last}</div>
                    </div>
                ))}
            </div>
        </div>
    );
}

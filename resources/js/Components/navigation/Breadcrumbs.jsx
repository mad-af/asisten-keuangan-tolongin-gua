import { Link } from "@inertiajs/react";

export default function Breadcrumbs({ items }) {
    const path = typeof window !== "undefined" ? window.location.pathname : "/";
    const segments = path
        .replace(/^\/+|\/+$/g, "")
        .split("/")
        .filter(Boolean);
    const isChat = path.startsWith("/chat");

    const computed =
        Array.isArray(items) && items.length
            ? items
            : [
                  ...segments.map((seg, idx) => {
                      const href = "/" + segments.slice(0, idx + 1).join("/");
                      const label = seg
                          .replace(/[-_]/g, " ")
                          .replace(/\b\w/g, (c) => c.toUpperCase());
                      return { label, href };
                  }),
              ];

    const lastIndex = computed.length - 1;

    return (
        <div className="w-full flex items-center px-4 gap-4">
            <div className="breadcrumbs text-sm">
                <ul>
                    {computed.map((item, idx) => (
                        <li key={idx}>
                            {idx === lastIndex ? (
                                <span className="opacity-70">{item.label}</span>
                            ) : (
                                <Link
                                    href={item.href}
                                    className="hover:underline"
                                >
                                    {item.label}
                                </Link>
                            )}
                        </li>
                    ))}
                </ul>
            </div>
            {isChat && (
                <span className="badge badge-sm badge-outline">Simulasi WhatsApp</span>
            )}
        </div>
    );
}

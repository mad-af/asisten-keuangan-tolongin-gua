import {
    HomeIcon,
    Cog6ToothIcon,
    ChatBubbleLeftRightIcon,
} from "@heroicons/react/24/outline";

export default function AppSidebar({ items }) {
    const defaultItems = [
        {
            label: "Chat",
            tip: "Chat",
            href: "/chat",
            Icon: ChatBubbleLeftRightIcon,
        },
        { label: "Settings", tip: "Settings", href: "#", Icon: Cog6ToothIcon },
    ];

    const menu = Array.isArray(items) && items.length ? items : defaultItems;

    return (
        <ul className="menu w-full grow">
            {menu.map(({ label, tip, href, Icon }, idx) => {
                const path =
                    typeof window !== "undefined"
                        ? window.location.pathname
                        : "";
                const isActive =
                    typeof href === "string" &&
                    (path === href || (href !== "/" && path.startsWith(href)));
                const itemCls = `is-drawer-close:tooltip is-drawer-close:tooltip-right rounded-md ${
                    isActive
                        ? "bg-primary text-primary-content"
                        : "hover:bg-base-300"
                }`;
                const iconCls = `my-1.5 inline-block size-4 ${
                    isActive ? "text-primary-content" : ""
                }`;
                return (
                    <li key={idx}>
                        <a
                            href={href}
                            className={itemCls}
                            data-tip={tip}
                            aria-current={isActive ? "page" : undefined}
                        >
                            <Icon className={iconCls} />
                            <span className="is-drawer-close:hidden">
                                {label}
                            </span>
                        </a>
                    </li>
                );
            })}
        </ul>
    );
}

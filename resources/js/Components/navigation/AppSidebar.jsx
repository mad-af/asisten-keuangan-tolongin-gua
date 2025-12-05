import {
    ChatBubbleLeftRightIcon,
    ChevronUpDownIcon,
    DocumentCurrencyDollarIcon,
    TrashIcon,
} from "@heroicons/react/24/outline";
import { usePage, Link } from "@inertiajs/react";
import Avatar from "../ui/Avatar.jsx";
import Logo from "../ui/Logo.jsx";

export default function AppSidebar({ items }) {
    const { user } = usePage().props ?? {};
    const defaultItems = [
        {
            label: "Chat",
            tip: "Chat",
            href: "/chat",
            Icon: ChatBubbleLeftRightIcon,
        },
        {
            label: "Transaksi",
            tip: "Transaksi",
            href: "/transactions",
            Icon: DocumentCurrencyDollarIcon,
        },
    ];

    const menu = Array.isArray(items) && items.length ? items : defaultItems;

    return (
        <div className="flex min-h-screen h-full flex-col w-full">
            <div className="flex-1 flex flex-col items-center justify-start pt-44 pb-6">
                <div className="w-full flex items-center justify-center p-4">
                    <Logo width={140} />
                </div>
                <ul className="menu w-full gap-2">
                    {menu.map(({ label, tip, href, Icon }, idx) => {
                        const path =
                            typeof window !== "undefined"
                                ? window.location.pathname
                                : "";
                        const isActive =
                            typeof href === "string" &&
                            (path === href ||
                                (href !== "/" && path.startsWith(href)));
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
                                <Link
                                    href={href}
                                    className={itemCls}
                                    data-tip={tip}
                                    aria-current={isActive ? "page" : undefined}
                                >
                                    <Icon className={iconCls} />
                                    <span className="is-drawer-close:hidden">
                                        {label}
                                    </span>
                                </Link>
                            </li>
                        );
                    })}
                </ul>
            </div>

            <div className="w-full border-t border-base-300 sticky bottom-0 overflow-visible is-drawer-close:flex is-drawer-close:justify-center">
                <div className="p-2 dropdown dropdown-right dropdown-end w-full is-drawer-close:w-auto">
                    <button
                        tabIndex={0}
                        className="flex items-center gap-3 hover:bg-base-300 rounded-md p-2 w-full cursor-pointer is-drawer-close:w-14 is-drawer-close:h-14 is-drawer-close:justify-center is-drawer-close:mx-auto is-drawer-close:p-1 is-drawer-close:gap-0"
                    >
                        <Avatar
                            size={36}
                            rounded={false}
                            className="rounded-md"
                            dicebear={{
                                seed: user?.name ?? "Guest",
                                backgroundType: "solid",
                                radius: 0,
                            }}
                        />
                        <div className="min-w-0 is-drawer-close:hidden">
                            <div className="text-sm font-medium truncate">
                                {user?.name ?? "Guest"}
                            </div>
                            {user?.email && (
                                <div className="text-xs opacity-60 truncate">
                                    {user.email}
                                </div>
                            )}
                        </div>
                        <ChevronUpDownIcon className="size-4 ml-auto opacity-60 is-drawer-close:hidden" />
                    </button>
                    <ul
                        tabIndex={0}
                        className="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-40 mb-2 z-50"
                    >
                        <li>
                            <a className="text-error flex items-center gap-2">
                                <TrashIcon className="size-4" />
                                Hapus akun
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    );
}

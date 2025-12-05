import AppSidebar from "../Components/navigation/AppSidebar.jsx";
import Logo from "../Components/ui/Logo.jsx";
import Breadcrumbs from "../Components/navigation/Breadcrumbs.jsx";
import {
    ArrowRightStartOnRectangleIcon,
    Bars3Icon,
} from "@heroicons/react/24/outline";

export default function AppLayout({ children }) {
    return (
        <div className="drawer lg:drawer-open bg-base-200">
            <input
                id="my-drawer-4"
                type="checkbox"
                className="drawer-toggle"
                defaultChecked
            />
            <div className="drawer-content m-1 md:m-2 md:ml-0.5 bg-base-100 rounded-md shadow-lg">
                {/* Navbar */}
                <nav className="navbar w-full min-h-8">
                    <label
                        htmlFor="my-drawer-4"
                        aria-label="open sidebar"
                        className="btn btn-square btn-ghost"
                    >
                        {/* Sidebar toggle icon */}
                        <ArrowRightStartOnRectangleIcon className="size-4" />
                    </label>

                    <Breadcrumbs />
                </nav>
                {/* Page content here */}
                <div className="flex-1 w-full">{children}</div>
            </div>

            <div className="drawer-side">
                <label
                    htmlFor="my-drawer-4"
                    aria-label="close sidebar"
                    className="drawer-overlay"
                ></label>
                <div className="flex min-h-full flex-col items-start bg-base-200 is-drawer-close:w-14 is-drawer-open:w-64">
                    <AppSidebar />
                </div>
            </div>
        </div>
    );
}

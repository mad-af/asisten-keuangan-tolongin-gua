import AppSidebar from "../Components/navigation/AppSidebar.jsx";

export default function AppLayout({ children }) {
    return (
        <div className="drawer lg:drawer-open">
            <input
                id="my-drawer-4"
                type="checkbox"
                className="drawer-toggle"
                defaultChecked
            />
            <div className="drawer-content m-2 ml-0.5 bg-base-100 rounded-md shadow-lg">
                {/* Page content here */}
                <div className="p-4">Page Content</div>
            </div>

            <div className="drawer-side overflow-visible">
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

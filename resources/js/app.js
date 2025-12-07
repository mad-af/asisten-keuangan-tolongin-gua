import "./bootstrap";
import "../css/app.css";
import { createInertiaApp } from "@inertiajs/react";
import { createRoot } from "react-dom/client";
import React from "react";

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob([
            "./Pages/**/*.jsx",
            "!./Pages/**/__tests__/*.jsx",
            "!./Pages/**/*.test.jsx",
        ], { eager: true });
        const page = pages[`./Pages/${name}.jsx`];
        return page?.default ?? page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(React.createElement(App, props));
    },
});

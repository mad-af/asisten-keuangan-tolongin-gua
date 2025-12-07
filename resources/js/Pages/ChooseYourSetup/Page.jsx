import { Head, router } from "@inertiajs/react";
import LoginLayout from "../../Layouts/LoginLayout.jsx";
import ChooseSetup from "../../Components/forms/ChooseSetup.jsx";
import { useEffect } from "react";

function TestPage() {
    useEffect(() => {
        const getCookie = (name) =>
            typeof document !== "undefined"
                ? document.cookie
                      .split(";")
                      .map((c) => c.trim())
                      .find((c) => c.startsWith(name + "="))
                : null;
        const tokenCookie = getCookie("user_token");
        const setupTypeCookie = localStorage.getItem("setup_type");
        if (tokenCookie && setupTypeCookie) {
            router.visit("/chat");
        }
    }, []);
    return (
        <>
            <Head title="Masuk" />
            <ChooseSetup />
        </>
    );
}

TestPage.layout = (page) => <LoginLayout>{page}</LoginLayout>;

export default TestPage;

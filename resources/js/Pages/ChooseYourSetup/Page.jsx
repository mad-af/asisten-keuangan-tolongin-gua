import { Head } from "@inertiajs/react";
import LoginLayout from "../../Layouts/LoginLayout.jsx";
import ChooseSetup from "../../Components/forms/ChooseSetup.jsx";

function TestPage() {
    return (
        <>
            <Head title="Masuk" />
            <ChooseSetup />
        </>
    );
}

TestPage.layout = (page) => <LoginLayout>{page}</LoginLayout>;

export default TestPage;

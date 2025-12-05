import { Head } from "@inertiajs/react";
import LoginLayout from "../../Layouts/LoginLayout.jsx";
import EnterForm from "../../Components/forms/EnterForm.jsx";

function TestPage() {
    return (
        <>
            <Head title="Masuk" />
            <EnterForm />
        </>
    );
}

TestPage.layout = (page) => <LoginLayout>{page}</LoginLayout>;

export default TestPage;

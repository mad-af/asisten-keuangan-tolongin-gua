import { Head, router } from "@inertiajs/react";
import LoginLayout from "../../Layouts/LoginLayout.jsx";
import DeviceOnboardForm from "../../Components/presentational/DeviceOnboardForm.jsx";
import { useUserApi } from "../../Hooks/useUserApi.jsx";
import { useEffect } from "react";

function OnboardingContainer() {
    const { name, setName, loadingEnter, error, register } = useUserApi({
        onRegisterSuccess: () => router.visit("/choose-your-setup"),
    });

    useEffect(() => {
        const getCookie = (name) =>
            typeof document !== "undefined"
                ? document.cookie
                      .split(";")
                      .map((c) => c.trim())
                      .find((c) => c.startsWith(name + "="))
                : null;
        const tokenCookie = getCookie("user_token");
        if (tokenCookie) {
            router.visit("/choose-your-setup");
        }
    }, []);

    return (
        <>
            <Head title="Onboarding" />
            <DeviceOnboardForm
                name={name}
                setName={setName}
                error={error}
                loadingEnter={loadingEnter}
                onEnter={register}
            />
        </>
    );
}

OnboardingContainer.layout = (page) => <LoginLayout>{page}</LoginLayout>;

export default OnboardingContainer;

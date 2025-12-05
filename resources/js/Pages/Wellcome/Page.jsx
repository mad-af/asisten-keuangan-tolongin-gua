import { Head, router } from "@inertiajs/react";
import LoginLayout from "../../Layouts/LoginLayout.jsx";
import DeviceOnboardForm from "../../Components/presentational/DeviceOnboardForm.jsx";
import { useRegisterDevice } from "../../Hooks/useRegisterDevice.jsx";

function OnboardingContainer() {
    const { name, setName, loadingEnter, error, handleEnter } =
        useRegisterDevice({
            onSuccess: () => router.visit("/choose-your-setup"),
        });

    return (
        <>
            <Head title="Onboarding" />
            <DeviceOnboardForm
                name={name}
                setName={setName}
                error={error}
                loadingEnter={loadingEnter}
                onEnter={handleEnter}
            />
        </>
    );
}

OnboardingContainer.layout = (page) => <LoginLayout>{page}</LoginLayout>;

export default OnboardingContainer;

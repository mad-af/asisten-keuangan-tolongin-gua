import { Head, router } from "@inertiajs/react";
import LoginLayout from "../../Layouts/LoginLayout.jsx";
import { useRegisterDevice } from "../../Hooks/useRegisterDevice.jsx";
import DeviceNameInput from "../../Components/presentational/DeviceNameInput.jsx";
import ChoiceButtons from "../../Components/presentational/ChoiceButtons.jsx";

function DeviceOnboardPage() {
  const { name, setName, loading, error, handleNewSetup, handleDummySetup } = useRegisterDevice({
    onSuccess: () => router.visit("/transactions"),
  });

  return (
    <>
      <Head title="Onboarding" />
      <div className="w-full flex items-center justify-center p-4">
        <div className="card w-full max-w-sm bg-base-100 shadow">
          <div className="card-body">
            <div className="flex flex-col items-center gap-1 mb-2">
              <h2 className="card-title text-center">Selamat Datang</h2>
              <p className="text-sm opacity-70 text-center">Isi nama perangkat atau nama Anda</p>
            </div>
            <DeviceNameInput name={name} onChangeName={setName} error={error} />
            <ChoiceButtons
              loading={loading}
              onNew={handleNewSetup}
              onDummy={handleDummySetup}
            />
          </div>
        </div>
      </div>
    </>
  );
}

DeviceOnboardPage.layout = (page) => <LoginLayout>{page}</LoginLayout>;

export default DeviceOnboardPage;


import Logo from "../Components/ui/Logo.jsx";

export default function LoginLayout({ children }) {
  return (
    <div className="min-h-screen bg-base-200 flex flex-col gap-6 items-center justify-start pt-32 px-4">
      <Logo width={120} />
      {children}
    </div>
  );
}


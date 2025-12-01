import NavBar from '../Components/navigation/NavBar.jsx';

export default function AppLayout({ children }) {
  return (
    <div className="min-h-screen bg-base-200 text-base-content">
      <NavBar />
      <main className="container mx-auto p-4">{children}</main>
    </div>
  );
}

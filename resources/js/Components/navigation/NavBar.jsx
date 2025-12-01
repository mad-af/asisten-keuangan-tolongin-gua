import { Link } from '@inertiajs/react';

export default function NavBar() {
  return (
    <div className="navbar bg-base-200">
      <div className="flex-1">
        <Link href="/" className="btn btn-ghost text-xl">App</Link>
      </div>
      <div className="flex-none">
        <ul className="menu menu-horizontal px-1">
          <li><a href="/">Home</a></li>
          <li><Link href="/test">Test</Link></li>
        </ul>
      </div>
    </div>
  );
}

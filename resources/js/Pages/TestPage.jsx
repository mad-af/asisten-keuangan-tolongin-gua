import { Head, usePage } from '@inertiajs/react';
import AppLayout from '../Layouts/AppLayout.jsx';
import Button from '../Components/ui/Button.jsx';
import Card from '../Components/ui/Card.jsx';

function TestPage() {
  const { serverTime, message, user } = usePage().props;

  return (
    <>
      <Head title="Test" />
      <div className="space-y-6">
        <h1 className="text-3xl font-bold">Inertia + React + DaisyUI Test</h1>

        <Card title="Props From Laravel">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="p-2">
              <div className="font-semibold">Message</div>
              <div className="badge badge-primary">{message}</div>
            </div>
            <div className="p-2">
              <div className="font-semibold">Server Time</div>
              <div className="badge badge-secondary">{serverTime}</div>
            </div>
            <div className="p-2 md:col-span-2">
              <div className="font-semibold">User</div>
              <div className="mockup-code">
                <pre data-prefix="$"><code>{JSON.stringify(user, null, 2)}</code></pre>
              </div>
            </div>
          </div>
        </Card>

        <Card title="Interactivity">
          <div className="flex items-center gap-3">
            <Button onClick={() => alert('Button works')}>Primary Button</Button>
            <button className="btn btn-secondary">Secondary</button>
          </div>
        </Card>
      </div>
    </>
  );
}

TestPage.layout = (page) => <AppLayout>{page}</AppLayout>;

export default TestPage;

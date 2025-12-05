Tugas: Buat implementasi lengkap untuk fitur "1 device = 1 akun (auto-login)" di proyek Laravel + React (Inertia). Hasil harus mencakup file berikut yang siap copy-paste ke repo:

1) Laravel migration untuk tabel `devices`.
2) Eloquent Model `Device`.
3) Controller `DeviceController` dengan method register, dummySetup, revoke, me.
4) Service class `DeviceService` yang menampung business logic (create/update device, generate token, revoke).
5) Middleware `DeviceAuth` yang autentikasi via cookie `device_token`.
6) Route definitions (api.php / web.php sesuai kebutuhan).
7) React: custom hook `useRegisterDevice` (src/hooks/useRegisterDevice.js).
8) React: page Inertia container `DeviceOnboardPage.jsx` (src/Pages/DeviceOnboardPage.jsx) yang menggunakan hook.
9) React: presentational components di `src/components/presentational/`:
   - `DeviceOnboardForm.jsx` (input name + New Setup + Dummy Setup buttons)
   - `DeviceNameInput.jsx` (simple input)
   - `ChoiceButtons.jsx` (two buttons)
10) Small utility `getOrCreateDeviceId()` (src/utils/device.js).
11) Instructions: where to set `axios.defaults.withCredentials = true` and how to register middleware in Kernel.

Design constraints & requirements:
- **JANGAN** ubah layout, className, atau struktur DOM yang mempengaruhi tampilan. Visual must remain identical.
- **All side effects, network calls, validation and state** must live inside the hook (useRegisterDevice) or service; presentational components only receive props and callbacks.
- Server must set credential as **HttpOnly Secure cookie** named `device_token` with configurable expiry (default 365 days).
- Use Laravel built-in validation. Use `hash('sha256', Str::random(64) . microtime())` (or equivalent secure generator) for device_token.
- Cookie options: httpOnly=true, secure=true, sameSite='Lax' (assume HTTPS).
- Middleware must read cookie `device_token` and attach Device model to request (e.g., `$request->attributes->set('device', $device)` and optionally `auth()->setUser($device)`).
- Provide endpoints:
   - POST `/api/devices/register` — body: { device_id, device_name, device_info? } → create or update device, set cookie, return device minimal data.
   - POST `/api/devices/dummy-setup` — use pre-seeded dummy device (for testing), set cookie, return success.
   - POST `/api/devices/revoke` — revoke current device token (require auth via middleware).
   - GET `/api/devices/me` — return current device (require auth via middleware).
- Migration fields: id, device_id (unique), device_name, device_token (unique), device_info (json nullable), last_seen (timestamp nullable), created_at, updated_at.
- Model fillable/hidden: hide device_token from JSON; cast device_info to array.
- Service responsibilities: generate token, persist device, update last_seen, revoke token, describe any events/logging.
- Hook behavior (`useRegisterDevice`):
   - Expose: { name, setName, loading, error, handleNewSetup, handleDummySetup }.
   - handleNewSetup: validate name not empty, call `/api/devices/register` with device_id from `getOrCreateDeviceId()`, handle response, call optional onSuccess callback.
   - handleDummySetup: call `/api/devices/dummy-setup`.
   - Ensure axios call includes `{ withCredentials: true }` or rely on global axios defaults.
- Presentational `DeviceOnboardForm` props: { name, onChangeName, loading, error, onNew, onDummy } and must only render UI and call props.
- Inertia page container `DeviceOnboardPage` must use the hook and pass handlers to presentational component. On success, redirect to `/dashboard` (use Inertia.visit or router push).
- Provide brief README or comment in top of files that explains how to wire up (register middleware in Kernel, set axios defaults in bootstrap/app.js or resources/js/app.js).
- Provide minimal error handling and success path; keep code idiomatic and readable.

Acceptance criteria:
- All files compile (no obviously missing imports).
- Flow works end-to-end conceptually: first open -> show form -> user enters name -> click New Setup -> server sets HttpOnly cookie -> subsequent requests authenticated via middleware -> Inertia props include auth.device and UI shows logged-in state.
- Dummy setup path sets cookie similarly for quick testing.
- Presentational components contain zero side-effects or network calls.
- Hook contains side-effects and network calls and returns clear API.
- Middleware attaches Device to request for controllers to use.
- Migration matches specified schema.

Output format:
- Provide a list of created files and their exact paths.
- For each file, include the full code block (with PHP/JS/JSX code) ready to paste.
- At the end, add a short checklist of manual steps to perform after copying files (e.g., run migration, add Kernel middleware, set axios.defaults.withCredentials = true, seed dummy device, run npm build).

Notes:
- Keep names consistent: Device, DeviceController, DeviceService, DeviceAuth, useRegisterDevice, DeviceOnboardPage, DeviceOnboardForm.
- Keep everything in Indonesian comments where helpful.

import { getOrCreateDeviceId } from "../device.js";

globalThis.localStorage = globalThis.localStorage || {
  _s: {}, getItem(k){return this._s[k] ?? null;}, setItem(k,v){this._s[k]=String(v);}, removeItem(k){delete this._s[k];}
};
globalThis.crypto = globalThis.crypto || { randomUUID: () => "uuid-123" };

test("returns existing device_id from localStorage", () => {
  localStorage.setItem("device_id", "abc");
  const id = getOrCreateDeviceId();
  expect(id).toBe("abc");
});

test("creates device_id when missing and stores it", () => {
  localStorage.removeItem("device_id");
  const id = getOrCreateDeviceId();
  expect(id.length > 0).toBe(true);
  expect(localStorage.getItem("device_id")).toBe(id);
});


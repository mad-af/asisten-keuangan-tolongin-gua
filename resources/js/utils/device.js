export function getOrCreateDeviceId() {
  try {
    const existing = localStorage.getItem('device_id');
    if (existing && typeof existing === 'string' && existing.length > 0) {
      return existing;
    }
    const random = crypto?.randomUUID ? crypto.randomUUID() : `${Date.now()}-${Math.random().toString(16).slice(2)}`;
    localStorage.setItem('device_id', random);
    return random;
  } catch {
    return `${Date.now()}-${Math.random().toString(16).slice(2)}`;
  }
}


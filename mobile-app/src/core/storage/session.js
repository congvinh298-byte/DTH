import AsyncStorage from '@react-native-async-storage/async-storage';

const PHONE_KEY = 'dth_user_phone';
const NAME_KEY = 'dth_user_name';
const DEVICE_KEY = 'dth_device_fingerprint';
const ACTIVE_BOOKING_KEY = 'dth_active_booking';

export function normalizePhone(value) {
  return String(value || '').replace(/\D/g, '').slice(0, 15);
}

export function isValidPhone(value) {
  return /^\d{8,15}$/.test(normalizePhone(value));
}

export async function loadSession() {
  const [phone, name] = await Promise.all([
    AsyncStorage.getItem(PHONE_KEY),
    AsyncStorage.getItem(NAME_KEY),
  ]);

  return {
    phone: normalizePhone(phone),
    name: String(name || '').trim(),
  };
}

export async function saveSession({ phone, name = '' }) {
  await Promise.all([
    AsyncStorage.setItem(PHONE_KEY, normalizePhone(phone)),
    AsyncStorage.setItem(NAME_KEY, String(name).trim()),
  ]);
}

export async function clearSession() {
  await Promise.all([
    AsyncStorage.removeItem(PHONE_KEY),
    AsyncStorage.removeItem(NAME_KEY),
    AsyncStorage.removeItem(ACTIVE_BOOKING_KEY),
  ]);
}

export async function getDeviceFingerprint() {
  let fingerprint = await AsyncStorage.getItem(DEVICE_KEY);
  if (!fingerprint) {
    fingerprint = `dth-app-${Date.now().toString(36)}-${Math.random().toString(36).slice(2, 12)}`;
    await AsyncStorage.setItem(DEVICE_KEY, fingerprint);
  }
  return fingerprint;
}

export async function saveActiveBooking(booking) {
  await AsyncStorage.setItem(ACTIVE_BOOKING_KEY, JSON.stringify(booking));
}

export async function loadActiveBooking() {
  try {
    const value = await AsyncStorage.getItem(ACTIVE_BOOKING_KEY);
    return value ? JSON.parse(value) : null;
  } catch {
    return null;
  }
}

export async function clearActiveBooking() {
  await AsyncStorage.removeItem(ACTIVE_BOOKING_KEY);
}

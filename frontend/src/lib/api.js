import axios from 'axios';

const API_URL = (import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api').replace(/\/$/, '');
const TOKEN_KEY = 'campus_portal_token';
export const UNAUTHORIZED_EVENT = 'campus-portal:unauthorized';

export const authToken = {
  get: () => localStorage.getItem(TOKEN_KEY),
  set: (token) => localStorage.setItem(TOKEN_KEY, token),
  clear: () => localStorage.removeItem(TOKEN_KEY),
};

export class ApiError extends Error {
  constructor(message, status, errors = {}) {
    super(message);
    this.name = 'ApiError';
    this.status = status;
    this.errors = errors;
  }
}

export const http = axios.create({
  baseURL: API_URL,
  headers: { Accept: 'application/json' },
});

http.interceptors.request.use((config) => {
  const token = authToken.get();
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});

http.interceptors.response.use(
  (response) => response.data,
  (error) => {
    const status = error.response?.status;
    const payload = error.response?.data;

    if (status === 401) {
      authToken.clear();
      window.dispatchEvent(new Event(UNAUTHORIZED_EVENT));
    }

    return Promise.reject(new ApiError(
      payload?.message ?? (error.response ? 'The request could not be completed.' : 'Unable to reach the server.'),
      status,
      payload?.errors ?? {},
    ));
  },
);

export function api(path, config = {}) {
  return http.request({ url: path, ...config });
}

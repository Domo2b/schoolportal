import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { api, authToken, UNAUTHORIZED_EVENT } from '../lib/api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  const refreshUser = useCallback(async () => {
    if (!authToken.get()) {
      setLoading(false);
      return null;
    }
    try {
      const payload = await api('/me');
      setUser(payload.data);
      return payload.data;
    } catch {
      authToken.clear();
      setUser(null);
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  // The initial token check intentionally hydrates React state after mount.
  // eslint-disable-next-line react-hooks/set-state-in-effect
  useEffect(() => { refreshUser(); }, [refreshUser]);

  useEffect(() => {
    const clearSession = () => setUser(null);
    window.addEventListener(UNAUTHORIZED_EVENT, clearSession);
    return () => window.removeEventListener(UNAUTHORIZED_EVENT, clearSession);
  }, []);

  const login = async (credentials) => {
    const payload = await api('/login', { method: 'POST', data: credentials });
    authToken.set(payload.token);
    setUser(payload.user);
    return payload.user;
  };

  const logout = async () => {
    try { await api('/logout', { method: 'POST' }); } finally {
      authToken.clear();
      setUser(null);
    }
  };

  const value = useMemo(() => ({ user, loading, login, logout, refreshUser, isAdmin: user?.role === 'admin' }), [user, loading, refreshUser]);
  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

// This module keeps the provider and its companion hook together.
// eslint-disable-next-line react-refresh/only-export-components
export function useAuth() {
  const value = useContext(AuthContext);
  if (!value) throw new Error('useAuth must be used inside AuthProvider.');
  return value;
}

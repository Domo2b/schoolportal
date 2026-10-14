import { useState } from 'react';
import { useAuth } from '../context/AuthContext';
import { api } from '../lib/api';

export default function ProfilePage() {
  const { user, refreshUser } = useAuth();
  const [form, setForm] = useState({ name: user.name, email: user.email });
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');
  const submit = async (event) => { event.preventDefault(); setError(''); setMessage(''); try { await api('/profile', { method: 'POST', data: form }); await refreshUser(); setMessage('Profile updated.'); } catch (e) { setError(Object.values(e.errors ?? {}).flat()[0] ?? e.message); } };
  return <section className="panel"><div className="page-heading"><div><h1>My profile</h1><p>Manage your account details</p></div></div>{message && <div className="alert success-alert">{message}</div>}{error && <div className="alert error-alert">{error}</div>}<form className="form-card" onSubmit={submit}><label>Name<input required value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} /></label><label>Email<input type="email" required value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} /></label><div className="form-actions"><button>Save profile</button></div></form></section>;
}

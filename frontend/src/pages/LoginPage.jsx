import { useState } from 'react';
import { Navigate, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function LoginPage() {
  const { user, login } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [form, setForm] = useState({ identifier: '', password: '' });
  const [error, setError] = useState('');
  const [submitting, setSubmitting] = useState(false);
  if (user) return <Navigate to="/" replace />;

  const submit = async (event) => {
    event.preventDefault();
    setError(''); setSubmitting(true);
    try {
      await login({ ...form, device_name: 'campus-portal-web' });
      navigate(location.state?.from?.pathname ?? '/', { replace: true });
    } catch (requestError) {
      setError(requestError.errors?.identifier?.[0] ?? requestError.message);
    } finally { setSubmitting(false); }
  };

  return <main className="auth-page"><section className="auth-card">
    <div className="brand auth-brand">CampusPortal</div><h1>Sign in</h1><p>Administrators use email; students can use their matric number.</p>
    {error && <div className="alert error-alert" role="alert">{error}</div>}
    <form onSubmit={submit} className="auth-form">
      <label>Email or matric number<input required autoComplete="username" value={form.identifier} onChange={(e) => setForm({ ...form, identifier: e.target.value })} /></label>
      <label>Password<input type="password" required autoComplete="current-password" value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} /></label>
      <button disabled={submitting}>{submitting ? 'Signing in…' : 'Sign in'}</button>
    </form>
  </section></main>;
}
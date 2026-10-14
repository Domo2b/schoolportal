import { Link } from 'react-router-dom';
export default function UnauthorizedPage() {
  return <main className="auth-page"><section className="auth-card"><h1>Access denied</h1><p>Your account does not have permission to open that page.</p><Link className="button-link" to="/">Return to dashboard</Link></section></main>;
}

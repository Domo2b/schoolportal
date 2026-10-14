import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

export default function Header() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const initials = user.name.split(' ').map((part) => part[0]).join('').slice(0, 2).toUpperCase();
  const signOut = async () => { await logout(); navigate('/login', { replace: true }); };
  return <header className="topbar"><div><small>{user.role === 'admin' ? 'Administrator portal' : 'Student portal'}</small><h1>Welcome, {user.name}</h1></div>
    <div className="user-actions">{user.avatar_url ? <img className="avatar" src={user.avatar_url} alt="" /> : <div className="avatar">{initials}</div>}<button className="secondary" type="button" onClick={signOut}>Sign out</button></div>
  </header>;
}

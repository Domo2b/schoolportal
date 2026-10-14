import { NavLink, Outlet } from 'react-router-dom';
import Header from '../components/Header';
import Breadcrumb from '../components/Breadcrumb';
import { useAuth } from '../context/AuthContext';

export default function PortalLayout() {
  const { isAdmin } = useAuth();
  const links = [['/', 'Dashboard'], ...(isAdmin ? [['/students', 'Students']] : []), ['/courses', 'Courses'], ['/enrolments', isAdmin ? 'Enrolments' : 'My enrolments'], ['/profile', 'My profile']];
  return <div className="app-shell"><aside className="sidebar"><div className="brand">CampusPortal</div>
    {links.map(([to, label]) => <NavLink key={to} end={to === '/'} className="nav-link" to={to}>{label}</NavLink>)}
  </aside><main className="main"><Header /><Breadcrumb /><Outlet /></main></div>;
}

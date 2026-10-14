import { useEffect, useState } from 'react';
import StatCard from '../components/StatCard';
import { useAuth } from '../context/AuthContext';
import { api } from '../lib/api';

export default function Dashboard() {
  const { isAdmin, user } = useAuth();
  const [stats, setStats] = useState(null);
  const [error, setError] = useState('');
  useEffect(() => { api('/dashboard').then(setStats).catch((e) => setError(e.message)); }, []);
  if (error) return <div className="alert error-alert">{error}</div>;
  if (!stats) return <div className="page-state">Loading dashboard…</div>;
  const cards = isAdmin ? [['Total Students', stats.students], ['Active Students', stats.active_students], ['Active Courses', stats.courses], ['Enrolments', stats.enrolments]] : [['My Courses', stats.courses], ['Current Enrolments', stats.enrolments], ['Completed Courses', stats.completed_courses]];
  return <><section className="cards">{cards.map(([label, value]) => <StatCard key={label} label={label} value={value} helper="Live portal data" />)}</section><section className="panel"><h1>{isAdmin ? 'Administration overview' : 'Student overview'}</h1><p>Signed in as {user.email}. The figures above are loaded from the Laravel API.</p></section></>;
}

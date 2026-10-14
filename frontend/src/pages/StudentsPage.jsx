import { useCallback, useEffect, useState } from 'react';
import StudentForm from '../components/StudentForm';
import StudentTable from '../components/StudentTable';
import Modal from '../components/Modal';
import { api } from '../lib/api';

const fromApi = (student) => ({ ...student, matricNo: student.matric_no, status: student.status[0].toUpperCase() + student.status.slice(1) });
const toApi = (student) => ({ name: student.name, email: student.email, matric_no: student.matricNo, department: student.department, level: Number(student.level), status: student.status.toLowerCase(), ...(student.password ? { password: student.password } : {}) });

export default function StudentsPage() {
  const [students, setStudents] = useState([]); const [search, setSearch] = useState(''); const [status, setStatus] = useState('');
  const [editing, setEditing] = useState(null); const [showForm, setShowForm] = useState(false); const [error, setError] = useState(''); const [loading, setLoading] = useState(true);
  const load = useCallback(async () => { setLoading(true); setError(''); try { const payload = await api('/students', { params: { search: search || undefined, status: status.toLowerCase() || undefined, per_page: 100 } }); setStudents(payload.data.map(fromApi)); } catch (e) { setError(e.message); } finally { setLoading(false); } }, [search, status]);
  useEffect(() => { const timer = setTimeout(load, 250); return () => clearTimeout(timer); }, [load]);
  const save = async (form, { avatar, removeAvatar }) => { setError(''); try { const student = toApi(form); if (editing) { const data = new FormData(); Object.entries(student).forEach(([key, value]) => data.append(key, value)); data.append('_method', 'PATCH'); if (avatar) data.append('avatar', avatar); if (removeAvatar) data.append('remove_avatar', '1'); await api(`/students/${editing.id}`, { method: 'POST', data }); } else { await api('/students', { method: 'POST', data: student }); } setEditing(null); setShowForm(false); await load(); } catch (e) { setError(Object.values(e.errors ?? {}).flat()[0] ?? e.message); } };
  const remove = async (id) => { if (!window.confirm('Remove this student?')) return; try { await api(`/students/${id}`, { method: 'DELETE' }); await load(); } catch (e) { setError(e.message); } };
  return <section className="panel"><div className="page-heading"><div><h1>Students</h1><p>Manage student accounts and records</p></div><button onClick={() => { setEditing(null); setError(''); setShowForm(true); }}>Add student</button></div>
    {error && !showForm && <div className="alert error-alert">{error}</div>}{showForm && <Modal title={editing ? 'Edit student' : 'Add student'} onClose={() => setShowForm(false)}>{error && <div className="alert error-alert">{error}</div>}<StudentForm key={editing?.id ?? 'new'} initialValue={editing} onSave={save} onCancel={() => setShowForm(false)} students={students} editingId={editing?.id} /></Modal>}
    <div className="toolbar"><label>Search<input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Name, email or matric number" /></label><label>Status<select value={status} onChange={(e) => setStatus(e.target.value)}><option value="">All</option><option>Active</option><option>Inactive</option></select></label></div>
    {loading ? <div className="page-state">Loading students…</div> : <StudentTable students={students} onEdit={(student) => { setEditing(student); setError(''); setShowForm(true); }} onRemove={remove} />}
  </section>;
}

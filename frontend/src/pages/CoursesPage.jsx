import { useCallback, useEffect, useState } from "react";
import { useAuth } from "../context/AuthContext";
import { api } from "../lib/api";
import Modal from "../components/Modal";

const empty = {
  code: "",
  title: "",
  description: "",
  credit_unit: 3,
  status: "active",
};
export default function CoursesPage() {
  const { isAdmin } = useAuth();
  const [courses, setCourses] = useState([]);
  const [search, setSearch] = useState("");
  const [form, setForm] = useState(empty);
  const [editing, setEditing] = useState(null);
  const [showForm, setShowForm] = useState(false);
  const [error, setError] = useState("");
  const load = useCallback(async () => {
    try {
      const payload = await api("/courses", {
        params: { search: search || undefined, per_page: 100 },
      });
      setCourses(payload.data);
    } catch (e) {
      setError(e.message);
    }
  }, [search]);
  useEffect(() => {
    const timer = setTimeout(load, 250);
    return () => clearTimeout(timer);
  }, [load]);
  const open = (course = null) => {
    setEditing(course);
    setError("");
    setForm(
      course
        ? {
            code: course.code,
            title: course.title,
            description: course.description ?? "",
            credit_unit: course.credit_unit,
            status: course.status,
          }
        : empty,
    );
    setShowForm(true);
  };
  const submit = async (event) => {
    event.preventDefault();
    try {
      await api(editing ? `/courses/${editing.id}` : "/courses", {
        method: editing ? "PATCH" : "POST",
        data: { ...form, credit_unit: Number(form.credit_unit) },
      });
      setShowForm(false);
      await load();
    } catch (e) {
      setError(Object.values(e.errors ?? {}).flat()[0] ?? e.message);
    }
  };
  const remove = async (id) => {
    if (!window.confirm("Remove or archive this course?")) return;
    try {
      await api(`/courses/${id}`, { method: "DELETE" });
      await load();
    } catch (e) {
      setError(e.message);
    }
  };
  return (
    <section className="panel">
      <div className="page-heading">
        <div>
          <h1>Courses</h1>
          <p>
            {isAdmin
              ? "Create and manage available courses"
              : "Browse available courses"}
          </p>
        </div>
        {isAdmin && <button onClick={() => open()}>Add course</button>}
      </div>
      {error && !showForm && <div className="alert error-alert">{error}</div>}
      {showForm && (
        <Modal
          title={editing ? "Edit course" : "Add course"}
          onClose={() => setShowForm(false)}
        >
          <form className="form-card modal-form" onSubmit={submit}>
            <label>
              Course code
              <input
                required
                value={form.code}
                onChange={(e) => setForm({ ...form, code: e.target.value })}
              />
            </label>
            <label>
              Title
              <input
                required
                value={form.title}
                onChange={(e) => setForm({ ...form, title: e.target.value })}
              />
            </label>
            <label>
              Credit units
              <input
                type="number"
                min="1"
                max="12"
                required
                value={form.credit_unit}
                onChange={(e) =>
                  setForm({ ...form, credit_unit: e.target.value })
                }
              />
            </label>
            <label>
              Status
              <select
                value={form.status}
                onChange={(e) => setForm({ ...form, status: e.target.value })}
              >
                <option value="active">Active</option>
                <option value="archived">Archived</option>
              </select>
            </label>
            <label className="wide-field">
              Description
              <textarea
                value={form.description}
                onChange={(e) =>
                  setForm({ ...form, description: e.target.value })
                }
              />
            </label>
            <div className="form-actions">
              <button
                type="button"
                className="secondary"
                onClick={() => setShowForm(false)}
              >
                Cancel
              </button>
              <button>Save course</button>
            </div>
          </form>
        </Modal>
      )}
      <div className="toolbar single-control">
        <label>
          Search courses
          <input value={search} onChange={(e) => setSearch(e.target.value)} />
        </label>
      </div>
      <div className="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Code</th>
              <th>Title</th>
              <th>Units</th>
              <th>Status</th>
              {isAdmin && <th>Actions</th>}
            </tr>
          </thead>
          <tbody>
            {courses.map((course) => (
              <tr key={course.id}>
                <td>
                  <strong>{course.code}</strong>
                </td>
                <td>{course.title}</td>
                <td>{course.credit_unit}</td>
                <td>
                  <span className={`badge ${course.status}`}>
                    {course.status}
                  </span>
                </td>
                {isAdmin && (
                  <td className="table-actions">
                    <button className="secondary" onClick={() => open(course)}>
                      Edit
                    </button>
                    <button
                      className="danger"
                      onClick={() => remove(course.id)}
                    >
                      Remove
                    </button>
                  </td>
                )}
              </tr>
            ))}
          </tbody>
        </table>
        {!courses.length && <div className="empty">No courses found.</div>}
      </div>
    </section>
  );
}

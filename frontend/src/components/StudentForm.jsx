import { useState } from "react";

const emptyForm = {
  name: "",
  email: "",
  matricNo: "",
  department: "",
  level: "100",
  status: "Active",
  password: "",
};

const StudentForm = ({
  initialValue,
  onSave,
  onCancel,
  students = [],
  editingId,
}) => {
  const [form, setForm] = useState(initialValue ?? emptyForm);
  const [errors, setErrors] = useState({});
  const [avatar, setAvatar] = useState(null);
  const [removeAvatar, setRemoveAvatar] = useState(false);
  const change = (e) =>
    setForm((current) => ({ ...current, [e.target.name]: e.target.value }));
  const submit = (e) => {
    e.preventDefault();
    const next = {};
    if (!form.name.trim()) next.name = "Name is required.";
    if (!/^\S+@\S+\.\S+$/.test(form.email)) next.email = "Enter a valid email.";
    if (!form.matricNo.trim()) next.matricNo = "Matric number is required.";
    if (!initialValue && form.password.length < 8) next.password = "Set an initial password of at least 8 characters.";
    if (initialValue && form.password && form.password.length < 8) next.password = "A new password must be at least 8 characters.";
    const duplicate = students.some(
      (student) =>
        student.id !== editingId &&
        student.matricNo.toLowerCase() === form.matricNo.trim().toLowerCase()
    );
    if (duplicate) next.matricNo = "Matric number already exists.";
    setErrors(next);
    if (Object.keys(next).length) return;
    onSave(form, { avatar, removeAvatar });
  };
  return (
    <form className="form-card modal-form" onSubmit={submit} noValidate>
      {["name", "email", "matricNo", "department"].map((field) => (
        <label key={field}>
          {field}
          <input name={field} value={form[field]} onChange={change} />
          {errors[field] && <small className="error">{errors[field]}</small>}
        </label>
      ))}
      <label>
        Password {initialValue && "(leave blank to keep current)"}
        <input name="password" type="password" required={!initialValue} minLength="8" value={form.password ?? ""} onChange={change} />
        {errors.password && <small className="error">{errors.password}</small>}
      </label>
      {initialValue && <label className="avatar-field">
        Profile picture
        {initialValue.avatar_url && !removeAvatar && <img className="student-avatar-preview" src={initialValue.avatar_url} alt={`${initialValue.name}'s profile`} />}
        <input name="avatar" type="file" accept="image/jpeg,image/png,image/webp" onChange={(event) => { setAvatar(event.target.files?.[0] ?? null); setRemoveAvatar(false); }} />
        <small>JPG, PNG or WebP, up to 2 MB.</small>
        {initialValue.avatar_url && <span className="checkbox-field"><input type="checkbox" checked={removeAvatar} onChange={(event) => { setRemoveAvatar(event.target.checked); if (event.target.checked) setAvatar(null); }} /> Remove current picture</span>}
      </label>}
      <label>
        Level
        <select name="level" value={form.level} onChange={change}>
          <option>100</option>
          <option>200</option>
          <option>300</option>
          <option>400</option>
        </select>
      </label>
      <label>
        Status
        <select name="status" value={form.status} onChange={change}>
          <option>Active</option>
          <option>Inactive</option>
        </select>
      </label>
      <div className="form-actions">
        <button type="button" className="secondary" onClick={onCancel}>
          Cancel
        </button>
        <button>Save student</button>
      </div>
    </form>
  );
};

export default StudentForm;

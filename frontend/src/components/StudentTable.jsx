const StudentTable = ({ students, onEdit, onRemove }) => {
    if( students.length === 0) {
        return <div className="empty">No student data available</div>
    }
  return (
    <div className="table-wrap"> 
      <table>
        <thead>
          <tr>
            <th>Student Name</th>
            <th>Matric No.</th>
            <th>Courses</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
            {students.map(student => <tr key={student.id}>
                <td><span className="student-name">{student.avatar_url ? <img className="student-avatar" src={student.avatar_url} alt="" /> : <span className="student-avatar avatar-fallback">{student.name.charAt(0)}</span>}{student.name}</span></td>
                <td>{student.matricNo}</td>
                <td>{student.department || student.course}</td>
                <td><span className={`badge ${student.status.toLowerCase()}`}>{student.status}</span></td>
                <td className="table-actions">
                  <button type="button" className="secondary" onClick={() => onEdit(student)}>Edit</button>
                  <button type="button" className="danger" onClick={() => onRemove(student.id)}>Remove</button>
                </td>

            </tr>)}
        </tbody>
      </table>
    </div>
  )
}

export default StudentTable

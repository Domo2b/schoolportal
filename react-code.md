import { useState, useEffect} from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from './assets/vite.svg'
import heroImg from './assets/hero.png'
import './App.css'
import Person from './assets/Person'
import Animal from './assets/Animal'


function App() {
  const initialStudents = [
    { id: 1, name: 'John', age: 20, score: 85, address: '123 Main St' },
    { id: 2, name: 'Jane', age: 22, score: 90, address: '456 Elm St' },
    { id: 3, name: 'Bob', age: 19, score: 45, address: '789 Oak St' },
  ];

  const [students, setStudents] = useState(initialStudents);
  const [searchTerm, setSearchTerm] = useState('');
  const [scoreFilter, setScoreFilter] = useState('all');

  // Students who scored 50 and above
  const passed = students.filter((student) => student.score >= 50);

  // Increase Bob's score by 10
  const improveBobScore = () => {
    const improved = students.map((student) =>
      student.id === 3
        ? { ...student, score: student.score + 10 }
        : student
    );

    setStudents(improved);
  };

  // Add a new student
  const addStudent = () => {
    const newStudent = {
      id: Date.now(),
      name: 'Grace',
      age: 21,
      score: 78,
      address: '10 Lagos Road',
    };

    setStudents([...students, newStudent]);
  };

  // Update John's age
  const updateJohnAge = () => {
    const updatedStudents = students.map((student) =>
      student.id === 1
        ? { ...student, age: student.age + 1 }
        : student
    );

    setStudents(updatedStudents);
  };

  // Search students by name
  const searchedStudents = students.filter((student) =>
    student.name.toLowerCase().includes(searchTerm.toLowerCase())
  );

  // Filter students by score
  const filteredStudents = searchedStudents.filter((student) => {
    if (scoreFilter === 'passed') {
      return student.score >= 50;
    }

    if (scoreFilter === 'failed') {
      return student.score < 50;
    }

    return true;
  });

  return (
    <div className="app">
      <h1>Student Management</h1>

      <section
        style={{
          marginTop: '10px',
          marginBottom: '20px',
          borderBottom: '1px solid #ccc',
          paddingBottom: '20px',
          backgroundColor: '#3c2929',
          borderRadius: '8px',
          padding: '60px',
        }}
      >
        <h2>All Students</h2>

        {students.map((student) => (
          <Person
            key={student.id}
            name={student.name}
            score={student.score}
          />
        ))}
<div style={{
  display: 'flex',
  gap: '30px',
  marginTop: '10px',
}}>
        <button onClick={improveBobScore}style={{
  padding: '10px 20px',
  backgroundColor: '#4CAF50',
  color: 'white',
  border: 'none',
  borderRadius: '4px',
  cursor: 'pointer',
}}>
          Improve Bob's Score
        </button>

        <button onClick={addStudent} style={{
  padding: '10px 20px',
  backgroundColor: '#2196F3',
  color: 'white',
  border: 'none',
  borderRadius: '4px',
  cursor: 'pointer',
}}>
          Add Grace
        </button>
  
        <button onClick={updateJohnAge} style={{
  padding: '10px 20px',
  backgroundColor: '#FF9800',
  color: 'white',
  border: 'none',
  borderRadius: '4px',
  cursor: 'pointer',
}}>
          Increase John's Age
        </button>

      </div>
      </section>

      <section>
        <h2>Passed Students</h2>

        {passed.length > 0 ? (
          passed.map((student) => (
            <Person
              key={student.id}
              name={student.name}
              score={student.score}
              age={student.age}
            />
          ))
        ) : (
          <p>No student passed.</p>
        )}
      </section>

      <section>
        <h2>Search and Filter Students</h2>

        <input
          type="text"
          placeholder="Search student by name"
          value={searchTerm}
          onChange={(event) => setSearchTerm(event.target.value)}
        />

        <select
          value={scoreFilter}
          onChange={(event) => setScoreFilter(event.target.value)}
        >
          <option value="all">All Students</option>
          <option value="passed">Passed</option>
          <option value="failed">Failed</option>
        </select>

        {filteredStudents.length > 0 ? (
          filteredStudents.map((student) => (
            <div key={student.id}>
              <Person
                name={student.name}
                score={student.score}
                age={student.age}
              />

          
              <p>Address: {student.address}</p>
            </div>
          ))
        ) : (
          <p>No student found.</p>
        )}
      </section>
    </div>
  );
}

export default App;


import React from 'react'

const Person = ({ name, score, age}) => {
  const status = score >= 50 ? 'Passed' : 'Failed';
  return (
    
    <div className='person' style= {{
      border: '1px solid #ccc',
      padding: '10px',}}>
      <h1>{name}, Score: {score}, Status: {status}, Age: {age}</h1>

    </div>

  )
}


export default Person

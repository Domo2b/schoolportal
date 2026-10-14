import { Route, Routes } from 'react-router-dom';
import Aside from './components/Aside';
import Header from './components/Header';
import './styles/index.css';
import Dashboard from './pages/Dashboard';
import StudentsPage from './pages/StudentsPage';
import initialStudents from './data/students';


const App = () => {
  return (
    <div className='app-shell'>
      <Aside />
      <main className='main'>
        <Header />

        <Routes>
          <Route path='/' element={<Dashboard />} />
          <Route path='/students' element={<StudentsPage students={initialStudents} />} />
        </Routes>
      </main>
    </div>
  );
};

export default App;

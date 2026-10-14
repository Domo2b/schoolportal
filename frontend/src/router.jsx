import { createBrowserRouter } from 'react-router-dom';
import ProtectedRoute from './components/ProtectedRoute';
import PortalLayout from './layouts/PortalLayout';
import CoursesPage from './pages/CoursesPage';
import Dashboard from './pages/Dashboard';
import EnrolmentsPage from './pages/EnrolmentPage';
import LoginPage from './pages/LoginPage';
import ProfilePage from './pages/ProfilePage';
import StudentsPage from './pages/StudentsPage';
import UnauthorizedPage from './pages/UnauthorizedPage';

export const router = createBrowserRouter([
  { path: '/login', element: <LoginPage /> },
  { path: '/unauthorized', element: <UnauthorizedPage /> },
  { element: <ProtectedRoute />, children: [{ path: '/', element: <PortalLayout />, children: [
    { index: true, element: <Dashboard /> },
    { path: 'courses', element: <CoursesPage /> },
    { path: 'enrolments', element: <EnrolmentsPage /> },
    { path: 'profile', element: <ProfilePage /> },
    { element: <ProtectedRoute roles={['admin']} />, children: [{ path: 'students', element: <StudentsPage /> }] },
  ] }] },
  { path: '*', element: <div className="not-found"><h1>404</h1><p>Page not found.</p></div> },
]);

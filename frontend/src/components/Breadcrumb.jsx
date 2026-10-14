import { Link, useLocation } from 'react-router-dom';

const pageNames = {
  students: 'Students',
  courses: 'Courses',
  enrolments: 'Enrolments',
};

const Breadcrumb = () => {
  const { pathname } = useLocation();
  const page = pageNames[pathname.split('/').filter(Boolean)[0]];

  return (
    <nav className="breadcrumb" aria-label="Breadcrumb">
      {page ? <><Link to="/">Dashboard</Link>
      <span aria-hidden="true">/</span>
      <span aria-current="page">{page}</span></> : <span aria-current="page">Dashboard</span>}
    </nav>
  );
};

export default Breadcrumb;

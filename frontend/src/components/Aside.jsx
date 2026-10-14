import { NavLink } from 'react-router-dom';

const Aside = () => {
  return (
    <aside className='sidebar'>
      <div className='brand'>CampusPortal</div>

      <NavLink className='nav-link' to='/'>
        Dashboard
      </NavLink>

      <NavLink className='nav-link' to='/students'>
        Students
      </NavLink>
    </aside>
  );
};

export default Aside;
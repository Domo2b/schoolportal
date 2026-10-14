const StatCard = ({label, value, helper}) => {
  return (
    <div className="card">
      <span>{label}</span>
      <div className="stat">{value}</div>
      <small>{helper}</small>
    </div>
  )
}

export default StatCard

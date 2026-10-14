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

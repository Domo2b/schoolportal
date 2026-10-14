import React from 'react'

const Animal = ({name, species, age, height, weight}) => {
  return (
    <div>
      <h1>This is {name}</h1>
      <p>Species: {species}</p>
      <p>Age: {age}</p>
      <p>Height: {height}</p>
      <p>Weight: {weight}</p>
    </div>
  )
}

export default Animal

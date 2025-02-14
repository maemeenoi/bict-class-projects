function AddUser({ onClick }) {
  const [firstName, setFirstName] = React.useState("")
  const [lastName, setLastName] = React.useState("")
  const [birthDate, setBirthDate] = React.useState("")

  const handleAddUser = (e) => {
    e.preventDefault()
    onClick(firstName, lastName, birthDate)
    setFirstName("")
    setLastName("")
    setBirthDate("")
  }

  return (
    <>
      <h2>Add User</h2>
      <div>
        <input
          type="text"
          value={firstName}
          onChange={(e) => setFirstName(e.target.value)}
          placeholder="First Name"
        />
        <input
          type="text"
          value={lastName}
          onChange={(e) => setLastName(e.target.value)}
          placeholder="Last Name"
        />
        <input
          type="date"
          value={birthDate}
          onChange={(e) => setBirthDate(e.target.value)}
          placeholder="Birth Date"
        />
        <button onClick={handleAddUser}>Add User</button>
      </div>
    </>
  )
}

export default AddUser

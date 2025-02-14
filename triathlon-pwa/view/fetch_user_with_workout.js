function FetchUserWithWorkout({
  onFetchUser,
  onGetTotalDuration,
  onGetTotalDistance,
  onUpdate,
  onDelete,
}) {
  const [firstName, setFirstName] = React.useState("")
  const [lastName, setLastName] = React.useState("")
  const [birthDate, setBirthDate] = React.useState("")
  const [userId, setUserId] = React.useState("")
  const [userDetails, setUserDetails] = React.useState(null)
  const [totalDuration, setTotalDuration] = React.useState(null)
  const [totalDistance, setTotalDistance] = React.useState(null)
  const [sortOrder, setSortOrder] = React.useState("asc")
  const [searchTerm, setSearchTerm] = React.useState("")
  const [originalWorkouts, setOriginalWorkouts] = React.useState([])

  const handleFetchUser = async (e) => {
    e.preventDefault()
    try {
      const user = await onFetchUser(userId)
      if (user) {
        console.log("Fetched user:", user)
        setUserDetails(user)
        setOriginalWorkouts(user.workouts) // Assuming workouts are a direct property
        setTotalDuration(await onGetTotalDuration(Number(userId)))
        setTotalDistance(await onGetTotalDistance(Number(userId)))
      } else {
        alert(`No user found with ID: ${userId}`)
        console.log("No user found with ID:", userId)
      }
    } catch (error) {
      console.error("Failed to fetch user:", error)
    }
  }

  const handleUpdateUser = (e) => {
    e.preventDefault()
    const updatedData = {
      firstName: firstName || undefined,
      lastName: lastName || undefined,
      birthDate: birthDate ? new Date(birthDate).toISOString() : undefined,
    }
    onUpdate(Number(userId), updatedData, () => {
      console.log(`User with ID ${userId} has been updated.`)
      handleFetchUser(e) // Refresh the user details after update
    })
    location.reload()
  }

  const handleDeleteUser = async () => {
    if (userId && !isNaN(Number(userId))) {
      if (
        window.confirm(`Are you sure you want to delete user ID ${userId}?`)
      ) {
        await onDelete(Number(userId))
        alert("User deleted successfully.")
        setUserId("")
        setUserDetails(null)
        setTotalDuration(null)
        setTotalDistance(null)
      }
    } else {
      alert("Please enter a valid User ID.")
    }
  }

  const handleCancelUpdate = () => {
    setFirstName("")
    setLastName("")
    setBirthDate("")
  }

  const handleSearch = () => {
    if (userDetails) {
      const filteredWorkouts = userDetails.workouts.filter((workout) =>
        workout.exercise.toLowerCase().includes(searchTerm.toLowerCase())
      )
      setUserDetails({ ...userDetails, workouts: filteredWorkouts })
    }
  }

  const handleCancelSearch = () => {
    setUserDetails({ ...userDetails, workouts: originalWorkouts })
    setSearchTerm("")
  }

  const handleSortWorkouts = (order) => {
    if (userDetails) {
      const sortedWorkouts = [...userDetails.workouts].sort((a, b) => {
        if (order === "asc") {
          return new Date(a.date) - new Date(b.date)
        } else {
          return new Date(b.date) - new Date(a.date)
        }
      })
      setUserDetails({ ...userDetails, workouts: sortedWorkouts })
      setSortOrder(order)
    }
  }

  const handleClose = () => {
    setUserId("")
    setUserDetails(null)
    setTotalDuration(null)
    setTotalDistance(null)
    setSearchTerm("")
    location.reload()
  }

  return (
    <>
      <h2>User and Workouts Log</h2>
      <p>Please enter User ID to see the information and workout logs.</p>
      <div>
        <input
          type="text"
          value={userId}
          onChange={(e) => setUserId(e.target.value)}
          placeholder="User ID"
        />
        <button onClick={handleFetchUser}>Fetch User with Workouts</button>
      </div>
      {userDetails && (
        <div>
          <h2>User Details</h2>
          <p>
            Name: {userDetails.firstName} {userDetails.lastName}
          </p>
          <p>Date of Birth: {new Date(userDetails.dob).toDateString()}</p>
          <div>
            <h2>Update User</h2>
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
            <button onClick={handleUpdateUser}>Update User</button>
            <button onClick={handleDeleteUser}>Delete User</button>
            <button onClick={handleCancelUpdate}>Cancel Update</button>
          </div>
          <h3>Workouts</h3>
          <button onClick={() => handleSortWorkouts("asc")}>
            Sort by Date Ascending
          </button>
          <button onClick={() => handleSortWorkouts("desc")}>
            Sort by Date Descending
          </button>
          <input
            type="text"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            placeholder="Search by exercise"
          />
          <button onClick={handleSearch}>Search</button>
          <button onClick={handleCancelSearch}>Cancel</button>
          <ul>
            {userDetails.workouts.map((workout) => (
              <li key={workout.id}>
                {new Date(workout.date).toDateString()} - {workout.exercise}:{" "}
                {workout.duration} mins, {workout.distance} km
              </li>
            ))}
          </ul>
          <h3>Total Duration: {totalDuration} mins</h3>
          <h3>Total Distance: {totalDistance} km</h3>
          <button onClick={handleClose}>Close</button>
        </div>
      )}
    </>
  )
}

export default FetchUserWithWorkout

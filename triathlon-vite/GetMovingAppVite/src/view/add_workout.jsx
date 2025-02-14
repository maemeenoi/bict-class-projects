import { useState } from "react"

function AddWorkout({ onAddWorkout }) {
  const [userId, setUserId] = useState("")
  const [workoutDate, setWorkoutDate] = useState("")
  const [exercise, setExercise] = useState("")
  const [duration, setDuration] = useState("")
  const [distance, setDistance] = useState("")

  const handleAddWorkout = (e) => {
    e.preventDefault()
    onAddWorkout(
      Number(userId),
      workoutDate,
      exercise,
      Number(duration),
      Number(distance)
    )
    setUserId("")
    setWorkoutDate("")
    setExercise("")
    setDuration("")
    setDistance("")
  }

  return (
    <>
      <div>
        <h2>Add Workout</h2>
        <input
          type="text"
          value={userId}
          onChange={(e) => setUserId(e.target.value)}
          placeholder="User ID"
        />
        <input
          type="date"
          value={workoutDate}
          onChange={(e) => setWorkoutDate(e.target.value)}
          placeholder="Workout Date"
        />
        <select value={exercise} onChange={(e) => setExercise(e.target.value)}>
          <option value="">Select Exercise</option>
          <option value="Running">Running</option>
          <option value="Cycling">Cycling</option>
          <option value="Swimming">Swimming</option>
          <option value="Rowing">Rowing</option>
          <option value="Walking">Walking</option>
          <option value="Hiking">Hiking</option>
        </select>
        <input
          type="number"
          value={duration}
          onChange={(e) => setDuration(e.target.value)}
          placeholder="Duration (mins)"
        />
        <input
          type="number"
          value={distance}
          onChange={(e) => setDistance(e.target.value)}
          placeholder="Distance (km)"
        />
        <button onClick={handleAddWorkout}>Add Workout</button>
      </div>
    </>
  )
}

export default AddWorkout

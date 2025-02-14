export default class GetMovingAppViewModel {
  constructor(log) {
    this.log = log
    this.state = {
      firstName: "",
      lastName: "",
      birthDate: "",
    }
  }

  addUser = async (newFirstName, newLastName, newBirthDate) => {
    const user = {
      firstName: newFirstName,
      lastName: newLastName,
      birthDate: new Date(newBirthDate).toISOString(), // ISO string for consistent formatting
    }
    try {
      await this.log.addUser(user)
      alert(
        `User added: ${newFirstName} ${newLastName}. Date of Birth: ${newBirthDate}`
      )
      console.log("User added:", user)
    } catch (error) {
      console.error("Error adding user:", error)
    }
  }

  addWorkoutToUser = async (
    targetUserId,
    date,
    exercise,
    duration,
    distance
  ) => {
    try {
      const user = await this.log.findUser(targetUserId)
      if (user) {
        await this.log.addWorkoutToUser(
          targetUserId,
          date,
          exercise,
          duration,
          distance
        )
        alert(
          `Workout added to user ID: ${targetUserId} with ${exercise} on ${date}. Duration: ${duration} Distance: ${distance}`
        )
        console.log("Workout added to user ID:", targetUserId)
      } else {
        alert(`User with ID ${targetUserId} not found.`)
        console.log(`User with ID ${targetUserId} not found.`)
      }
    } catch (error) {
      console.error("Error adding workout to user:", error)
    }
  }

  getUserWithWorkouts = async (userId) => {
    try {
      const userWithWorkouts = await this.log.getUserWithWorkouts(userId)
      console.log("User with workouts:", userWithWorkouts)
      return userWithWorkouts
    } catch (error) {
      console.error("Error getting user with workouts:", error)
    }
  }

  deleteUser = async (userId) => {
    try {
      await this.log.deleteUser(userId)
      alert(`User with ID ${userId} deleted`)
      console.log(`User with ID ${userId} deleted`)
    } catch (error) {
      console.error("Error deleting user:", error)
    }
  }

  updateUser = async (userId, updatedData) => {
    try {
      await this.log.updateUser(userId, updatedData)
      alert(`User with ID ${userId} updated`)
      console.log(`User with ID ${userId} updated`)
    } catch (error) {
      console.error("Error updating user:", error)
    }
  }

  getTotalDuration = async (userId) => {
    try {
      const totalDuration = await this.log.getTotalDuration(userId)
      console.log(`Total duration for user ID ${userId}:`, totalDuration)
      return totalDuration
    } catch (error) {
      console.error("Error getting total duration:", error)
    }
  }

  getTotalDistance = async (userId) => {
    try {
      const totalDistance = await this.log.getTotalDistance(userId)
      console.log(`Total distance for user ID ${userId}:`, totalDistance)
      return totalDistance
    } catch (error) {
      console.error("Error getting total distance:", error)
    }
  }

  setState = (newState) => {
    this.state = { ...this.state, ...newState }
    console.log("State updated:", this.state)
  }
}

import User from "./user.js"

export default class Log {
  constructor(db) {
    this.allMyUsers = []
    this.db = db
    this.loadFromLocalStorage()
    this.nextUserId = this.nextUserId || 1
  }

  //Local Storage Management
  loadFromLocalStorage() {
    const dataString = localStorage.getItem("workoutLogData")
    if (dataString) {
      const usersArray = JSON.parse(dataString)
      this.allMyUsers = usersArray.map((userObj) => {
        const user = new User(
          userObj.id,
          userObj.firstName,
          userObj.lastName,
          userObj.birthDate
        )
        userObj.allMyWorkouts.forEach((workout) => {
          user.addWorkout(
            workout.date,
            workout.exercise,
            workout.duration,
            workout.distance
          )
        })
        return user
      })
      this.nextUserId = Math.max(...this.allMyUsers.map((user) => user.id)) + 1
    }
  }

  saveToLocalStorage() {
    const dataString = JSON.stringify(this.allMyUsers)
    localStorage.setItem("workoutLogData", dataString)
  }

  deleteUserFromLocalStorage(userId) {
    try {
      const users = JSON.parse(localStorage.getItem("workoutLogData"))
      if (users) {
        const updatedUsers = users.filter((user) => user.id !== userId)
        localStorage.setItem("workoutLogData", JSON.stringify(updatedUsers))
        console.log(
          `User with ID ${userId} has been deleted from localStorage.`
        )
      } else {
        console.log("No users found in localStorage.")
      }
    } catch (error) {
      console.error("Error managing users in localStorage:", error)
    }
  }

  // Interact with User.js
  addUser = async (user) => {
    // Ensure that you extract proper fields from the user object
    const newFirstName = user.firstName
    const newLastName = user.lastName
    const newBirthDate = user.birthDate

    // Create a new user instance
    const newUser = new User(
      this.nextUserId++, // Increment the userID for each new user
      newFirstName,
      newLastName,
      newBirthDate // Ensure this is a valid date or handled correctly in the User constructor
    )
    this.allMyUsers.push(newUser)
    this.sortUser()
    this.saveToLocalStorage()
    await this.saveUserToDB(newUser)
  }

  addWorkoutToUser = async (
    targetUserId,
    date,
    exercise,
    duration,
    distance
  ) => {
    const user = this.findUser(targetUserId)
    if (user) {
      user.addWorkout(date, exercise, duration, distance)
      this.saveToLocalStorage()
      await this.db.saveWorkoutToDB(
        targetUserId,
        date,
        exercise,
        duration,
        distance
      )
    } else {
      throw new Error(`User with ID ${targetUserId} not found.`)
    }
  }

  // Database interaction management
  saveUserToDB = async (newUser) => {
    await this.db.saveUserToDB(newUser)
  }

  getUserWithWorkouts = async (userId) => {
    return await this.db.getUserWithWorkouts(userId)
  }

  deleteUser = async (userId) => {
    this.deleteUserFromLocalStorage(userId)
    await this.db.deleteUser(userId)
  }

  updateUser = async (userId, updatedData) => {
    await this.db.updateUser(userId, updatedData)
  }

  findUser = (targetUserId) => {
    return this.allMyUsers.find((user) => user.id === Number(targetUserId))
  }

  sortUser = () => {
    this.allMyUsers.sort((a, b) => a.id - b.id)
  }

  // Calculate totals
  getTotalDuration = async (userId) => {
    const user = await this.getUserWithWorkouts(userId)
    return user
      ? user.workouts.reduce((total, workout) => total + workout.duration, 0)
      : 0
  }

  getTotalDistance = async (userId) => {
    const user = await this.getUserWithWorkouts(userId)
    return user
      ? user.workouts.reduce((total, workout) => total + workout.distance, 0)
      : 0
  }
}

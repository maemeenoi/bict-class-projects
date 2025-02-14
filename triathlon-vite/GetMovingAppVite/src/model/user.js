import Workout from "./workout.js"

export default class User {
  constructor(id, firstName, lastName, birthDate) {
    this.id = id
    this.firstName = firstName
    this.lastName = lastName
    this.birthDate = new Date(birthDate)
    this.allMyWorkouts = []
  }

  sortWorkouts() {
    try {
      this.allMyWorkouts.sort((a, b) =>
        a.date.toISOString().localeCompare(b.date.toISOString())
      )
    } catch (error) {
      console.error("Error sorting workouts: ", error)
    }
  }

  toString() {
    try {
      return `User info: id:${this.id} Name:${this.firstName} ${this.lastName} DOB:${this.birthDate}`
    } catch (error) {
      console.error("Error converting User to string: ", error)
    }
  }

  addWorkout(newDate, newExercise, newDuration, newDistance) {
    try {
      let newWorkout = new Workout(
        this.allMyWorkouts.length + 1,
        newDate,
        newExercise,
        newDuration,
        newDistance
      )
      this.allMyWorkouts.push(newWorkout)
      this.sortWorkouts()
    } catch (error) {
      console.error("Error adding workout: ", error)
    }
  }
}

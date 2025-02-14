export default class Workout {
  constructor(newId, newDate, newExercise, newDuration, newDistance) {
    try {
      this.id = newId
      this.date = new Date(newDate)
      this.exercise = newExercise
      this.duration = parseInt(newDuration, 10)
      this.distance = parseFloat(newDistance)
    } catch (error) {
      console.error("Error creating Workout: ", error)
    }
  }

  toString() {
    try {
      return `On ${this.date}, Your exercise is ${this.exercise} with ${this.duration} minutes`
    } catch (error) {
      console.error("Error converting Workout to string: ", error)
      return "Error converting Workout to string"
    }
  }
}

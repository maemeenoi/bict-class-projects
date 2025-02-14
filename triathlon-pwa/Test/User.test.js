import User from "../Model/User.js"
import Workout from "../Model/Workout.js"

describe("User", () => {
  let user

  beforeEach(() => {
    user = new User(1, "John", "Doe", "1990-01-01")
  })

  test("should initialize correctly", () => {
    expect(user.id).toBe(1)
    expect(user.firstName).toBe("John")
    expect(user.lastName).toBe("Doe")
    expect(user.birthDate).toEqual(new Date("1990-01-01"))
    expect(user.allMyWorkouts).toEqual([])
  })

  test("should add workout correctly", () => {
    user.addWorkout("2022-01-01", "Running", 30, 5)
    expect(user.allMyWorkouts.length).toBe(1)
    expect(user.allMyWorkouts[0].id).toBe(1)
    expect(user.allMyWorkouts[0].exercise).toBe("Running")
  })

  test("should sort workouts correctly", () => {
    user.addWorkout("2022-01-02", "Running", 30, 5)
    user.addWorkout("2022-01-01", "Swimming", 60, 2)
    expect(user.allMyWorkouts[0].exercise).toBe("Swimming") // After sort
  })

  test("toString should return user information correctly", () => {
    const info = user.toString()
    expect(info).toBe(`User info: id:1 Name:John Doe DOB:${user.birthDate}`)
  })

  test("handles errors during sorting workouts", () => {
    // Adding workouts with incorrect date format to induce an error in sorting
    user.allMyWorkouts.push(new Workout(1, "bad-date", "Running", 30, 5))
    console.error = jest.fn() // Mock console.error to verify it was called
    user.sortWorkouts()
    expect(console.error).toHaveBeenCalled()
  })

  test("handles errors during toString method", () => {
    // Force an error by corrupting user data
    user.birthDate = undefined // Corrupt the birthDate to trigger an error in toString
    console.error = jest.fn()
    const info = user.toString()
    expect(console.error).toHaveBeenCalled()
  })

  test("handles errors during adding a workout", () => {
    console.error = jest.fn()
    // Simulate an error by passing an invalid date
    user.addWorkout(null, "Running", 30, 5)
    expect(console.error).toHaveBeenCalled()
  })

  test("handles errors during sorting workouts", () => {
    // Inject an invalid date object that will throw when toISOString is called
    user.allMyWorkouts.push({
      date: {
        toISOString: () => {
          throw new Error("Invalid date")
        },
      },
      exercise: "Running",
      duration: 30,
      distance: 5,
    })

    console.error = jest.fn() // Mock console.error to verify it was called
    user.sortWorkouts()
    expect(console.error).toHaveBeenCalledWith(
      "Error sorting workouts: ",
      expect.any(Error)
    )
  })
  test("handles errors during toString method", () => {
    // Force an error by corrupting internal data
    user.firstName = undefined // Assume this will break the toString logic
    console.error = jest.fn()

    expect(() => user.toString()).toThrow()
    expect(console.error).toHaveBeenCalledWith(
      "Error converting User to string: ",
      expect.any(Error)
    )
  })
  test("handles errors during adding a workout", () => {
    console.error = jest.fn()
    // Simulate an error by tampering with Workout constructor to throw
    const originalWorkout = Workout
    global.Workout = jest.fn().mockImplementation(() => {
      throw new Error("Workout creation failed")
    })

    expect(() => user.addWorkout("2022-01-01", "Running", 30, 5)).toThrow()
    expect(console.error).toHaveBeenCalledWith(
      "Error adding workout: ",
      expect.any(Error)
    )

    // Restore original constructor to avoid side effects in other tests
    global.Workout = originalWorkout
  })
})

import Workout from "../Model/Workout"

describe("Workout class", () => {
  test("handles errors during Workout creation with invalid inputs", () => {
    console.error = jest.fn() // Mock console.error to verify it's called

    const createInvalidWorkout = () =>
      new Workout(1, "not-a-date", "Running", "not-a-number", "not-a-number")

    expect(createInvalidWorkout).toThrow() // Adjust based on actual implementation if it does not throw
    expect(console.error).toHaveBeenCalledWith(
      "Error creating Workout: ",
      expect.any(Error)
    )
  })

  test("creates a Workout instance successfully", () => {
    const workout = new Workout(1, "2022-01-01", "Running", "30", "5")
    expect(workout).toBeDefined()
    expect(workout.id).toBe(1)
    expect(workout.exercise).toBe("Running")
    expect(workout.duration).toBe(30)
    expect(workout.distance).toBe(5.0)
  })
})
describe("Workout.toString method", () => {
  test("correctly converts Workout to string", () => {
    const workout = new Workout(1, "2022-01-01", "Running", 30, 5)
    const description = workout.toString()
    expect(description).toBe(
      "On Fri Jan 01 2022 00:00:00 GMT+0000 (Coordinated Universal Time), Your exercise is Running with 30 minutes"
    )
  })

  test("handles errors when converting Workout to string", () => {
    console.error = jest.fn()

    // Corrupting the date object intentionally
    const workout = new Workout(1, "2022-01-01", "Running", 30, 5)
    workout.date = null // This should cause the toString method to fail

    expect(() => workout.toString()).toThrow()
    expect(console.error).toHaveBeenCalledWith(
      "Error converting Workout to string: ",
      expect.any(Error)
    )
  })

  describe("Workout.toString method", () => {
    test("handles errors when converting Workout to string", () => {
      const workout = new Workout(1, "2022-01-01", "Running", 30, 5)

      // Mocking the Date object's toISOString method to throw an error
      const originalToISOString = Date.prototype.toISOString
      Date.prototype.toISOString = jest.fn().mockImplementation(() => {
        throw new Error("Date conversion failed")
      })

      console.error = jest.fn() // Spy on console.error

      expect(workout.toString()).toBe("Error converting Workout to string")
      expect(console.error).toHaveBeenCalledWith(
        "Error converting Workout to string: ",
        expect.any(Error)
      )

      // Restore original function to prevent side effects in other tests
      Date.prototype.toISOString = originalToISOString
    })
  })
})

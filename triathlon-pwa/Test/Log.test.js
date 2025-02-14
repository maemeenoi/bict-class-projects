import Log from "../Model/Log"
import User from "../Model/User"
import GetMovingDB from "../Model/Database"

jest.mock("../Model/User")
jest.mock("../Model/Database")

describe("Log", () => {
  let log, dbMock, userMock

  beforeEach(() => {
    dbMock = new GetMovingDB()
    log = new Log(dbMock)
    userMock = new User()
    User.mockClear() // Clear instances and calls to constructor and all methods
  })

  afterEach(() => {
    localStorage.clear()
    jest.restoreAllMocks()
  })

  test("loads users from localStorage", () => {
    const users = [
      {
        id: 1,
        firstName: "John",
        lastName: "Doe",
        birthDate: "1990-01-01",
        allMyWorkouts: [],
      },
    ]
    localStorage.setItem("workoutLogData", JSON.stringify(users))
    log.loadFromLocalStorage()
    expect(log.allMyUsers.length).toBe(1)
    expect(User).toHaveBeenCalledTimes(1)
  })

  test("saves users to localStorage", () => {
    log.allMyUsers = [
      { id: 1, firstName: "John", lastName: "Doe", birthDate: "1990-01-01" },
    ]
    log.saveToLocalStorage()
    expect(localStorage.setItem).toHaveBeenCalledWith(
      "workoutLogData",
      JSON.stringify(log.allMyUsers)
    )
  })

  test("adds a user correctly", async () => {
    const user = {
      firstName: "John",
      lastName: "Doe",
      birthDate: "1990-01-01",
    }
    await log.addUser(user)
    expect(log.allMyUsers.length).toBe(1)
    expect(dbMock.saveUserToDB).toHaveBeenCalledWith(expect.any(User))
  })

  test("deletes a user from both local storage and database", async () => {
    log.allMyUsers = [
      { id: 1, firstName: "John", lastName: "Doe", birthDate: "1990-01-01" },
    ]
    log.saveToLocalStorage()
    await log.deleteUser(1)
    expect(localStorage.setItem).toHaveBeenCalled()
    expect(dbMock.deleteUser).toHaveBeenCalledWith(1)
  })

  test("handles missing user in addWorkoutToUser", async () => {
    const workout = {
      date: "2020-01-01",
      exercise: "Running",
      duration: 30,
      distance: 5,
    }
    await expect(
      log.addWorkoutToUser(
        999,
        workout.date,
        workout.exercise,
        workout.duration,
        workout.distance
      )
    ).rejects.toThrow("User with ID 999 not found.")
  })

  test("updates a user correctly", async () => {
    const updatedData = { firstName: "Jane" }
    log.allMyUsers.push(new User(1, "John", "Doe", "1990-01-01"))
    await log.updateUser(1, updatedData)
    expect(dbMock.updateUser).toHaveBeenCalledWith(1, updatedData)
  })

  test("finds a user correctly", () => {
    log.allMyUsers = [
      { id: 1, firstName: "John", lastName: "Doe", birthDate: "1990-01-01" },
    ]
    const foundUser = log.findUser(1)
    expect(foundUser).toBeDefined()
    expect(foundUser.id).toBe(1)
  })

  test("calculates total duration for a user", async () => {
    const userId = 1
    dbMock.getUserWithWorkouts.mockResolvedValue({
      workouts: [{ duration: 30 }, { duration: 20 }],
    })
    const totalDuration = await log.getTotalDuration(userId)
    expect(totalDuration).toBe(50)
  })

  test("calculates total distance for a user", async () => {
    const userId = 1
    dbMock.getUserWithWorkouts.mockResolvedValue({
      workouts: [{ distance: 5 }, { distance: 10 }],
    })
    const totalDistance = await log.getTotalDistance(userId)
    expect(totalDistance).toBe(15)
  })

  describe("Log - localStorage management", () => {
    let log, dbMock

    beforeEach(() => {
      dbMock = new GetMovingDB()
      log = new Log(dbMock)
      jest.spyOn(console, "error").mockImplementation(() => {})
      jest.spyOn(console, "log").mockImplementation(() => {})
    })

    afterEach(() => {
      localStorage.clear()
      jest.restoreAllMocks()
    })

    test("logs a message when no users are found in localStorage", () => {
      // Ensure localStorage is empty
      localStorage.removeItem("workoutLogData")
      log.loadFromLocalStorage()
      expect(console.log).toHaveBeenCalledWith(
        "No users found in localStorage."
      )
    })

    test("handles JSON parsing errors when loading from localStorage", () => {
      // Set invalid JSON data to simulate a parsing error
      localStorage.setItem("workoutLogData", "not a valid JSON")
      log.loadFromLocalStorage()
      expect(console.error).toHaveBeenCalledWith(
        expect.stringContaining("Error managing users in localStorage:")
      )
    })
    describe("Log - deleteUserFromLocalStorage", () => {
      let log, dbMock

      beforeEach(() => {
        dbMock = new GetMovingDB()
        log = new Log(dbMock)
        jest.spyOn(console, "log").mockImplementation(() => {})
        jest.spyOn(console, "error").mockImplementation(() => {})
      })

      afterEach(() => {
        localStorage.clear()
        jest.restoreAllMocks()
      })

      test("deletes a user from localStorage when user exists", () => {
        const users = [
          { id: 1, name: "John Doe" },
          { id: 2, name: "Jane Doe" },
        ]
        localStorage.setItem("workoutLogData", JSON.stringify(users))
        log.deleteUserFromLocalStorage(1)
        const updatedUsers = JSON.parse(localStorage.getItem("workoutLogData"))
        expect(updatedUsers.length).toBe(1)
        expect(updatedUsers[0].id).toBe(2)
        expect(console.log).toHaveBeenCalledWith(
          "User with ID 1 has been deleted from localStorage."
        )
      })

      test("logs a message when no users are found in localStorage for deletion", () => {
        log.deleteUserFromLocalStorage(1)
        expect(console.log).toHaveBeenCalledWith(
          "No users found in localStorage."
        )
      })

      test("handles errors when localStorage data is corrupted", () => {
        localStorage.setItem("workoutLogData", "not a valid JSON")
        log.deleteUserFromLocalStorage(1)
        expect(console.error).toHaveBeenCalledWith(
          expect.stringContaining("Error managing users in localStorage:")
        )
      })

      describe("Log - addWorkoutToUser", () => {
        let log, dbMock

        beforeEach(() => {
          // Mocking database functionality
          dbMock = {
            saveWorkoutToDB: jest.fn().mockResolvedValue(true),
          }
          log = new Log(dbMock)
          log.saveToLocalStorage = jest.fn() // Mock local storage saving
          log.findUser = jest.fn() // Mock findUser method
        })

        test("adds workout to an existing user", async () => {
          // Setup: Assuming findUser returns a valid user object
          const mockUser = {
            id: 1,
            addWorkout: jest.fn(),
          }
          log.findUser.mockReturnValue(mockUser)

          await log.addWorkoutToUser(1, "2021-01-01", "Running", 30, 5)

          expect(log.findUser).toHaveBeenCalledWith(1)
          expect(mockUser.addWorkout).toHaveBeenCalledWith(
            "2021-01-01",
            "Running",
            30,
            5
          )
          expect(log.saveToLocalStorage).toHaveBeenCalled()
          expect(dbMock.saveWorkoutToDB).toHaveBeenCalledWith(
            1,
            "2021-01-01",
            "Running",
            30,
            5
          )
        })

        test("throws error if user is not found", async () => {
          // Setup: findUser does not find a user
          log.findUser.mockReturnValue(null)

          await expect(
            log.addWorkoutToUser(2, "2021-01-01", "Swimming", 40, 2)
          ).rejects.toThrow("User with ID 2 not found.")

          expect(log.findUser).toHaveBeenCalledWith(2)
          expect(dbMock.saveWorkoutToDB).not.toHaveBeenCalled()
          expect(log.saveToLocalStorage).not.toHaveBeenCalled()
        })
      })
    })
  })

  describe("Log - loadFromLocalStorage", () => {
    let log
    beforeEach(() => {
      log = new Log() // Assume Log does not need any parameters or mock them accordingly
      log.saveToLocalStorage = jest.fn() // Mock this function to ensure it does not perform actual storage operations
      global.localStorage = {
        getItem: jest.fn(),
        setItem: jest.fn(),
      }
    })

    test("loads users from local storage when data is present", () => {
      // Mock local storage to return a JSON string that represents an array of users
      const mockUsers = JSON.stringify([
        {
          id: 1,
          firstName: "John",
          lastName: "Doe",
          birthDate: "1990-01-01",
          allMyWorkouts: [
            {
              date: "2020-01-01",
              exercise: "Running",
              duration: 30,
              distance: 5,
            },
          ],
        },
      ])
      localStorage.getItem.mockReturnValue(mockUsers)

      log.loadFromLocalStorage()

      expect(localStorage.getItem).toHaveBeenCalledWith("workoutLogData")
      expect(log.allMyUsers.length).toBe(1)
      expect(log.allMyUsers[0].firstName).toBe("John")
      expect(log.allMyUsers[0].allMyWorkouts.length).toBe(1)
    })

    test("does nothing when no data is found in local storage", () => {
      // Mock local storage to return null
      localStorage.getItem.mockReturnValue(null)

      log.loadFromLocalStorage()

      expect(localStorage.getItem).toHaveBeenCalledWith("workoutLogData")
      expect(log.allMyUsers.length).toBe(0)
    })
  })
})

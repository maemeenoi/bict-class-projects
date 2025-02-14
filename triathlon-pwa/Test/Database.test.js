import { afterEach } from "node:test"
import GetMovingDB from "../Model/Database.js"

describe("GetMovingDB", () => {
  let db

  beforeAll(async () => {
    db = new GetMovingDB("testDB", "users", "workouts")
    await db.init()
  })

  afterEach(async () => {
    db.deleteDatabase()
    await db.close()
  })

  test("Initializes the database correctly", async () => {
    expect(db.dbName).toBe("testDB")
    expect(db.storeUsers).toBe("users")
    expect(db.storeWorkouts).toBe("workouts")
    expect(db.db).toBeDefined()
  })

  test("Adds a user to the database", async () => {
    const user = {
      firstName: "John",
      lastName: "Doe",
      dob: new Date("1990-01-01").toISOString(),
    }
    const userId = await db.saveUserToDB(user)
    expect(userId).toBeDefined()
  })

  test("Gets a user with workouts", async () => {
    const userId = 1 // Example user ID
    const user = await db.getUserWithWorkouts(userId)
    expect(user).toBeDefined()
    expect(user.workouts).toBeDefined()
  })

  test("Updates a user in the database", async () => {
    const userId = 1 // Example user ID
    const updatedData = { firstName: "Jane" }
    await db.updateUser(userId, updatedData)
    const user = await db.getUserWithWorkouts(userId)
    expect(user.firstName).toBe("Jane")
  })

  test("Deletes a user from the database", async () => {
    const userId = 1 // Example user ID
    await db.deleteUser(userId)
    await expect(db.getUserWithWorkouts(userId)).rejects.toThrow(
      "User with ID 1 not found."
    )
  })

  describe("Error handling for deleteUser", () => {
    test("rejects deletion with invalid user ID (null)", async () => {
      const userId = null // Invalid user ID
      await expect(db.deleteUser(userId)).rejects.toThrow(
        "Invalid or no User ID provided"
      )
    })

    test("rejects deletion with invalid user ID (string)", async () => {
      const userId = "invalid" // Non-numeric user ID
      await expect(db.deleteUser(userId)).rejects.toThrow(
        "Invalid or no User ID provided"
      )
    })

    test("rejects deletion with invalid user ID (zero)", async () => {
      const userId = 0 // Typically considered an invalid ID in many systems
      await expect(db.deleteUser(userId)).rejects.toThrow(
        "Invalid or no User ID provided"
      )
    })

    test("Deletes a valid user from the database", async () => {
      const userId = 1 // Example valid user ID
      await db.deleteUser(userId)
      await expect(db.getUserWithWorkouts(userId)).rejects.toThrow(
        "User with ID 1 not found."
      )
    })
  })

  test("Adds a workout to the database", async () => {
    const workout = {
      userId: 1,
      date: new Date().toISOString(),
      exercise: "Running",
      duration: 30,
      distance: 5,
    }
    const workoutId = await db.saveWorkoutToDB(
      workout.userId,
      workout.date,
      workout.exercise,
      workout.duration,
      workout.distance
    )
    expect(workoutId).toBeDefined()
  })

  describe("saveUserToDB Error Handling", () => {
    test("handles failure when adding a user", async () => {
      // Assuming db has been initialized and is available
      const mockAddRequest = {
        onerror: null,
        onsuccess: null,
        error: new Error("Failed to add user"),
      }

      // Mock the object store and add method
      const userStore = {
        add: jest.fn().mockImplementation(() => {
          setTimeout(() => {
            // Ensure the error is called asynchronously
            if (mockAddRequest.onerror) {
              mockAddRequest.onerror({
                target: { error: new Error("Failed to add user") },
              })
            }
          }, 0)
          return mockAddRequest
        }),
      }

      // Mock the transaction to return the mocked object store
      const transaction = {
        objectStore: jest.fn().mockReturnValue(userStore),
        onerror: jest.fn(),
      }

      db.db = {
        // Ensure db is mocked to return our transaction
        transaction: jest.fn().mockReturnValue(transaction),
      }

      // Test the saveUserToDB function for failure
      await expect(
        db.saveUserToDB({
          firstName: "John",
          lastName: "Doe",
          dob: new Date("1990-01-01").toISOString(),
        })
      ).rejects.toThrow("Failed to add user")

      // Verify that the mocked methods were called as expected
      expect(transaction.objectStore).toHaveBeenCalledWith("users")
      expect(userStore.add).toHaveBeenCalled()
    })
  })

  describe("saveUserToDB", () => {
    let db
    let userStoreMock, transactionMock

    beforeEach(() => {
      userStoreMock = {
        add: jest.fn(),
      }
      transactionMock = {
        objectStore: jest.fn().mockReturnValue(userStoreMock),
        onerror: jest.fn(),
        oncomplete: jest.fn(),
      }
      db = new GetMovingDB("testDB", "users", "workouts")
      db.db = {
        transaction: jest.fn().mockReturnValue(transactionMock),
      }
    })

    test("handles add operation errors", async () => {
      const user = { firstName: "John", lastName: "Doe", dob: "1990-01-01" }
      const addRequest = {
        onsuccess: jest.fn(),
        onerror: jest.fn(),
      }
      userStoreMock.add.mockReturnValue(addRequest)

      setTimeout(
        () => addRequest.onerror({ target: { error: "Failed to add user" } }),
        0
      )

      await expect(db.saveUserToDB(user)).rejects.toThrow("Failed to add user")
    })

    test("handles transaction errors", async () => {
      const user = { firstName: "John", lastName: "Doe", dob: "1990-01-01" }
      userStoreMock.add.mockReturnValue({
        onsuccess: jest.fn(),
        onerror: jest.fn(),
      })

      setTimeout(
        () =>
          transactionMock.onerror({ target: { error: "Transaction failed" } }),
        0
      )

      await expect(db.saveUserToDB(user)).rejects.toThrow("Transaction failed")
    })
  })

  describe("GetMovingDB.close", () => {
    test("closes the database connection if open", () => {
      const db = new GetMovingDB("testDB", "users", "workouts")
      db.db = { close: jest.fn() } // Mock db object with a close function

      db.close()
      expect(db.db).toBeNull()
      expect(db.db.close).toHaveBeenCalled()
    })
  })

  describe("GetMovingDB.deleteDatabase", () => {
    let db

    beforeEach(() => {
      db = new GetMovingDB("testDB", "users", "workouts")
      db.db = { close: jest.fn() } // Mock db object with a close function
    })

    test("successfully deletes the database", () => {
      // Mock the IndexedDB deleteDatabase function
      const request = {
        onsuccess: jest.fn(),
        onerror: jest.fn(),
      }
      window.indexedDB.deleteDatabase = jest.fn().mockReturnValue(request)

      return db.deleteDatabase().then(() => {
        expect(window.indexedDB.deleteDatabase).toHaveBeenCalledWith("testDB")
        expect(request.onsuccess).toHaveBeenCalled()
      })
    })

    test("fails to delete the database with an error", () => {
      // Mock the IndexedDB deleteDatabase function to simulate an error
      const request = {
        onsuccess: jest.fn(),
        onerror: jest.fn(),
        error: new Error("Deletion failed"),
      }
      window.indexedDB.deleteDatabase = jest.fn().mockReturnValue(request)

      setTimeout(() => request.onerror({ target: request }), 100) // Trigger the error asynchronously

      return db.deleteDatabase().catch((error) => {
        expect(error).toEqual(
          new Error("Failed to delete database: Deletion failed")
        )
      })
    })
  })
})

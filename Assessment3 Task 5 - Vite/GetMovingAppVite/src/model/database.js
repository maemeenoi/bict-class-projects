export default class GetMovingDB {
  constructor(
    dbName = "GetMovingDB",
    storeUsers = "users",
    storeWorkouts = "workouts"
  ) {
    this.dbName = dbName
    this.storeUsers = storeUsers
    this.storeWorkouts = storeWorkouts
    this.init()
  }

  async init(version = 1) {
    return new Promise((resolve, reject) => {
      const request = window.indexedDB.open(this.dbName, version) // Fixed to use this.dbName

      request.onerror = (event) => {
        console.error("Database error: ", event.target.error)
        reject(new Error(`Failed to open database: ${event.target.error}`))
      }

      // Handle database upgrades
      request.onupgradeneeded = (event) => {
        const db = event.target.result

        // Create the 'users' store if it doesn't exist
        if (!db.objectStoreNames.contains(this.storeUsers)) {
          const userStore = db.createObjectStore(this.storeUsers, {
            keyPath: "id",
            autoIncrement: true,
          })
          userStore.createIndex("firstName", "firstName", { unique: false })
          userStore.createIndex("lastName", "lastName", { unique: false })
          userStore.createIndex("dob", "dob", { unique: false })
        }

        // Create the 'workouts' store if it doesn't exist
        if (!db.objectStoreNames.contains(this.storeWorkouts)) {
          const workoutStore = db.createObjectStore(this.storeWorkouts, {
            keyPath: "id",
            autoIncrement: true,
          })
          workoutStore.createIndex("userId", "userId", { unique: false })
          workoutStore.createIndex("exercise", "exercise", { unique: false })
          workoutStore.createIndex("duration", "duration", { unique: false })
          workoutStore.createIndex("distance", "distance", { unique: false })
        }
      }

      // Handle successful database opening
      request.onsuccess = (event) => {
        this.db = event.target.result
        console.log("Database opened successfully")
        resolve(this.db) // Resolve with the database instance for potential immediate use
      }
    })
  }

  async saveUserToDB(user) {
    return new Promise((resolve, reject) => {
      // Start new transaction
      console.log(user)
      const transaction = this.db.transaction([this.storeUsers], "readwrite")
      const store = transaction.objectStore(this.storeUsers)
      const request = store.add({
        firstName: user.firstName,
        lastName: user.lastName,
        dob: user.birthDate,
      })
      // Handle errors when adding data
      request.onerror = (event) => {
        console.error("Error adding user: ", event.target.error)
        reject(new Error(`Failed to add user: ${event.target.error}`)) // Reject the promise on error
      }

      request.onsuccess = (event) => {
        console.log(
          `User added successfully: ${user.firstName} ${user.lastName}`
        )
        resolve(request.result) // Resolve with the key of the newly added user
      }

      // Handle transaction errors
      transaction.onerror = (event) => {
        console.error("Transaction error: ", event.target.error)
        reject(new Error(`Transaction failed: ${event.target.error}`))
      }

      // Optionally, you could handle the complete event of the transaction
      transaction.oncomplete = () => {
        console.log("Transaction completed successfully.")
      }
    })
  }
  async saveWorkoutToDB(userId, date, exercise, duration, distance) {
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([this.storeWorkouts], "readwrite")
      const store = transaction.objectStore(this.storeWorkouts)
      const request = store.add({
        userId,
        exercise,
        duration,
        distance,
        date: new Date(date).toISOString(),
      })

      request.onsuccess = () => {
        console.log(`Workout added for user ID: ${userId}`)
        resolve(request.result) // Resolve with the key of the newly added workout
      }

      request.onerror = (event) => {
        console.error("Error adding workout: ", event.target.error)
        reject(new Error(`Error adding workout: ${event.target.error}`)) // More specific error message
      }

      transaction.onerror = (event) => {
        console.error("Transaction error: ", event.target.error)
        reject(new Error(`Transaction error: ${event.target.error}`)) // Reject also on transaction error
      }

      transaction.oncomplete = () => {
        console.log("Transaction completed successfully.")
      }
    })
  }

  async getUserWithWorkouts(userId) {
    return new Promise((resolve, reject) => {
      const id = Number(userId)
      const userTransaction = this.db.transaction([this.storeUsers], "readonly")
      const userStore = userTransaction.objectStore(this.storeUsers)
      const userRequest = userStore.get(id)

      userRequest.onsuccess = async (event) => {
        const user = event.target.result
        if (user) {
          const workoutTransaction = this.db.transaction(
            [this.storeWorkouts],
            "readonly"
          )
          const workoutStore = workoutTransaction.objectStore(
            this.storeWorkouts
          )
          const index = workoutStore.index("userId")
          const workoutRequest = index.getAll(id)

          workoutRequest.onsuccess = (event) => {
            user.workouts = event.target.result
            resolve(user) // Resolve the promise with the user and their workouts
          }

          workoutRequest.onerror = (event) => {
            console.error("Failed to get workouts: ", event.target.error)
            reject(new Error(`Failed to get workouts: ${event.target.error}`)) // Reject the promise on workout fetch error
          }
        } else {
          console.error(`User with ID ${userId} not found.`)
          reject(new Error(`User with ID ${userId} not found.`)) // Reject the promise if the user is not found
        }
      }

      userRequest.onerror = (event) => {
        console.error("Failed to get user: ", event.target.error)
        reject(new Error(`Failed to get user: ${event.target.error}`)) // Reject the promise on user fetch error
      }
    })
  }

  async deleteUser(userId) {
    return new Promise((resolve, reject) => {
      // First, check if the userId is valid and not zero
      if (!userId || isNaN(Number(userId))) {
        console.error("Invalid or no User ID provided")
        reject(new Error("Invalid or no User ID provided"))
        return // Stop the function if no valid ID
      }

      // Start the transaction to delete the user
      const userTransaction = this.db.transaction(
        [this.storeUsers],
        "readwrite"
      )
      const userStore = userTransaction.objectStore(this.storeUsers)
      const userDeleteRequest = userStore.delete(userId)

      userDeleteRequest.onerror = (event) => {
        console.error("Failed to delete user: ", event.target.error)
        reject(new Error(`Failed to delete user: ${event.target.error}`))
      }

      userDeleteRequest.onsuccess = () => {
        console.log(`User with ID ${userId} deleted`)
        // Proceed to delete workouts related to the user
        const workoutTransaction = this.db.transaction(
          [this.storeWorkouts],
          "readwrite"
        )
        const workoutStore = workoutTransaction.objectStore(this.storeWorkouts)
        const index = workoutStore.index("userId")
        const workoutDeleteRequest = index.openCursor(IDBKeyRange.only(userId))

        workoutDeleteRequest.onsuccess = (event) => {
          const cursor = event.target.result
          if (cursor) {
            workoutStore.delete(cursor.primaryKey)
            cursor.continue() // Continue until no more entries
          } else {
            console.log(`All workouts for user ID ${userId} deleted.`)
            resolve() // Resolve the promise here after all deletions
          }
        }

        workoutDeleteRequest.onerror = (event) => {
          console.error("Failed to delete workouts: ", event.target.error)
          reject(new Error(`Failed to delete workouts: ${event.target.error}`))
        }
      }
    })
  }

  async updateUser(userId, updatedData) {
    return new Promise((resolve, reject) => {
      const transaction = this.db.transaction([this.storeUsers], "readwrite")
      const store = transaction.objectStore(this.storeUsers)
      const getUserRequest = store.get(userId)

      getUserRequest.onsuccess = (event) => {
        const user = event.target.result

        if (user) {
          Object.keys(updatedData).forEach((key) => {
            if (key === "birthDate" && updatedData[key]) {
              user.dob = new Date(updatedData[key]).toISOString()
            } else {
              user[key] = updatedData[key]
            }
          })

          const updateUserRequest = store.put(user)

          updateUserRequest.onsuccess = () => {
            console.log(`User with ID ${userId} updated successfully.`)
            resolve(user) // Resolve with the updated user object
          }

          updateUserRequest.onerror = (event) => {
            console.error("Failed to update user: ", event.target.error)
            reject(new Error(`Failed to update user: ${event.target.error}`))
          }
        } else {
          console.error(`User with ID ${userId} not found.`)
          reject(new Error(`User with ID ${userId} not found.`)) // Reject the promise if the user is not found
        }
      }

      getUserRequest.onerror = (event) => {
        console.error("Failed to get user: ", event.target.error)
        reject(new Error(`Failed to get user: ${event.target.error}`))
      }

      transaction.onerror = (event) => {
        console.error("Transaction error: ", event.target.error)
        reject(new Error(`Transaction error: ${event.target.error}`)) // Handle transaction errors
      }
    })
  }
  // The close method closes the connection to the database
  close() {
    if (this.db) {
      this.db.close()
      this.db = null
    }
  }

  // The deleteDatabase method deletes the entire database
  async deleteDatabase() {
    return new Promise((resolve, reject) => {
      // Close the database before deleting it
      this.close()
      const request = window.indexedDB.deleteDatabase(this.dbName)

      // Handle errors when deleting the database
      request.onerror = (event) => {
        reject(new Error(`Failed to delete database: ${event.target.error}`))
      }

      // Handle successful database deletion
      request.onsuccess = (event) => {
        resolve()
      }
    })
  }
}

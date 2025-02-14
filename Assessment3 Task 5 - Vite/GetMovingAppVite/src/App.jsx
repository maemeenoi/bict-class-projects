import GetMovingDB from "./model/database.js"
import Log from "./model/log.js"
import GetMovingAppViewModel from "./viewmodel/get_moving_app_viewmodel.js"
import FetchUserWithWorkout from "./view/fetch_user_with_workout.jsx"
import AddUser from "./view/add_user.jsx"
import AddWorkout from "./view/add_workout.jsx"

import "./App.css"

function App() {
  const db = new GetMovingDB()
  const log = new Log(db)
  const viewModel = new GetMovingAppViewModel(log)

  return (
    <>
      <h1>Get Moving App</h1>
      <AddUser onClick={viewModel.addUser} />
      <AddWorkout onAddWorkout={viewModel.addWorkoutToUser} />
      <FetchUserWithWorkout
        onFetchUser={viewModel.getUserWithWorkouts}
        onUpdate={viewModel.updateUser}
        onDelete={viewModel.deleteUser}
        onGetTotalDuration={viewModel.getTotalDuration}
        onGetTotalDistance={viewModel.getTotalDistance}
      />
    </>
  )
}

export default App

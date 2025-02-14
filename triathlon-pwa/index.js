import GetMovingDB from "./model/database.js"
import Log from "./model/log.js"
import GetMovingAppViewModel from "./viewModel/get_moving_app_viewmodel.js"
import FetchUserWithWorkout from "./view/fetch_user_with_workout.js"
import AddUser from "./view/add_user.js"
import AddWorkout from "./view/add_workout.js"

const db = new GetMovingDB()
const log = new Log(db)
const viewModel = new GetMovingAppViewModel(log)

const element = (
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

const container = document.getElementById("app")
const root = ReactDOM.createRoot(container)
root.render(element)

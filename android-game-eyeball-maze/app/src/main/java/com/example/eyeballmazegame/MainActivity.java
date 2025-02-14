package com.example.eyeballmazegame;

import android.content.Intent;
import android.media.AudioAttributes;
import android.media.AudioManager;
import android.media.SoundPool;
import android.os.Build;
import android.os.Bundle;
import android.util.Log;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;

import com.example.eyeballmazegame.enums.Direction;
import com.example.eyeballmazegame.enums.Message;

public class MainActivity extends AppCompatActivity {
    private static final String TAG = "MainActivity";

    private GameBoardView gameBoardView; //A view component that displays the game board.
    private Game game; //Manages the game logic and state.
    private TextView levelTextView; //Displays the current level of the game.
    private TextView goalProgressTextView; //Shows the progress towards completing goals.
    private TextView moveCountTextView; //Indicates the number of moves taken in the current level.
    private int currentLevel = 1;
    private int completedGoals = 0;
    private static final int MAX_MOVES = 10;
    private int moveCount = 0;
    private final int TOTAL_LEVELS = 5;  // Total number of levels/goals
    private ImageButton soundToggleButton; //Button to toggle sound on and off.
    private TextView soundStateTextView; // Displays the current state of the sound (on or off).
    private boolean isSoundOn = true;
    private SoundPool soundPool; //Manages and plays audio resources for the game.
    private int moveSound; // sound when move around
    private int completionSound; // sound when won
    private int failSound; // Sound when failed
    private int errorSound;  // New sound for incorrect moves


    //Initializes the activity, sets up the layout, and configures initial game settings.
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        initializeViews();
        setupButtonListeners();
        initializeGame();
        initializeSound();
        Button rulesButton = findViewById(R.id.rulesButton);
        rulesButton.setOnClickListener(v -> showRulesVideo());
    }

    //Configures listeners for buttons to handle user interactions.
    private void setupButtonListeners() {
        findViewById(R.id.upButton).setOnClickListener(v -> moveEyeball(Direction.UP));
        findViewById(R.id.downButton).setOnClickListener(v -> moveEyeball(Direction.DOWN));
        findViewById(R.id.leftButton).setOnClickListener(v -> moveEyeball(Direction.LEFT));
        findViewById(R.id.rightButton).setOnClickListener(v -> moveEyeball(Direction.RIGHT));
        findViewById(R.id.restartButton).setOnClickListener(v -> restartCurrentLevel());
        soundToggleButton.setOnClickListener(v -> toggleSound());

    }

    //Links the Java variables with their respective views in the layout.
    private void initializeViews() {
        gameBoardView = findViewById(R.id.gameBoardView);
        levelTextView = findViewById(R.id.levelTextView);
        goalProgressTextView = findViewById(R.id.goalProgressTextView);
        moveCountTextView = findViewById(R.id.moveCountTextView);
        soundToggleButton = findViewById(R.id.soundToggleButton);
        soundStateTextView = findViewById(R.id.soundStateTextView);



    }

    //Sets up the sound engine and loads sound effects.
    private void initializeSound() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            AudioAttributes audioAttributes = new AudioAttributes.Builder()
                    .setUsage(AudioAttributes.USAGE_GAME)
                    .setContentType(AudioAttributes.CONTENT_TYPE_SONIFICATION)
                    .build();

            soundPool = new SoundPool.Builder()
                    .setMaxStreams(3)  // Increased to accommodate the new sound
                    .setAudioAttributes(audioAttributes)
                    .build();
        } else {
            soundPool = new SoundPool(3, AudioManager.STREAM_MUSIC, 0);
        }

        moveSound = soundPool.load(this, R.raw.move_sound, 1);
        completionSound = soundPool.load(this, R.raw.completion_sound, 1);
        errorSound = soundPool.load(this, R.raw.error_sound, 1);  // Load the new error sound
        failSound = soundPool.load(this, R.raw.fail_sound, 1);
    }

    //Initializes the game logic, loads the first level, and updates display elements.
    private void initializeGame() {
        Log.d(TAG, "Initializing game");
        game = new Game();
        loadLevel(currentLevel);
        updateLevelDisplay();
        Log.d(TAG, "Game initialized with level " + currentLevel);
    }

    //Loads game data for a specific level and updates the game board.
    private void loadLevel(int levelNumber) {
        Log.d("MainActivity", "Loading level " + levelNumber);
        game = new Game();
        game.addLevel(4, 4);
        gameBoardView.setLevel(levelNumber);

        Square[][] board = gameBoardView.getBoard();
        Level level = game.getLevel();
        for (int row = 0; row < 4; row++) {
            for (int col = 0; col < 4; col++) {
                level.setSquare(row, col, board[row][col]);
            }
        }

        Position goalPosition = gameBoardView.getGoalPosition();
        level.addGoal(goalPosition);

        Position startPosition = getStartPosition(levelNumber);
        game.addEyeball(startPosition.getRow(), startPosition.getColumn(), Direction.UP);

        gameBoardView.setGame(game);

        moveCount = 0;
        updateMoveCount();
        updateLevelDisplay();
    }

    //Determines the starting position of the eyeball based on the level.
    private Position getStartPosition(int levelNumber) {
        switch (levelNumber) {
            case 1:
                return new Position(3, 0);
            case 2:
                return new Position(3, 3);
            case 3:
                return new Position(3, 0);
            case 4:
                return new Position(3, 3);
            case 5:
                return new Position(3, 0);
            default:
                return new Position(3, 0);
        }
    }

    //Displays an alert dialog when all levels are completed.
    private void showGameComplete() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Game Complete!")
                .setMessage("Congratulations! You've completed all levels.\nFinal Score: " + completedGoals + "/" + TOTAL_LEVELS)
                .setPositiveButton("Restart", (dialog, which) -> {
                    currentLevel = 1;
                    completedGoals = 0;
                    loadLevel(currentLevel);
                    updateLevelDisplay();
                })
                .setCancelable(false)
                .show();
    }

    //Handles the completion of a level, updates UI, and checks if the game is complete.
    public void onLevelComplete() {
        completedGoals++;
        updateGoalProgress();
        playSound(completionSound);  // Play the completion sound
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Level Complete!")
                .setMessage("Congratulations! You've completed level " + currentLevel +
                        ".\nGoals Progress: " + completedGoals + "/" + TOTAL_LEVELS +
                        "\nMoves taken: " + moveCount + "/" + MAX_MOVES)
                .setPositiveButton("Next Level", (dialog, which) -> {
                    currentLevel++;
                    if (currentLevel <= TOTAL_LEVELS) {
                        loadLevel(currentLevel);
                    } else {
                        showGameComplete();
                    }
                })
                .setCancelable(false)
                .show();
    }

    //Updates the display of the current level on the screen.
    private void updateLevelDisplay() {
        levelTextView.setText("Level: " + currentLevel);
        updateGoalProgress();
    }

    //Updates the UI to show the number of goals completed.
    private void updateGoalProgress() {
        goalProgressTextView.setText("Goals: " + completedGoals + "/" + TOTAL_LEVELS);
    }

    //Resets the current level to its initial state.
    private void restartCurrentLevel() {
        moveCount = 0;
        loadLevel(currentLevel);
        updateLevelDisplay();
        updateMoveCount();
    }

    //Displays a dialog when the player fails a level by exceeding the allowed number of moves.
    private void onLevelLost() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        playSound(failSound);
        builder.setTitle("Level Failed!")
                .setMessage("You've exceeded the maximum number of moves (" + MAX_MOVES + ").")
                .setPositiveButton("Restart Level", (dialog, which) -> {
                    restartCurrentLevel();
                })
                .setCancelable(false)
                .show();
    }

    //Toggles the sound on or off and updates the UI accordingly.
    private void toggleSound() {
        isSoundOn = !isSoundOn;
        updateSoundButtonImage();
        updateSoundStateText();
    }

    //Updates the sound toggle button's icon based on the sound state.
    private void updateSoundButtonImage() {
        soundToggleButton.setImageResource(isSoundOn ? R.drawable.ic_volume_up : R.drawable.ic_volume_off);
    }

    //Updates the text display of the sound state.
    private void updateSoundStateText() {
        soundStateTextView.setText(isSoundOn ? "Sound On" : "Sound Off");
    }

    // Plays a sound effect based on the provided sound ID.
    private void playSound(int soundId) {
        if (isSoundOn) {
            soundPool.play(soundId, 1.0f, 1.0f, 1, 0, 1.0f);
        }
    }

    //Updates the count of moves taken in the current level on the UI.
    private void updateMoveCount() {
        moveCountTextView.setText("Moves: " + moveCount + "/" + MAX_MOVES);
    }

    //Displays a toast message and plays an error sound if necessary.
    public void showMessage(Message message) {
        Toast.makeText(this, message.toString(), Toast.LENGTH_SHORT).show();
        if (message == Message.DIFFERENT_SHAPE_OR_COLOR || message == Message.MOVING_OVER_BLANK) {
            playSound(errorSound);
        }
    }

    //Starts an activity to display the game rules video.
    private void showRulesVideo() {
        Intent intent = new Intent(this, VideoActivity.class);
        startActivity(intent);
    }

    //Moves the eyeball in the specified direction and handles game state updates.
    private void moveEyeball(Direction direction) {
        boolean moved = gameBoardView.moveEyeball(direction);
        if (moved) {
            moveCount++;
            updateMoveCount();
            playSound(moveSound);

            if (moveCount >= MAX_MOVES) {
                onLevelLost();
            }
        } else {
            playSound(errorSound);
        }
    }

    //Cleans up resources when the activity is destroyed, specifically releasing sound resources.
    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (soundPool != null) {
            soundPool.release();
            soundPool = null;
        }
    }

    //Ensures the sound state is correctly displayed when the app resumes.
    @Override
    protected void onResume() {
        super.onResume();
        updateSoundButtonImage();
        updateSoundStateText();
    }

}
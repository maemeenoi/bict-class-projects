package com.example.eyeballmazegame;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Paint;
import android.util.AttributeSet;
import android.view.GestureDetector;
import android.view.View;
import android.view.MotionEvent;
import android.widget.Toast;

import com.example.eyeballmazegame.enums.Color;
import com.example.eyeballmazegame.enums.Direction;
import com.example.eyeballmazegame.enums.Message;
import com.example.eyeballmazegame.enums.Shape;

import java.util.EnumMap;
import java.util.HashMap;
import java.util.Map;

public class GameBoardView extends View {
    private Game game;
    private Paint paint;
    private Map<String, Bitmap> squareBitmaps; //  stores Bitmap images for different types of squares on the game board, with each String key representing a specific type of square.
    private Map<Direction, Bitmap> eyeballBitmaps; // holds Bitmap images for the eyeball character in various directions, with the Direction enum defining possible movement directions like up, down, left, and right.
    private Map<String, Bitmap> symbolBitmaps;// keeps Bitmap images for various symbols that might appear within the game, with each String key representing a different symbol.
    private Bitmap goalBitmap; //  stores the Bitmap image used to represent the goal or target location in the game.
    private GestureDetector gestureDetector; // used to detect and interpret various touchscreen gestures, such as taps and swipes, to simplify handling user interactions.
    private Direction currentEyeballDirection = Direction.UP; // Always face up to make it not confused
    private Square[][] board;
    private Position goalPosition;
    private int currentLevel = 1;

    // Initializes the view by setting the context and attributes, then calls the init method to set up the game components.
    public GameBoardView(Context context, AttributeSet attrs) {
        super(context, attrs);
        init(context);
    }

    //Sets up paint tools, initializes bitmap storage, loads necessary bitmaps, and sets up the gesture detector for handling user interactions.
    private void init(Context context) {
        paint = new Paint();
        squareBitmaps = new HashMap<>();
        eyeballBitmaps = new EnumMap<>(Direction.class);
        loadBitmaps();
        gestureDetector = new GestureDetector(context, new GameGestureListener());
        loadEyeballBitmaps();
        loadSymbolBitmaps();
        loadGoalBitmap();
        initializeBoard();
    }

    //Creates and initializes a 4x4 grid of blank squares and sets up the game board according to the current level.
    private void initializeBoard() {
        board = new Square[4][4];

        // Initialize all squares as blank
        for (int i = 0; i < 4; i++) {
            for (int j = 0; j < 4; j++) {
                board[i][j] = new Square.BlankSquare();
            }
        }

        switch (currentLevel) {
            case 1:
                setupLevel1();
                break;
            case 2:
                setupLevel2();
                break;
            case 3:
                setupLevel3();
                break;
            case 4:
                setupLevel4();
                break;
            case 5:
                setupLevel5();
                break;
            default:
                setupLevel1(); // Default to level 1
        }
    }

    //Loads bitmap images for various game symbols from resources and stores them in a map for quick access during gameplay.
    private void loadSymbolBitmaps() {
        symbolBitmaps = new HashMap<>();

        // Blue symbols
        symbolBitmaps.put("BLUE_CROSS", BitmapFactory.decodeResource(getResources(), R.drawable.cross_blue));
        symbolBitmaps.put("BLUE_DIAMOND", BitmapFactory.decodeResource(getResources(), R.drawable.diamond_blue));
        symbolBitmaps.put("BLUE_STAR", BitmapFactory.decodeResource(getResources(), R.drawable.star_blue));
        symbolBitmaps.put("BLUE_FLOWER", BitmapFactory.decodeResource(getResources(), R.drawable.flower_blue));

        // Red symbols
        symbolBitmaps.put("RED_CROSS", BitmapFactory.decodeResource(getResources(), R.drawable.cross_red));
        symbolBitmaps.put("RED_DIAMOND", BitmapFactory.decodeResource(getResources(), R.drawable.diamon_red));
        symbolBitmaps.put("RED_STAR", BitmapFactory.decodeResource(getResources(), R.drawable.star_red));
        symbolBitmaps.put("RED_FLOWER", BitmapFactory.decodeResource(getResources(), R.drawable.flower_red));

        // Yellow symbols
        symbolBitmaps.put("YELLOW_CROSS", BitmapFactory.decodeResource(getResources(), R.drawable.cross_yellow));
        symbolBitmaps.put("YELLOW_DIAMOND", BitmapFactory.decodeResource(getResources(), R.drawable.diamon_yellow));
        symbolBitmaps.put("YELLOW_STAR", BitmapFactory.decodeResource(getResources(), R.drawable.star_yellow));
        symbolBitmaps.put("YELLOW_FLOWER", BitmapFactory.decodeResource(getResources(), R.drawable.flower_yellow));

        // Green symbols
        symbolBitmaps.put("GREEN_CROSS", BitmapFactory.decodeResource(getResources(), R.drawable.cross_green));
        symbolBitmaps.put("GREEN_DIAMOND", BitmapFactory.decodeResource(getResources(), R.drawable.diamon_green));
        symbolBitmaps.put("GREEN_STAR", BitmapFactory.decodeResource(getResources(), R.drawable.star_green));
        symbolBitmaps.put("GREEN_FLOWER", BitmapFactory.decodeResource(getResources(), R.drawable.flower_green));
    }

    //Loads the bitmap image used to represent the goal in the game from resources.
    private void loadGoalBitmap() {
        goalBitmap = BitmapFactory.decodeResource(getResources(), R.drawable.goal);
    }

    //Loads and assigns bitmap images for the squares and the eyeball, based on their color and shape, from drawable resources.
    private void loadBitmaps() {
        // Load square bitmaps
        for (Color color : Color.values()) {
            for (Shape shape : Shape.values()) {
                if (color != Color.BLANK && shape != Shape.BLANK) {
                    String key = color.name() + "_" + shape.name();
                    int resourceId = getResources().getIdentifier(
                            shape.name().toLowerCase() + "_" + color.name().toLowerCase(),
                            "drawable",
                            getContext().getPackageName()
                    );
                    squareBitmaps.put(key, BitmapFactory.decodeResource(getResources(), resourceId));
                }
            }
        }

        // Load eyeball bitmaps
        for (Direction direction : Direction.values()) {
            int resourceId = getResources().getIdentifier(
                    "eyeball_" + direction.name().toLowerCase(),
                    "drawable",
                    getContext().getPackageName()
            );
            eyeballBitmaps.put(direction, BitmapFactory.decodeResource(getResources(), resourceId));
        }

        goalBitmap = BitmapFactory.decodeResource(getResources(), R.drawable.goal);
    }

    //Sets the game logic handler and triggers a redraw of the view.
    public void setGame(Game game) {
        this.game = game;
        invalidate();
    }

    //Updates the current level, reinitializes the board for the new level, and triggers a redraw.
    public void setLevel(int level) {
        this.currentLevel = level;
        initializeBoard();
        invalidate();
    }

    //Returns the current state of the game board.
    public Square[][] getBoard() {
        return board;
    }

    //Retrieves the current goal position on the game board.
    public Position getGoalPosition() {
        return goalPosition;
    }

    //Displays a brief message in a toast notification on the screen.
    private void showToast(final String message) {
        post(new Runnable() {
            @Override
            public void run() {
                Toast.makeText(getContext(), message, Toast.LENGTH_SHORT).show();
            }
        });
    }

    //Loads bitmap images for different directions of the eyeball from resources into an enum map.
    private void loadEyeballBitmaps() {
        eyeballBitmaps = new EnumMap<>(Direction.class);
        eyeballBitmaps.put(Direction.DOWN, BitmapFactory.decodeResource(getResources(), R.drawable.eyesd));
        eyeballBitmaps.put(Direction.LEFT, BitmapFactory.decodeResource(getResources(), R.drawable.eyesl));
        eyeballBitmaps.put(Direction.RIGHT, BitmapFactory.decodeResource(getResources(), R.drawable.eyesr));
        eyeballBitmaps.put(Direction.UP, BitmapFactory.decodeResource(getResources(), R.drawable.eyesu));
    }

    //Draws the game board, including grid lines, symbols, the goal, and the eyeball, on the canvas.
    @Override
    protected void onDraw(Canvas canvas) {
        super.onDraw(canvas);
        if (board == null) return;

        int width = getWidth();
        int height = getHeight();
        int cellSize = Math.min(width, height) / 4;

        // Draw the grid and symbols
        for (int row = 0; row < 4; row++) {
            for (int col = 0; col < 4; col++) {
                float left = col * cellSize;
                float top = row * cellSize;

                // Draw grid cell
                Paint paint = new Paint();
                paint.setColor(R.drawable.blank_square);
                paint.setStyle(Paint.Style.STROKE);
                canvas.drawRect(left, top, left + cellSize, top + cellSize, paint);

                // Draw symbol if present
                Square square = board[row][col];
                if (square instanceof Square.PlayableSquare) {
                    Square.PlayableSquare playableSquare = (Square.PlayableSquare) square;
                    String key = playableSquare.getColor() + "_" + playableSquare.getShape();
                    Bitmap symbolBitmap = symbolBitmaps.get(key);
                    if (symbolBitmap != null) {
                        canvas.drawBitmap(symbolBitmap, left, top, null);
                    }
                }
            }
        }

        // Draw the goal
        if (goalPosition != null) {
            float goalLeft = goalPosition.getColumn() * cellSize;
            float goalTop = goalPosition.getRow() * cellSize;
            canvas.drawBitmap(goalBitmap, goalLeft, goalTop, null);
        }

        Bitmap eyeballBitmap = eyeballBitmaps.get(currentEyeballDirection);
        if (eyeballBitmap != null && game != null) {
            float eyeballLeft = game.getEyeballColumn() * cellSize;
            float eyeballTop = game.getEyeballRow() * cellSize;
            canvas.drawBitmap(eyeballBitmap, eyeballLeft, eyeballTop, null);

            // Draw debug info
            Paint debugPaint = new Paint();
            debugPaint.setColor(android.graphics.Color.BLACK);
            debugPaint.setTextSize(30);
            canvas.drawText("Eyeball: " + game.getEyeballColor() + " " + game.getEyeballShape(), 10, getHeight() - 10, debugPaint);
        }
    }

    //Handles touch events by passing them to the gesture detector and the superclasses touch event method.
    @Override
    public boolean onTouchEvent(MotionEvent event) {
        return gestureDetector.onTouchEvent(event) || super.onTouchEvent(event);
    }

    //Handles fling gestures to move the eyeball in the specified direction based on swipe thresholds and velocities.
    private class GameGestureListener extends GestureDetector.SimpleOnGestureListener {
        private static final int SWIPE_THRESHOLD = 100;
        private static final int SWIPE_VELOCITY_THRESHOLD = 100;

        @Override
        public boolean onFling(MotionEvent e1, MotionEvent e2, float velocityX, float velocityY) {
            float diffX = e2.getX() - e1.getX();
            float diffY = e2.getY() - e1.getY();

            if (Math.abs(diffX) > Math.abs(diffY)) {
                if (Math.abs(diffX) > SWIPE_THRESHOLD && Math.abs(velocityX) > SWIPE_VELOCITY_THRESHOLD) {
                    if (diffX > 0) {
                        moveEyeball(0, 1); // Right
                    } else {
                        moveEyeball(0, -1); // Left
                    }
                }
            } else {
                if (Math.abs(diffY) > SWIPE_THRESHOLD && Math.abs(velocityY) > SWIPE_VELOCITY_THRESHOLD) {
                    if (diffY > 0) {
                        moveEyeball(1, 0); // Down
                    } else {
                        moveEyeball(-1, 0); // Up
                    }
                }
            }
            return super.onFling(e1, e2, velocityX, velocityY);
        }
    }

    //Moves the eyeball on the board based on input directions and checks for goal completion or displays messages if movement is invalid.
    private void moveEyeball(int rowDelta, int colDelta) {
        int newRow = game.getEyeballRow() + rowDelta;
        int newCol = game.getEyeballColumn() + colDelta;

        Message message = game.MessageIfMovingTo(newRow, newCol);
        if (message == Message.OK && game.canMoveTo(newRow, newCol)) {
            game.moveTo(newRow, newCol);

            // Check if the new position is the goal position
            if (newRow == goalPosition.getRow() && newCol == goalPosition.getColumn()) {
                showToast("Goal Completed!");
                // You might want to trigger level completion here
                ((MainActivity)getContext()).onLevelComplete();
            }

            invalidate();
        } else {
            ((MainActivity)getContext()).showMessage(message);
        }
    }

    //Moves the eyeball on the board based on input directions and checks for goal completion or displays messages if movement is invalid.
    public boolean moveEyeball(Direction direction) {
        if (game == null) return false;

        int currentRow = game.getEyeballRow();
        int currentCol = game.getEyeballColumn();

        int newRow = currentRow;
        int newCol = currentCol;

        switch (direction) {
            case UP:
                newRow--;
                break;
            case DOWN:
                newRow++;
                break;
            case LEFT:
                newCol--;
                break;
            case RIGHT:
                newCol++;
                break;
        }

        Message message = game.MessageIfMovingTo(newRow, newCol);
        if (message == Message.OK) {
            game.moveTo(newRow, newCol);
            currentEyeballDirection = direction;

            if (newRow == goalPosition.getRow() && newCol == goalPosition.getColumn()) {
                ((MainActivity)getContext()).onLevelComplete();
            }

            invalidate();
            return true;
        } else {
            ((MainActivity)getContext()).showMessage(message);  // This will trigger the error sound
            return false;
        }
    }

    //Configures the board for different levels by placing squares with specific colors and shapes and setting the goal position.
    private void setupLevel1() {
        // Set up the path from start to goal
        board[3][0] = new Square.PlayableSquare(Color.BLUE, Shape.CROSS);   // Start
        board[2][0] = new Square.PlayableSquare(Color.BLUE, Shape.STAR);
        board[1][0] = new Square.PlayableSquare(Color.RED, Shape.STAR);
        board[1][1] = new Square.PlayableSquare(Color.RED, Shape.FLOWER);
        board[1][2] = new Square.PlayableSquare(Color.YELLOW, Shape.FLOWER);
        board[0][2] = new Square.PlayableSquare(Color.YELLOW, Shape.DIAMOND);
        board[0][1] = new Square.PlayableSquare(Color.GREEN, Shape.DIAMOND); // Goal

        // Add some additional squares
        board[0][0] = new Square.PlayableSquare(Color.BLUE, Shape.FLOWER);
        board[0][3] = new Square.PlayableSquare(Color.RED, Shape.DIAMOND);
        board[1][3] = new Square.PlayableSquare(Color.YELLOW, Shape.CROSS);
        board[2][1] = new Square.PlayableSquare(Color.GREEN, Shape.CROSS);
        board[2][2] = new Square.PlayableSquare(Color.BLUE, Shape.FLOWER);
        board[2][3] = new Square.PlayableSquare(Color.RED, Shape.STAR);
        board[3][1] = new Square.PlayableSquare(Color.GREEN, Shape.STAR);
        board[3][2] = new Square.PlayableSquare(Color.YELLOW, Shape.DIAMOND);
        board[3][3] = new Square.PlayableSquare(Color.BLUE, Shape.CROSS);

        goalPosition = new Position(0, 1);
    }

    private void setupLevel2() {
        // Set up a different path for level 2
        board[3][3] = new Square.PlayableSquare(Color.RED, Shape.CROSS);   // Start
        board[3][2] = new Square.PlayableSquare(Color.RED, Shape.STAR);
        board[2][2] = new Square.PlayableSquare(Color.YELLOW, Shape.STAR);
        board[2][1] = new Square.PlayableSquare(Color.YELLOW, Shape.FLOWER);
        board[1][1] = new Square.PlayableSquare(Color.BLUE, Shape.FLOWER);
        board[1][0] = new Square.PlayableSquare(Color.BLUE, Shape.DIAMOND);
        board[0][0] = new Square.PlayableSquare(Color.GREEN, Shape.DIAMOND); // Goal

        // Add some additional squares
        board[0][1] = new Square.PlayableSquare(Color.RED, Shape.FLOWER);
        board[0][2] = new Square.PlayableSquare(Color.YELLOW, Shape.CROSS);
        board[0][3] = new Square.PlayableSquare(Color.BLUE, Shape.STAR);
        board[1][2] = new Square.PlayableSquare(Color.GREEN, Shape.CROSS);
        board[1][3] = new Square.PlayableSquare(Color.RED, Shape.DIAMOND);
        board[2][0] = new Square.PlayableSquare(Color.YELLOW, Shape.DIAMOND);
        board[2][3] = new Square.PlayableSquare(Color.GREEN, Shape.FLOWER);
        board[3][0] = new Square.PlayableSquare(Color.BLUE, Shape.CROSS);
        board[3][1] = new Square.PlayableSquare(Color.GREEN, Shape.STAR);

        goalPosition = new Position(0, 0);
    }

    private void setupLevel3() {
        // Start
        board[3][0] = new Square.PlayableSquare(Color.BLUE, Shape.CROSS);
        board[2][0] = new Square.PlayableSquare(Color.BLUE, Shape.STAR);
        board[2][1] = new Square.PlayableSquare(Color.RED, Shape.STAR);
        board[2][2] = new Square.PlayableSquare(Color.RED, Shape.FLOWER);
        board[1][2] = new Square.PlayableSquare(Color.YELLOW, Shape.FLOWER);
        board[1][1] = new Square.PlayableSquare(Color.YELLOW, Shape.DIAMOND);
        board[0][1] = new Square.PlayableSquare(Color.GREEN, Shape.DIAMOND); // Goal

        // Additional squares
        board[0][0] = new Square.PlayableSquare(Color.RED, Shape.CROSS);
        board[0][2] = new Square.PlayableSquare(Color.BLUE, Shape.FLOWER);
        board[0][3] = new Square.PlayableSquare(Color.YELLOW, Shape.STAR);
        board[1][0] = new Square.PlayableSquare(Color.GREEN, Shape.STAR);
        board[1][3] = new Square.PlayableSquare(Color.RED, Shape.DIAMOND);
        board[2][3] = new Square.PlayableSquare(Color.GREEN, Shape.CROSS);
        board[3][1] = new Square.PlayableSquare(Color.YELLOW, Shape.CROSS);
        board[3][2] = new Square.PlayableSquare(Color.BLUE, Shape.DIAMOND);
        board[3][3] = new Square.PlayableSquare(Color.GREEN, Shape.FLOWER);

        goalPosition = new Position(0, 1);
    }

    private void setupLevel4() {
        // Start
        board[3][3] = new Square.PlayableSquare(Color.YELLOW, Shape.CROSS);
        board[2][3] = new Square.PlayableSquare(Color.YELLOW, Shape.STAR);
        board[1][3] = new Square.PlayableSquare(Color.BLUE, Shape.STAR);
        board[1][2] = new Square.PlayableSquare(Color.BLUE, Shape.FLOWER);
        board[1][1] = new Square.PlayableSquare(Color.RED, Shape.FLOWER);
        board[2][1] = new Square.PlayableSquare(Color.RED, Shape.DIAMOND);
        board[2][0] = new Square.PlayableSquare(Color.GREEN, Shape.DIAMOND);
        board[1][0] = new Square.PlayableSquare(Color.GREEN, Shape.CROSS);
        board[0][0] = new Square.PlayableSquare(Color.YELLOW, Shape.CROSS); // Goal

        // Additional squares
        board[0][1] = new Square.PlayableSquare(Color.BLUE, Shape.DIAMOND);
        board[0][2] = new Square.PlayableSquare(Color.RED, Shape.STAR);
        board[0][3] = new Square.PlayableSquare(Color.GREEN, Shape.FLOWER);
        board[2][2] = new Square.PlayableSquare(Color.YELLOW, Shape.DIAMOND);
        board[3][0] = new Square.PlayableSquare(Color.RED, Shape.CROSS);
        board[3][1] = new Square.PlayableSquare(Color.BLUE, Shape.CROSS);
        board[3][2] = new Square.PlayableSquare(Color.GREEN, Shape.STAR);

        goalPosition = new Position(0, 0);
    }

    private void setupLevel5() {
        // Start
        board[3][0] = new Square.PlayableSquare(Color.RED, Shape.DIAMOND);
        board[3][1] = new Square.PlayableSquare(Color.RED, Shape.STAR);
        board[2][1] = new Square.PlayableSquare(Color.BLUE, Shape.STAR);
        board[2][2] = new Square.PlayableSquare(Color.BLUE, Shape.CROSS);
        board[1][2] = new Square.PlayableSquare(Color.YELLOW, Shape.CROSS);
        board[0][2] = new Square.PlayableSquare(Color.YELLOW, Shape.FLOWER);
        board[0][1] = new Square.PlayableSquare(Color.GREEN, Shape.FLOWER);
        board[1][1] = new Square.PlayableSquare(Color.GREEN, Shape.DIAMOND);
        board[1][0] = new Square.PlayableSquare(Color.RED, Shape.DIAMOND); // Goal

        // Additional squares
        board[0][0] = new Square.PlayableSquare(Color.BLUE, Shape.DIAMOND);
        board[0][3] = new Square.PlayableSquare(Color.RED, Shape.CROSS);
        board[1][3] = new Square.PlayableSquare(Color.GREEN, Shape.STAR);
        board[2][0] = new Square.PlayableSquare(Color.YELLOW, Shape.DIAMOND);
        board[2][3] = new Square.PlayableSquare(Color.BLUE, Shape.FLOWER);
        board[3][2] = new Square.PlayableSquare(Color.GREEN, Shape.CROSS);
        board[3][3] = new Square.PlayableSquare(Color.YELLOW, Shape.STAR);

        goalPosition = new Position(1, 0);
    }

}
package com.example.eyeballmazegame;

import android.util.Log;

import java.util.ArrayList;
import java.util.List;

import com.example.eyeballmazegame.enums.Color;
import com.example.eyeballmazegame.enums.Direction;
import com.example.eyeballmazegame.enums.Message;
import com.example.eyeballmazegame.enums.Shape;

public class Game {
	private List<Level> levelList = new ArrayList<>();
	private int activeLevelIndex = -1; // The index of the currently level
	private Eyeball playerEyeball; // The player-controlled eyeball character

	public Game() {

	}

	public void addLevel(int height, int width) {
		levelList.add(new Level(height, width));
		activeLevelIndex = levelList.size() - 1;
	}

	public void addEyeball(int row, int column, Direction direction) {
		if (activeLevelIndex == -1)
			throw new IllegalStateException("No levels available in the game");
		this.playerEyeball = new Eyeball(row, column, direction);

		// Initialize eyeball's color and shape based on its starting position
		Level currentLevel = getLevel();
		Square square = currentLevel.getSquare(row, column);
		if (square instanceof Square.PlayableSquare) {
			Square.PlayableSquare playableSquare = (Square.PlayableSquare) square;
			playerEyeball.setColor(playableSquare.getColor());
			playerEyeball.setShape(playableSquare.getShape());
		}
	}

	public boolean canMoveTo(int newRow, int newCol) {
		Level currentLevel = getLevel();
		if (newRow < 0 || newRow >= currentLevel.getHeight() || newCol < 0 || newCol >= currentLevel.getWidth()) {
			return false;
		}
		Square targetSquare = currentLevel.getSquare(newRow, newCol);
		if (targetSquare instanceof Square.PlayableSquare) {
			Square.PlayableSquare playableSquare = (Square.PlayableSquare) targetSquare;
			return playerEyeball.isColorAndShapeMatch(playableSquare.getColor(), playableSquare.getShape());
		}
		return false;
	}

	public void moveTo(int newRow, int newCol) {
		if (canMoveTo(newRow, newCol)) {
			playerEyeball.moveTo(newRow, newCol);
			updateEyeballProperties(newRow, newCol);
			Log.d("Game", "Eyeball moved to: " + newRow + ", " + newCol);
			Log.d("Game", "New eyeball color: " + playerEyeball.getColor() + ", shape: " + playerEyeball.getShape());
		}
	}

	public Message MessageIfMovingTo(int newRow, int newCol) {
		Level currentLevel = getLevel();
		Log.d("Game", "Attempting to move to: " + newRow + ", " + newCol);
		Log.d("Game", "Current level dimensions: " + currentLevel.getHeight() + "x" + currentLevel.getWidth());

		if (newRow < 0 || newRow >= currentLevel.getHeight() || newCol < 0 || newCol >= currentLevel.getWidth()) {
			Log.d("Game", "Move is out of bounds");
			return Message.MOVING_OVER_BLANK;
		}

		Square targetSquare = currentLevel.getSquare(newRow, newCol);
		Log.d("Game", "Target square type: " + targetSquare.getClass().getSimpleName());

		if (targetSquare instanceof Square.PlayableSquare) {
			Square.PlayableSquare playableSquare = (Square.PlayableSquare) targetSquare;
			Log.d("Game", "Playable square color: " + playableSquare.getColor() + ", shape: " + playableSquare.getShape());
			Log.d("Game", "Eyeball color: " + playerEyeball.getColor() + ", shape: " + playerEyeball.getShape());

			if (playerEyeball.isColorAndShapeMatch(playableSquare.getColor(), playableSquare.getShape())) {
				Log.d("Game", "Move is OK");
				return Message.OK;
			} else {
				Log.d("Game", "Different shape and color");
				return Message.DIFFERENT_SHAPE_OR_COLOR;
			}
		}

		Log.d("Game", "Moving over blank");
		return Message.MOVING_OVER_BLANK;
	}

	public int getEyeballRow() {
		return playerEyeball.getRow();
	}

	public int getEyeballColumn() {
		return playerEyeball.getColumn();
	}

	public Direction getEyeballDirection() {
		return playerEyeball.getDirection();
	}

	public int getLevelWidth() {
		return getLevel().getWidth();
	}

	public int getLevelHeight() {
		return getLevel().getHeight();
	}

	public int getLevelCount() {
		return levelList.size();
	}

	public Color getEyeballColor() {
		return playerEyeball != null ? playerEyeball.getColor() : Color.BLANK;
	}

	public Shape getEyeballShape() {
		return playerEyeball != null ? playerEyeball.getShape() : Shape.BLANK;
	}

	private void updateEyeballProperties(int row, int col) {
		Level currentLevel = getLevel();
		Square square = currentLevel.getSquare(row, col);
		if (square instanceof Square.PlayableSquare) {
			Square.PlayableSquare playableSquare = (Square.PlayableSquare) square;
			playerEyeball.setColor(playableSquare.getColor());
			playerEyeball.setShape(playableSquare.getShape());
		}
	}

	public Level getLevel() {
		if (activeLevelIndex >= 0 && activeLevelIndex < levelList.size()) {
			return levelList.get(activeLevelIndex);
		}
		throw new IllegalStateException("Active level is not set correctly.");
	}

	public int getCompletedGoalCount() {
		return (int) getLevel().getGoals().stream()
				.filter(Position -> getLevel().isGoalCompleted(Position))
				.count();
	}

	public boolean hasBlankFreePathTo(int row, int column) {
		int startRow = getEyeballRow();
		int startColumn = getEyeballColumn();
		if (startRow == row) {
			for (int i = Math.min(startColumn, column); i <= Math.max(startColumn, column); i++) {
				if (!isSquareTraversable(startRow, i)) {
					return false;
				}
			}
		} else if (startColumn == column) {
			for (int i = Math.min(startRow, row); i <= Math.max(startRow, row); i++) {
				if (!isSquareTraversable(i, startColumn)) {
					return false;
				}
			}
		} else {
			return false;
		}
		return true;
	}

	private boolean isSquareTraversable(int row, int column) {
		Square square = getLevel().getSquare(row, column);
		return !(square instanceof Square.BlankSquare);
	}

	public boolean isMovementDirectionValid(int newRow, int newColumn) {
		boolean isDiagonal = (newRow != playerEyeball.getRow()) && (newColumn != playerEyeball.getColumn());
		if (isDiagonal) {
			return false; // Disallow diagonal movement.
		}

		// Check for backward movement based on direction
		switch (playerEyeball.getDirection()) {
			case UP:
				return !(newRow > playerEyeball.getRow());
			case DOWN:
				return !(newRow < playerEyeball.getRow());
			case LEFT:
				return !(newColumn > playerEyeball.getColumn());
			case RIGHT:
				return !(newColumn < playerEyeball.getColumn());
		}
		return true;
	}

	public Message checkDirectionMessage(int destRow, int destColumn) {
		boolean isDiagonal = (destRow != playerEyeball.getRow()) && (destColumn != playerEyeball.getColumn());
		if (isDiagonal) {
			return Message.MOVING_DIAGONALLY;
		} else if (!isMovementDirectionValid(destRow, destColumn)) {
			return Message.BACKWARDS_MOVE;
		}
		return Message.OK;
	}

	public Message checkMessageForBlankOnPathTo(int row, int column) {
		if (!hasBlankFreePathTo(row, column)) {
			return Message.MOVING_OVER_BLANK;
		}
		return Message.OK;
	}

	private void checkAndCompleteGoalAt(int row, int column) {
		Position position = new Position(row, column);
		if (getLevel().hasGoalAt(position)) {
			getLevel().completeGoal(position);
		}
	}

	private void updateEyeballDirection(int currentRow, int newRow, int currentColumn, int newColumn) {
		// Determine the new direction based on the change in position
		if (newRow < currentRow) {
			playerEyeball.setDirection(Direction.UP);
		} else if (newRow > currentRow) {
			playerEyeball.setDirection(Direction.DOWN);
		} else if (newColumn < currentColumn) {
			playerEyeball.setDirection(Direction.LEFT);
		} else if (newColumn > currentColumn) {
			playerEyeball.setDirection(Direction.RIGHT);
		}
	}

	public boolean isDirectionOK(int newRow, int newColumn) {
		// Determine if the movement is diagonal
		boolean isDiagonalMovement = (newRow != playerEyeball.getRow()) && (newColumn != playerEyeball.getColumn());
		if (isDiagonalMovement) {
			return false; // Diagonal movement is not permitted
		}

		// Determine if the movement is backward based on the eyeball's current
		// direction
		switch (playerEyeball.getDirection()) {
			case UP:
				return newRow <= playerEyeball.getRow(); // Movement up should not increase the row value
			case DOWN:
				return newRow >= playerEyeball.getRow(); // Movement down should not decrease the row value
			case LEFT:
				return newColumn <= playerEyeball.getColumn(); // Movement left should not increase the column value
			case RIGHT:
				return newColumn >= playerEyeball.getColumn(); // Movement right should not decrease the column value
			default:
				return true; // Allow movement in other cases
		}
	}

	public List<Position> getGoals() {
		return new ArrayList<>(levelList.get(activeLevelIndex).getGoals());
	}

}

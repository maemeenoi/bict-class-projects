package com.example.eyeballmazegame;

import java.util.HashSet;
import java.util.Set;

public class Level {
	// Stores the width and height of the level.
	private final int width, height;
	//A two-dimensional array that represents the layout of squares in the level.
	private final Square[][] squares;
	// A set of positions representing where goals are located within the level.
	private final Set<Position> goals = new HashSet<>();
	//A set of positions representing the goals that have been completed.
	private final Set<Position> completedGoals = new HashSet<>();

	// Initializes the level with the specified dimensions and sets all squares to be blank initially, preparing the level for gameplay.
	public Level(int height, int width) {
		this.width = width;
		this.height = height;
		squares = new Square[height][width];
		// Initialize all squares as blank squares
		for (int i = 0; i < height; i++) {
			for (int j = 0; j < width; j++) {
				squares[i][j] = new Square.BlankSquare();
			}
		}
	}


	// Sets the type of square at the specified position, updating the level’s layout.
	public void setSquare(int row, int column, Square square) {
		validatePosition(row, column);
		squares[row][column] = square;
	}

	// Retrieves the square at a given position, allowing for the examination or manipulation of its state.
	public Square getSquare(int row, int column) {
		validatePosition(row, column);
		return squares[row][column];
	}

	//Returns a new set containing all the goal positions, safeguarding the original set against direct modifications.
	public Set<Position> getGoals() {
		return new HashSet<>(goals);
	}


	public boolean hasGoalAt(Position position) {
		return goals.contains(position) && !completedGoals.contains(position);
	}

	//Adds a goal to the level at the specified position, after validating the position.
	public void addGoal(Position goal) {
		validatePosition(goal.getRow(), goal.getColumn());
		goals.add(goal);
	}
	// Checks if the goal at the specified position is completed
	public boolean isGoalCompleted(Position position) {
		return completedGoals.contains(position);
	}

	// Marks the goal at the specified position as completed
	public void completeGoal(Position position) {
		completedGoals.add(position);
	}


	// Validates that a given position is within the bounds of the level, throwing an exception if it is not.
	private void validatePosition(int row, int column) {
		if (row < 0 || row >= height || column < 0 || column >= width) {
			throw new IllegalArgumentException("Position out of bounds: row=" + row + " column=" + column);
		}
	}
	// Provide access to the level's width and height, which are useful for size-dependent operations and validations.
	public int getWidth() {
		return width;
	}

	// Provide access to the level's width and height, which are useful for size-dependent operations and validations.
	public int getHeight() {
		return height;
	}
}

package com.example.eyeballmazegame;


import com.example.eyeballmazegame.enums.Color;
import com.example.eyeballmazegame.enums.Direction;
import com.example.eyeballmazegame.enums.Shape;

public class Eyeball {
	private Color color = Color.BLANK;
	private Shape shape = Shape.BLANK;
	private int row, column;
	private Direction direction;

	public Eyeball(int row, int column, Direction direction) {
		this.row = row;
		this.column = column;
		this.direction = direction;
	}

    // Getter Setter

	public Shape getShape() {
		return shape;
	}

	public void setShape(Shape shape) {
		this.shape = shape;
	}

	public int getRow() {
		return row;
	}

	public int getColumn() {
		return column;
	}

	public Direction getDirection() {
		return direction;
	}

	public void setDirection(Direction direction) {
		this.direction = direction;
	}

	public Color getColor() {
		return color;
	}

	public void setColor(Color color) {
		this.color = color;
	}

	// Move eyeball to new position
	public void moveTo(int newRow, int newColumn) {
		this.row = newRow;
		this.column = newColumn;
	}

	// Check if color or shape match
	public boolean isColorAndShapeMatch(Color squareColor, Shape squareShape) {
		return this.color == squareColor || this.shape == squareShape;
	}

}

package com.example.eyeballmazegame.interfaces;

import com.example.eyeballmazegame.Square;
import com.example.eyeballmazegame.enums.Color;
import com.example.eyeballmazegame.enums.Shape;

public interface ISquareHolder {
	public void addSquare(Square square, int row, int column);

	public Color getColorAt(int row, int column);

	public Shape getShapeAt(int row, int column);
}

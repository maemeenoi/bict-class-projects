package com.example.eyeballmazegame;

import com.example.eyeballmazegame.enums.Color;
import com.example.eyeballmazegame.enums.Shape;

// Defines a square on the game board
public class Square {

	// Defines a playable square with specific color and shape properties
	public static class PlayableSquare extends Square {
		private final Color color; // The color attribute of the playable square
		private final Shape shape; // The shape attribute of the playable square

		public PlayableSquare(Color color, Shape shape) {
			this.color = color;
			this.shape = shape;
		}

		// Retrieves the color of the PlayableSquare
		public Color getColor() {
			return color;
		}

		// Retrieves the shape of the PlayableSquare
		public Shape getShape() {
			return shape;
		}

		// Returns a string representation of the PlayableSquare
		@Override
		public String toString() {
			return "PlayableSquare{" +
					"color=" + color +
					", shape=" + shape +
					'}';
		}
	}

	// Defines a blank square with no unique attributes
	public static class BlankSquare extends Square {

		// Returns a string representation of the BlankSquare
		@Override
		public String toString() {
			return "BlankSquare{}";
		}
	}


}

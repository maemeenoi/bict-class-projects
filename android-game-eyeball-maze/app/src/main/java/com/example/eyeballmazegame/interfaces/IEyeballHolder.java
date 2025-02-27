package com.example.eyeballmazegame.interfaces;

import com.example.eyeballmazegame.enums.Direction;

public interface IEyeballHolder {
	public void addEyeball(int row, int column, Direction direction);

	public int getEyeballRow();

	public int getEyeballColumn();

	public Direction getEyeballDirection();
}

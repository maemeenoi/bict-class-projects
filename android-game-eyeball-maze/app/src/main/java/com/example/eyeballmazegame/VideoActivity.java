package com.example.eyeballmazegame;

import android.net.Uri;
import android.os.Bundle;
import android.widget.MediaController;
import android.widget.VideoView;
import androidx.appcompat.app.AppCompatActivity;

//This class defines an activity for playing a video, specifically used to display the game rules video in the application.
public class VideoActivity extends AppCompatActivity {
    //This method is called when the activity is first created.
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        //Sets the layout for the activity using the specified XML layout file, activity_video, which should contain a VideoView.
        setContentView(R.layout.activity_video);

        // Initializes a VideoView object by finding it in the layout using its ID. This view will be used to display the video.
        VideoView videoView = findViewById(R.id.videoView);
        String videoPath = "android.resource://" + getPackageName() + "/" + R.raw.game_rules;
        //Parses the string path to the video into a Uri object, which is required to set the video source in the VideoView.
        Uri uri = Uri.parse(videoPath);
        //Sets the source of the video for the VideoView to the Uri of the game rules video.
        videoView.setVideoURI(uri);

        //Creates a MediaController, a UI component that provides media controls (like play, pause, and seek) for controlling video playback.
        MediaController mediaController = new MediaController(this);
        videoView.setMediaController(mediaController);
        mediaController.setAnchorView(videoView);

        //Starts playing the video as soon as the activity is created and the layout is set.
        videoView.start();
    }
}
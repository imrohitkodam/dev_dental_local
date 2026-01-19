 jQuery(window).on('load',function() {

    let vid = document.getElementById("myVideoUrl");
    lessonStartTime = new Date();

    vid.currentTime = plugdataObject.seekTo;
    var tjvideourl_Finish = false;
    var tjvideourl_Pause = false;
    var tjvideourl_tracked = false;
    
    tjInterval = setInterval(function () { 
		if(!tjvideourl_Pause && !tjvideourl_Finish && !tjvideourl_tracked)
		{
            console.log('Video URL video is just started');

			plugdataObject.current_position = vid.currentTime;
			plugdataObject.total_content = vid.duration;
			plugdataObject.lesson_status = "incomplete";
			plugdataObject.time_spent = 1;
			lessonStartTime = new Date();
			updateData(plugdataObject);
		}
	}, 1000);

    tjvimeo_myInterval = setInterval(function () {

        if(!tjvideourl_Pause && !tjvideourl_Finish)
        {
            console.log('Video URL video is playing');
            lessonStoptime = new Date();
            var timespentonLesson = lessonStoptime - lessonStartTime;
            var timeinseconds = Math.round(timespentonLesson / 1000);
            lessonStartTime = new Date();

            tjvideourl_tracked = true;
            plugdataObject.current_position = vid.currentTime;
            plugdataObject.total_content = vid.duration;
            plugdataObject.lesson_status = "incomplete";
            plugdataObject.time_spent = timeinseconds;
            lessonStartTime = new Date();
            updateData(plugdataObject);
        }

        vid.onended = function(e) {
            console.log('Video URL video is completed');
            tjyoutube_Finish = true;
            lessonStoptime = new Date();
            var timespentonLesson = lessonStoptime - lessonStartTime;

            var timeinseconds = Math.round(timespentonLesson / 1000);
            plugdataObject.current_position = vid.currentTime;
            plugdataObject.total_content = vid.duration;
            plugdataObject.lesson_status = 'completed';
            plugdataObject.time_spent = timeinseconds;
            
            lessonStartTime = null;
            delete lessonStartTime;
            updateData(plugdataObject);
        }

    }, 10000);
});


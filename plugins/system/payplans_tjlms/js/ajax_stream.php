<?php
set_time_limit(0);
ob_implicit_flush(true);
ob_end_flush();

//~ $jinput       = JFactory::getApplication()->input;
//~ echo "<br>". $appCount   = $jinput->get('appCount');
//~ echo "<br>". $current_running_app_count   = $jinput->get('current_running_app_count');

$appCount = 8;
$current_running_app_count = 1;

if ($current_running_app_count <= $appCount)
{
	sleep(1);

	// Progress
	$p = ($current_running_app_count+1)*10;
	$response = array('message' => $p . '% ', 'progress' => $p);

	echo json_encode($response);
}



/*for($i = 0; $i < 10; $i++){
    //Hard work!!
    sleep(1);
    $p = ($i+1)*10; //Progress
    $response = array(  'message' => $p . '% ',
                        'progress' => $p);

    echo json_encode($response);
}*/

sleep(1);
$response = array(  'message' => 'Complete',
                    'progress' => 100);

echo json_encode($response);

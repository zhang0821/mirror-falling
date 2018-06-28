<?php
	
	function p($obj)
	{
	    echo "<pre>";
	        print_r($obj);
	    echo "</pre>";
	}

	$id = @$_GET['id'];
	$name = @$_GET['name'];
	$type = $_GET['type'];

	$mysqli = mysqli_connect("localhost","root","","mirror"); 
	$sql = "select * from user where name = '{$name}'";
	$result = $mysqli -> query($sql);
	$result = $result -> fetch_array(MYSQLI_ASSOC);


	if($type == "schedule") {
		$schedule = unserialize($result['schedule']);
		echo json_encode($schedule);
	} else {
		$message = unserialize($result['message']);
		echo json_encode($message);
	}

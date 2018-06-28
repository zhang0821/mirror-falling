<?php

	$pm = $_POST['pm'];

	if($pm == '20') {
		$arr = [
			'num' => $pm,
			'info' => '优'
		];
		echo json_encode($arr);
	} else {
		$arr = [
			'num' => $pm,
			'info' => '差'
		];
		echo json_encode($arr);
	}
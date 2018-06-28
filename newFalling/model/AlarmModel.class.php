<?php
//=======================================================================
// File:        ALARMODEL.PHP
// Description: PHP calculation difference date Library.
// Created:     2016-08-06
// Copyright (c) CCNU-ACTL.Yangz All rights reserved.
//========================================================================
namespace Home\Model;
use Think\Model;
class AlarmModel extends Model {
/*
 * 判断两个datetime变量之差是否超过多少分钟
 * @param $datetime1 string
 * @param $datetime2 string
 * @param $MinuteThreshold int
 * @return bool,true表示超过，false表示没超过
 * */
 /***************判断两个datetime变量之差是否超过多少分钟***************/
    function DifferentDateTime($datetime1, $datetime2, $MinuteThreshold){
		$DifferentDate=floor((strtotime($datetime1)-strtotime($datetime2))/86400);
		$DifferentHour=floor((strtotime($datetime1)-strtotime($datetime2))%86400/3600);
		$DifferentMinute=floor((strtotime($datetime1)-strtotime($datetime2))%86400/60);
		if($DifferentDate>0){
			return true;
		}
		else if($DifferentHour>0){
			return true;
		}
		else if($DifferentMinute>$MinuteThreshold){
			return true;
		}
		else{
			return false;
		}
	}
}
?>


<?php
//=======================================================================
// File:        MESSAGEMODEL.PHP
// Description: PHP SEND MESSAGE Library.
// Created:     2016-08-04
// Copyright (c) CCNU-ACTL.Yangz All rights reserved.
//========================================================================
namespace Home\Model;
use Think\Model;
class MessageModel extends Model {
/*
 * 发送短信
 * @param $to string
 * @param $title string
 * @param $content string
 * @return bool
 * */
	/***************短信发送函数***************/
    function SendMessage($mobile, $title, $content){
		header("Content-Type: text/html; charset=UTF-8");
		$flag = 0; 
		$params='';//要post的数据 
		$argv = array( 
			'name'=>'15623053086',                //必填参数。用户账号
			'pwd'=>'3BD5218FABAC9211395461F500C2',//必填参数。（web平台：基本资料中的接口密码）
			'content'=>$content, //必填参数。发送内容（1-500 个汉字）UTF-8编码
			'mobile'=>$mobile,   //必填参数。手机号码。多个以英文逗号隔开
			'stime'=>'',         //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
			'sign'=>$title,      //必填参数。用户签名。
			'type'=>'pt',        //必填参数。固定值 pt
			'extno'=>''          //可选参数，扩展码，用户定义扩展码，只能为数字
		); 
		foreach ($argv as $key=>$value){ 
			if ($flag!=0){ 
				$params .= "&"; 
				$flag = 1; 
			} 
			$params.= $key."="; $params.= urlencode($value);// urlencode($value); 
			$flag = 1; 
		} 
		$url = "http://web.wasun.cn/asmx/smsservice.aspx?".$params; //提交的url地址
		$con = substr( file_get_contents($url), 0, 1 );  //获取信息发送后的状态		
		if($con == '0'){
			return true;
		}
		else{
			return false;
		}
	}
	/***************判断是否是手机号码***************/
	function isMobile($mobile) {
		if (!is_numeric($mobile)) {
			return false;
		}
		return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
	}
}
?>


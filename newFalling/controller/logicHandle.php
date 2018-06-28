<?php
namespace Home\Controller;
use Think\Controller;
$GLOBALS['ip'] = '192.168.1.102';
$GLOBALS['buf'];//soceket接收到的数据
$GLOBALS['send'];
$GLOBALS['USERS']=array();
$GLOBALS['userId'];
function startClientSocket($string){
	$soc=new MysocketController();
	echo $soc->socketClient($string);
}

function startServerSocket(){
	$soc=new MysocketController();
	echo $soc->socketServer();
}

function cutString($string){

	$filename=substr($string,0,strpos($string, '#')).'.txt' ;//文件名
	$username=substr($string,strpos($string, '#')+1,(strlen($string)-strlen($filename)-1));//获取用户名
	
	$GLOBALS['userId']=$username;

	$len=strlen($filename);
	$newStr=substr($filename,0,($len-4));	
	$sendStr=substr($newStr,-1);
	$ifok=substr($newStr,0,2);
	if(is_dir("D:\PHP_Project\HTTP-LoRa/".$username."/")){
		if(is_dir("D:\PHP_Project\HTTP-LoRa/".$username."/".$sendStr."/")){
				$file="D:\PHP_Project\HTTP-LoRa/111/".$string;
				$newFile="D:\PHP_Project\HTTP-LoRa/".$username."/".$sendStr."/".$filename; 
				copy($file,$newFile);
				unlink($file);
			}
			else{
				mkdir("D:\PHP_Project\HTTP-LoRa/".$username."/".$sendStr."/");
				$file="D:\PHP_Project\HTTP-LoRa/111/".$string;
				$newFile="D:\PHP_Project\HTTP-LoRa/".$username."/".$sendStr."/".$filename; 
				copy($file,$newFile);
				unlink($file);
			}
	}
	else{

		rename("D:\PHP_Project\HTTP-LoRa/111/","D:\PHP_Project\HTTP-LoRa/".$username."/");
		mkdir("D:\PHP_Project\HTTP-LoRa/".$username."/".$sendStr."/");
		$file="D:\PHP_Project\HTTP-LoRa/111/".$string;
		$newFile="D:\PHP_Project\HTTP-LoRa/".$username."/".$sendStr."/".$filename; 
		copy($file,$newFile);
		unlink($file);
	}
	if ($ifok!="ok") {
		$info=substr($newStr,0,3);
		rename("D:\PHP_Project\HTTP-LoRa/".$username."/".$sendStr."/".$filename,"D:\PHP_Project\HTTP-LoRa/".$username."/".$sendStr."/".$info.".txt");
	}
	if($ifok=="ok"){
		if(strlen($newStr)==3){
			// if($sendStr==1){
			// 	$sendStr=5;
			// }
			// $strToBoard=$username.($sendStr-1);//发生送文件夹名字以及用户名字
			$strToBoard=$username.$sendStr;
			startClientSocket($strToBoard);
		}
	}
}
//定时清空数据库表
function deleteDB(){
	M()->query($sql = 'TRUNCATE table `send`');
}

function minuteDiff($start,$end)
{
      $DifferentMinute=floor((strtotime($start)-strtotime($end))%86400/60);
      return $DifferentMinute;
}
//存储每5S一次的数据到数据库

function dataToBase()
{
	
	unset($GLOBALS['send']);
	$sendNum;
	$toDB='nothing';
	$stateClass='continuous';//continuous 持续
	if($GLOBALS['buf']){
		$j=0;
		$GLOBALS['send'][0]=$GLOBALS['buf'][0];
		for($i=0;$i<count($GLOBALS['buf']);$i++){
			if($GLOBALS['buf'][$i]=='fall' || $GLOBALS['buf'][$i]=='jump' || $GLOBALS['buf'][$i]=='upstairs' || $GLOBALS['buf'][$i]=='downstairs' || $GLOBALS['buf'][$i]=='standup' || $GLOBALS['buf'][$i]=='sitdown') {
					$toDB=$GLOBALS['buf'][$i];
					$stateClass='sudden';
					break;
					}
			if($GLOBALS['buf'][$i]!=$GLOBALS['send'][$j]){
				if($j==0)
					$sendNum[$j]=$i;
				else
					$sendNum[$j]=$i-$sendNum[($j-1)];//
				$j++;
				$GLOBALS['send'][$j]=$GLOBALS['buf'][$i];
			}
			if($i==(count($GLOBALS['buf'])-1) && count($GLOBALS['buf'])!=count($sendNum))
			{
				$sendNum[$j]=($i+1)-$sendNum[($j-1)];
			}
		}
		$GLOBALS['test']=$i;
		$GLOBALS['sendNum']=$sendNum;
	//找出sendNum数组中的最大的
		$max=$sendNum[0];
		$maxNo=0;
		for($k=1;$k<count($sendNum);$k++){
			if($sendNum[$k]>$max){
				$max=$sendNum[$k];
				$maxNo=$k;
			}
		}
		if($stateClass=='continuous'){
			$toDB=$GLOBALS['send'][$maxNo];
		}
	//更改online用户表的当前状态
		$userInfoTable=M('onlineuser');
		$hisInfoTable=M('userhistory');
		$condition['name']=$GLOBALS['userId'];
		$userInfo=$userInfoTable->where($condition)->limit(1)->find();
		$hisInfo=$hisInfoTable->where($condition)->order('id desc')->limit(1)->find();

		if($toDB=='#'){
			$toDB=$userInfo['currentstate'];
		}
		if ($toDB=='jump' || $toDB=='fall') {
	//突发的这两类动作直接存次数			
	//存放历史数据时，先判断是否有当天数据，无，则插入一条新的
		if($hisInfo['time']==date('Y-m-d')){
			$dataToHis[$toDB]=($hisInfo[$toDB]+1);
			$condition2['name']=$GLOBALS['userId'];
			$condition2['id']=$hisInfo['id'];
			$hisInfoTable->where($condition2)->save($dataToHis);
		}
		else {
			$newDataToHis['name']=$GLOBALS['userId'];
			$newDataToHis[$toDB]=1;
			$newDataToHis['time']=date('Y-m-d');
			$hisInfoTable->add($newDataToHis);
		}

	//跌倒时，发送邮件，上次发送超过60分钟，则不发送
			$sendMail=D('Mail');
			if($toDB=='fall'){
				$fallTimeNow=date("Y-m-d H:i:s",time());
				$sendAddress=M('user')->where($condition)->limit(1)->find();
				if(minuteDiff(date("Y-m-d H:i:s",time()),$sendAddress['lastMailTime'])>60){
					$sendMail->SendMail($sendAddress['email'],"跌倒报警","您的家人".$condition['name']."在北京时间".$fallTimeNow."跌倒了！请尽快确认Ta的安全(此邮件为通知邮件，无需回复，蟹蟹！)");
						$saveinfo['lastMailTime']=date("Y-m-d H:i:s",time());
						M('user')->where($condition)->save($saveinfo);
				}
			}
		}
		if($userInfo['currentstate']==$toDB){ //实时状态和之前的当前状态相同时，只更改时间
			$toDbInfo['currenttime']=date("Y-m-d H:i:s",time());
			$userInfoTable->where($condition)->save($toDbInfo);

		}
			else if($userInfo['currentstate']!=$toDB){ //实时状态不同于在线表的当前状态时，算出在线表的这次
				if($userInfo['currentstate']!='jump' && $userInfo['currentstate']!='fall'){
					$hisInfoSecond=$hisInfoTable->where($condition)->order('id desc')->limit(1)->find();

					// if($userInfo['laststate']=='downstairs' || $userInfo['laststate']=='upstairs'){
					// 	$userInfo['laststate']='stairs';
					// }
					// if($userInfo['currentstate']=='nothing' && ($userInfo['laststate']=='standup' || $userInfo['laststate']=='sitdown' ||$userInfo['laststate']=='stairs')){
					if($userInfo['currentstate']=='nothing' && ($userInfo['laststate']=='standup' || $userInfo['laststate']=='sitdown' ||$userInfo['laststate']=='upstairs' ||$userInfo['laststate']=='downstairs')){
						if($hisInfoSecond['time']==date('Y-m-d')){
							$timeToHis[$userInfo['laststate']]=$hisInfoSecond[$userInfo['laststate']]+minuteDiff($userInfo['currenttime'],$userInfo['lasttime']);//ok
						}
						else
							$timeToHis[$userInfo['laststate']]=minuteDiff($userInfo['currenttime'],$userInfo['lasttime']);
					}
					else {
						if($hisInfoSecond['time']==date('Y-m-d')){
							$timeToHis[$userInfo['currentstate']]=$hisInfoSecond[$userInfo['currentstate']]+minuteDiff($userInfo['currenttime'],$userInfo['lasttime']);
						}
						else
							$timeToHis[$userInfo['currentstate']]=minuteDiff($userInfo['currenttime'],$userInfo['lasttime']);
					}			
		//存放历史数据时，先判断是否有当天数据，无，则插入一条新的
					if($hisInfoSecond['time']==date('Y-m-d')){
						$condition3['id']=$hisInfoSecond['id'];
						$condition3['name']=$GLOBALS['userId'];
						$hisInfoTable->where($condition3)->save($timeToHis);
					}
					else {
						$timeToHis['time']=date("Y-m-d");
						$timeToHis['name']=$GLOBALS['userId'];
						$hisInfoTable->add($timeToHis);
					}
				}
		//更新在线表用户的状态
					$toDbInfo['laststate']=$userInfo['currentstate'];
					$toDbInfo['lasttime']=$userInfo['currenttime'];
					$toDbInfo['currentstate']=$toDB;
					$toDbInfo['currenttime']=date("Y-m-d H:i:s",time());
					$userInfoTable->where($condition)->save($toDbInfo);

			}
	}	
	unset($GLOBALS['buf']);
}


// function cutString($string){

// 	$filename=substr($string,0,strpos($string, '#')).'.txt' ;//文件名
// 	$username=substr($string,strpos($string, '#')+1,(strlen($string)-strlen($filename)-1));//获取用户名
	
// 	$len=strlen($filename);
// 	$GLOBALS['test']=$filename.'/'.$username;

// 	$newStr=substr($filename,0,($len-4));	
// 	$sendStr=substr($newStr,-1);
// 	$ifok=substr($newStr,0,2);
// 	if(is_dir("D:\PHP_Project\HTTP-LoRa/".$sendStr."/")){
// 		$file="D:\PHP_Project\HTTP-LoRa/111/".$string; 
//   		$newFile="D:\PHP_Project\HTTP-LoRa/".$sendStr."/".$filename;
// 		 copy($file,$newFile);
// 		 unlink($file);
// 	}
// 	else{
// 		rename("D:\PHP_Project\HTTP-LoRa/111/","D:\PHP_Project\HTTP-LoRa/".$sendStr."/");
// 	}
// 	if ($ifok!="ok") {
// 		$info=substr($newStr,0,3);
// 		rename("D:\PHP_Project\HTTP-LoRa/".$sendStr."/".$newStr.".txt","D:\PHP_Project\HTTP-LoRa/".$sendStr."/".$info.".txt");
// 	}
// 	if($ifok=="ok"){
// 		if(strlen($newStr)==3){
// 			// if($sendStr==1){
// 			// 	$sendStr=5;
// 			// }
// 			// startClientSocket(($sendStr-1));
// 			sleep(1);
// 			startClientSocket($sendStr);
// 		}
// 	}
// }

// function dataToBase()
// {
// 	unset($GLOBALS['send']);
// 	$sendNum;
// 	$toDB='nothing';
// 	if($GLOBALS['buf']){
// 		$j=0;
// 		$GLOBALS['send'][0]=$GLOBALS['buf'][0];
// 		for($i=0;$i<count($GLOBALS['buf']);$i++){
// 			if ($GLOBALS['buf'][$i]=='fall_front' || $GLOBALS['buf'][$i]=='fall_back' || $GLOBALS['buf'][$i]=='fall_right' || $GLOBALS['buf'][$i]=='fall_left') {
// 				$toDB='fall';
// 				break;
// 			}
// 			if($GLOBALS['buf'][$i]!=$GLOBALS['send'][$j]){
// 				if($j==0)//
// 					$sendNum[$j]=$i;
// 				else //
// 					$sendNum[$j]=$i-$sendNum[($j-1)];//
// 				$j++;
// 				$GLOBALS['send'][$j]=$GLOBALS['buf'][$i];
// 			}
// 			if($i==(count($GLOBALS['buf'])-1) && count($GLOBALS['buf'])!=count($sendNum))
// 			{
// 				$sendNum[$j]=($i+1)-$sendNum[($j-1)];
// 			}
// 		}
// 		//找出sendNum数组中的最大的
// 		$max=$sendNum[0];
// 		$maxNo=0;
// 		for($k=1;$k<count($sendNum);$k++){
// 			if($sendNum[$k]>$max){
// 				$max=$sendNum[$k];
// 				$maxNo=$k;
// 			}
// 		}
// 		if($toDB!='fall'){
// 			$toDB=$GLOBALS['send'][$maxNo];
// 		}
// 		$userInfoTable=M('onlineuser');
// 		$condition['name']='123';
// 		$userInfo=$userInfoTable->where($condition)->limit(1)->find();

// 		$toDbInfo['laststate']=$userInfo['currentstate'];
// 		$toDbInfo['lasttime']=$userInfo['currenttime'];
// 		if($toDB=='#')
// 			$toDB=$userInfo['currentstate'];
// 		$toDbInfo['currentstate']=$toDB;

// 		$toDbInfo['currenttime']=date("Y-m-d H:i:s",time());
// 		$userInfoTable->where($condition)->save($toDbInfo);
// 	}	
// 	unset($GLOBALS['buf']);
// }


// function socketServerOpen()
// {
// 	set_time_limit(0);
// 	$sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
// 	// $ret = socket_bind($sock,'192.168.1.102','123');
// 	$ret = socket_bind($sock,$GLOBALS['ip'],'123');
// 	$ret = socket_listen($sock,4);
// 	$count = 0;
// 	 do {
// 	    if (($msgsock = socket_accept($sock)) < 0) {
// 	        break;
// 	     } else {
// 	         //发到客户端
// 	        $msg ="12331233";
// 	        socket_write($msgsock, $msg, strlen($msg));         
// 		    $buf = socket_read($msgsock,8192); 
// 	        if(++$count >= 5){
// 	            break;
// 	         };
// 	     }
// 	    //echo $buf;
// 	     socket_close($msgsock);
	 
// 	 } while (true);
 
//  	socket_close($sock);
// }

// function dataToBase()
// {
// 	unset($GLOBALS['send']);
// 	unset($GLOBALS['sendNum']);
// 	$toDB='nothing';
// 	if($GLOBALS['buf']){
// 		$j=0;
// 		$GLOBALS['send'][0]=$GLOBALS['buf'][0];
// 		for($i=0;$i<count($GLOBALS['buf']);$i++){
// 			if ($GLOBALS['buf'][$i]=='fall_front' || $GLOBALS['buf'][$i]=='fall_back' || $GLOBALS['buf'][$i]=='fall_right' || $GLOBALS['buf'][$i]=='fall_left') {
// 				$toDB='fall';
// 				break;
// 			}
// 			if($GLOBALS['buf'][$i]!=$GLOBALS['send'][$j]){
// 				if($j==0)//
// 					$GLOBALS['sendNum'][$j]=$i;
// 				else //
// 					$GLOBALS['sendNum'][$j]=$i-$GLOBALS['sendNum'][($j-1)];//
// 				$j++;
// 				$GLOBALS['send'][$j]=$GLOBALS['buf'][$i];
// 			}
// 			if($i==(count($GLOBALS['buf'])-1) && count($GLOBALS['buf'])!=count($GLOBALS['sendNum']))
// 			{
// 				$GLOBALS['sendNum'][$j]=($i+1)-$GLOBALS['sendNum'][($j-1)];
// 				// $GLOBALS['sendNum'][$j]=($i+1);
// 			}
// 		}
// 		//找出sendNum数组中的最大的
// 		$max=$GLOBALS['sendNum'][0];
// 		$maxNo=0;
// 		for($k=1;$k<count($GLOBALS['sendNum']);$k++){
// 			if($GLOBALS['sendNum'][$k]>$max){
// 				$max=$GLOBALS['sendNum'][$k];
// 				$maxNo=$k;
// 			}
// 		}
// 		if($toDB!='fall'){
// 			$toDB=$GLOBALS['send'][$maxNo];
// 		}
// 			$test=M('teststory');
// 			$testinfo['state']=$toDB;
// 			$testinfo['time']=date("Y-m-d H:i:s",time());
// 			$test->add($testinfo);
// 	}	
// 	unset($GLOBALS['buf']);
// }
?>
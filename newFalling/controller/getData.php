<?php 
include_once('database.php');
	function getUersHisInfo($date){
		$hisInfoReturn;
		$i=0;
		$hisdate=date("Y-m-d",strtotime("-".$date." day"));
		$dbConnect=new Database();
		$userInfo=$dbConnect->query_users_hisInfo($hisdate);
		if ($userInfo)
		{
			while ($row= mysql_fetch_array($userInfo)){
				$hisInfoReturn[$i]['name']=$row['name'];
				$hisInfoReturn[$i]['walk']=$row['walk'];
				$hisInfoReturn[$i]['run']=$row['run'];
				$hisInfoReturn[$i]['stairs']=$row['stairs'];
				$hisInfoReturn[$i]['sitdown']=$row['sitdown'];
				$hisInfoReturn[$i]['stand']=$row['sitdown'];
				$hisInfoReturn[$i]['sitdown']=$row['sitdown'];
				$i++;
			}
			echo json_encode($hisInfoReturn);
		}
		else
		{
			echo  "无历史数据";
		}
	}

	function getOnlineUserInfo(){
		$userInfoReturn;
		$i=0;
		$dbConnect=new Database();
		$userInfo=$dbConnect->query_online_users();
		if($userInfo){
			$userInfoReturn= mysql_fetch_array($userinfo);
			echo "1";
			// echo json_encode($userInfoReturn);
		}
		else{
			echo '0';
		}
	}


	$_GET['method']();

 ?>
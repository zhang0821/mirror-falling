<?php
include('database.php');

/****************登陆检测******************/
	 function loginCheck(){
		$msgReturn;
		$dbConnect=new Database();
		$userinfo=$dbConnect->query_user_by_name($_POST['username']);
		if($userinfo){
			$row= mysql_fetch_array($userinfo);//无论是取一条还是几条，都要fetch
			if($_POST['password']==$row['password']){
				header("Location:../html/mainPage.html");
			}
			else{
				echo "用户名或密码错误！";
			}
		}
		else{
			echo "获取信息失败";
			// echo phpinfo();
		}
	}
	$_GET['method'](); //ajax请求时，传递进来的参数
?>
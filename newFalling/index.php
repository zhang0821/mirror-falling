<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width:1080px, initial-scale=1.0, maximum-scale=1.0"/>
<script type="text/javascript" src="js/jquery.min.js"></script>    
<link rel="stylesheet" href="css/login.css" type="text/css" />
<link rel="shortcut icon" href="img/title11.ico" type="image/x-icon" />
<title>姿态检测登录界面</title>
</head>
<body>

	<div class="pctContainer pctContainer1">
		
	</div>
	<div class="pctContainer pctContainer2">
		
	</div>
	<div class="bear bear1">
		
	</div>
	<div class="bear bear2">
		
	</div>
	<div class="bear bear3">
		
	</div>
	<div class="bear bear4">
		
	</div>
	<div class="login_body">
		<form id="loginform" class="form-vertical" method="post" action="controller/login.php?method=loginCheck">
		    <input type="text" class="username" name="username" placeholder="账户名" />        
		    <input type="password" class="pwd" name="password"  placeholder="密码" />                  
		    <input type="submit" class="login" value="登陆">
		   <!--  <div class="sign" href="www.baidu.com"  target="_blank"> 注册</div> -->
		</form>
	</div> 
</body>
</html>
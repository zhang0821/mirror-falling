<?php

function connect_database()
{
  // $handle=mysql_connect("192.168.1.102","root","zhang");
 $handle=mysql_connect("localhost","root","zhang");
 mysql_query("set names 'utf8'");
 mysql_select_db("bodydetect");
 return $handle;
}


class Database
{
  var $handle;

   function __construct()
   {
     $this->handle=connect_database();
   }

   function __destruct ( )
   {
     mysql_close($this->handle);
   }

//取出所有在线用户信息
   function query_online_users()
   {
    return mysql_query('select * from onlineuser',$this->handle);
   }
//在在历史表中获取所有用户历史信息
   function query_users_hisInfo($date)
   {
     $sql='select * from user where user.time='.$date;
     return mysql_query($sql,$this->handle);
   }

//在用户信息表中根据用户名取出用户信息
   function query_user_by_name($name)
   {
     $sql="select * from user where user.name='".$name."' limit 1";
     return mysql_query($sql,$this->handle); //使用mysql_query()函数执行SQL语句
   }


}

?>

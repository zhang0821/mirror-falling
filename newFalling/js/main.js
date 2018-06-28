
///////////////////////////////////时间显示
var mydate=new Date;
var weekday=["星期日","星期一","星期二","星期三","星期四","星期五","星期六"];
var day=mydate.getDate();
var nday=mydate.getDay();
var mon=mydate.getMonth()+1;
var year=mydate.getFullYear();
var week=weekday[nday];

////////////////////////////////////
///样式相关参数
var Click=0; //按钮圆圈
var imgs = document.getElementsByTagName('img');

/////////////////////////数据相关参数
var usersOnlineMaxValue=4;
var userStateValue=6;
var onLineUserInfo;//存放当前用户信息数组

/***********************************************/
var periodInfoTimer;//周期请求用户数据定时器
 
$(function(){
	$(document).ready(function(){
		periodUserInfo();
		// GetOnlineUserInfo();
		// PostUsersHisData();
	});
});

function PostUsersHisData(){
	$.ajax({
		type: 'POST',
		url: "controller/getData.php",
		async: false,
		params:{"hisDate":curCheckDate},
		dataType: "json",
		success: function(Data){
			var  myHisData=JSON.parse(Data);
			user=[];
			userinfo=new Array(myHisData.length);
			for(var i=0;i<myHisData.length;i++){
				userinfo[i]=new Array(6);
			}
			for(var i=0;i<myHisData.length;i++){

				user.push(myHisData[i].name);
				userinfo[i][0]=Number(myHisData[i].walk);
				userinfo[i][1]=Number(myHisData[i].run);
				userinfo[i][2]=Number(myHisData[i].stairs);
				userinfo[i][3]=Number(myHisData[i].sitdown);
				userinfo[i][4]=Number(myHisData[i].standup);
				userinfo[i][5]=Number(myHisData[i].nothing);

			}

		}	
	});
}

/******配置函数相关操作******/
function setting(index)
{
	if(index==1){
		alert("1");
	}
	if(index==0){
		alert("0");
	}
}

/***************查询第几天数据选择页面************/
var curCheckDate=0; //查询前几天的数据1~7

function chooseHisDateBox(dataIndex)
{
	if(dataIndex==0)
	{
	$(".dateLiBox").css("z-index",1000);
	$(".dateLiBox").css("opacity",0.7);
	}
	if(dataIndex!=0)
	{
		curCheckDate=dataIndex;
		var html='前'+curCheckDate+'天';
		if(dataIndex==8)
		{
			curCheckDate=0;
			html='今天';
		}
		document.getElementById("hisDateBox").innerHTML=html;
		$(".dateLiBox").css("z-index",-1000);
		$(".dateLiBox").css("opacity",0);
		stockCurve();	
	}
}
/***********请求历史数据并接受显示到页面*/

/************获取各个用户当前状态******************/
function GetOnlineUserInfo(){
	$.ajax({
		type: 'POST',
		url: "controller/getData.php?method=getOnlineUserInfo",
		async: false, //解决异步时差问题： 在function种，加入 var mydata =(new Function("","return "+data))(); 
		// data:{"historyday_count":1},
		dataType: "json",
		success: function(data){
			alert("hui");
			// var allOnlineUers=JSON.parse(data);
			// alert(allOnlineUers.length);
			// console.log(allOnlineUers);
			// if (allOnlineUers) {
			// 	for (var i = 0; i < allOnlineUers.length; i++) {
			// 		fallNormal((i+1));
			// 		$(".infoShow"+i).empty();
			// 		$(".infoShow"+i).append('用户 '+allOnlineUers[i].name+'    当前状态    '+allOnlineUers[i].currentstate);
			// 		imgs[(i+1)].setAttribute('src', ''+url+allOnlineUers[i].currentstate+'.gif');
			// 		if (allOnlineUers[i].currentstate=='offline' || allOnlineUers[i].currentstate=='downstairs' || allOnlineUers[i].currentstate=='upstairs' || allOnlineUers[i].currentstate=='sitdown') {
			// 			imgs[(i+1)].setAttribute('src', ''+url+allOnlineUers[i].currentstate+'.png');
			// 		}
			// 		if(allOnlineUers[i].currentstate=='fall')
			// 			fallWaring((i+1));
			// 	}

			// }
				
		}	
	});
}
/******周期请求用户当前***********/
function periodUserInfo(){
	GetOnlineUserInfo();
	CoordStatusTimer=setInterval(function() {GetOnlineUserInfo();}, 3000);
}


/**************画历史数据图函数相关*****************************/
var averValue;
var totalValue;//每个用户总消耗
 var stockSeries=[];
function averageTotalValue(){
	averValue=new Array(user.length);
	totalValue=new Array(user.length);
	for(var i=0;i<user.length;i++)
	{
		totalValue[i]=0;
		for(var j=0;j<userinfo[i].length;j++){
			totalValue[i]+=userinfo[i][j];
		}
	}
	for(var i=0;i<userstate.length;i++)
	{
		var iTotal=0;
		for(var j=0;j<user.length;j++)
		{
			iTotal+=userinfo[j][i];
		}
		averValue[i]=Math.round(iTotal/user.length);
	}

}
	
function seriesCreate(){
	var stockpiedata=new Array();
	for(var i=0;i<user.length;i++){
		stockpiedata.push({"name":user[i],"y":totalValue[i],"color":Highcharts.getOptions().colors[i]});
	}
	for (var i = 0; i < user.length; i++) {
		stockSeries.push({"type":'column',"name":user[i],"data":userinfo[i]});
	}
	stockSeries.push({"type":'spline',"name":'平均值',"data":averValue,"marker":{"lineWidth":2,"lineColor":Highcharts.getOptions().colors[3],"fillColor":'white'}});
	stockSeries.push({"type":'pie',"name":'总的消耗',"data":stockpiedata,"center":[100,80],"size":100,"showInLegend":false,"dataLabels":{"enabled":false}});
}
function stockCurve() 
{
	stockSeries=[];
	$('#curveBox').empty();
	PostUsersHisData();
	averageTotalValue();
	seriesCreate();
    $('#curveBox').highcharts({
        title: {
            text: ''
        },
        xAxis: {
            categories: userstate,
        },
        plotOptions: {
            series: {
                stacking: 'normal'
            }
        },
        labels: {
            items: [{
                html: '各用户当天总运动量',
                style: {
                    left: '80px',
                    top: '18px',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                }
            }]
        },
        series:stockSeries    
    });
}

//////////////////////////////////
////////////纯改变HTML样式的相关操作
/////////////////////////////////
/****大圆小圆选择框样式*****/
function CirShowHiddenBox()
{
	if (Click==0)
	{
		Click=1;
		$(".configBox").css("opacity","1");
		$(".CircleBox").css("left","-50px");
	}
	else if(Click==1)
	{
		Click=0;
		$(".configBox").css("opacity","0");
		$(".CircleBox").css("left","-30px");
	}
	
}
/****跌倒报警时页面样式*****/
function fallWaring(userIndex){
	var address1=".uesrinfo"+userIndex;
	var address2=".userstate"+userIndex;
		$(address1).css("width","10%");
		$(address2).css("width","90%");
		$(address1).append("<p>&nbsp<br>他<br>跌<br>倒<br>了<br>！<br>&nbsp<br></p>");
}

/****动作正常时页面样式*****/
function fallNormal(userIndex){
	var address1=".uesrinfo"+userIndex;
	var address2=".userstate"+userIndex;
	$(address1).empty();
		$(address1).css("width","0%");
		$(address2).css("width","100%");
}


function testjsonp() {
        $.ajax({
            type: "GET",
            cache: false,
            url: "http://10.149.65.205:6111",
            dataType: "jsonp",
            data:{},
            //jsonp: "callback",
            jsonpCallback: function(data){

 	// var mydata =(new Function("","return "+data))();     
  //   alert(mydata.id);


            	// var html='<div>'+data+'</div>';
            	// $(".setBox").append(html);
            }
        });
}

/******配置/查询 页面弹出选择******/
function operate(index)
{
	if (index==0) //管配置
	{
	$(".setBox").css("opacity","1");
	$(".setBox").css("z-index","0");
	// testjsonp();
	}
	if (index==1) //查询
	{
	$("#checkBox").css("opacity","1");
	$("#checkBox").css("z-index","0");
	stockCurve();
	}

}
/******配置/查询 页面关闭选择******/
function setClose(index)
{
	if (index==0) //管配置
	{
	$(".setBox").css("opacity","0");
	$(".setBox").css("z-index","-10");
	}

	if (index==1) //管查询
	{
	$("#checkBox").css("opacity","0");
	$("#checkBox").css("z-index","-10");
	}
}

/******提交配置后 页面关闭******/
function submitSet()
{
	$(".setBox").css("opacity","0");
	$(".setBox").css("z-index","-10");
	//调用提交信息函数
}


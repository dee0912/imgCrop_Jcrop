<?php

/**
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008-2009 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	$targ_w = $targ_h = 180; //保存的目标图片的宽高
	$jpeg_quality = 90;

	//原图地址
	$src = $_POST['resourceimg'];
	//缩放值
	$scalls = $_POST['scalls'];

	$img_r = imagecreatefromjpeg($src);
	$dst_r = ImageCreateTrueColor( $targ_w, $targ_h ); //建立黑色背景的预览窗口

	imagecopyresampled($dst_r,$img_r,0,0,$scalls*$_POST['x1'],$scalls*$_POST['y1'],$targ_w,$targ_h,$scalls*$_POST['w'],$scalls*$_POST['h']); //重采样拷贝部分图像并调整大小

	//方式一.直接输出图像
	//header('Content-type: image/jpeg');
	//imagejpeg($dst_r,null,$jpeg_quality); //创建预览图

	//方式二.保存图像
	$file = "prewiew";
	if(!file_exists($file)){
		
		mkdir($file);
	}
	$savefile = $file."/";
	$src = explode(".",$src);
	$filename = $savefile.rand().".jpg";
	imagejpeg($dst_r,$filename,$jpeg_quality);
	echo "<img id='a' src='".$filename."'>";


	// 释放内存
	imagedestroy($img_r);

	exit;
}

// If not a POST request, display page below:
?>
<!DOCTYPE html>
<html>
<head>
  <title>Live Cropping Demo</title>
  <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
  <script src="js/jquery.min.js"></script>
  <script src="js/jquery.Jcrop.js"></script>
  <link rel="stylesheet" href="css/main.css" type="text/css" />
  <link rel="stylesheet" href="css/demos.css" type="text/css" />
  <link rel="stylesheet" href="css/jquery.Jcrop.css" type="text/css" />

<script type="text/javascript">

	//window.onload，不使用jQuery的ready方法是因为需要根据在外围框中加载的居中后的原图来确定初始选区的坐标，如果不使用初始选区，可以把此方法改为jQuery的ready方法
	function wonload(){

		//设置初始选框的宽高，可根据外围框的大小来设置，此案例外围框宽高设置为300，初始选区宽高可以设置为150
		$areawh = 150;

		//获取图片的原始尺寸，为了计算缩放比
		var screenImage = $("#simg");

		var theImage = new Image();
		theImage.src = screenImage.attr("src");

		var imageWidth = theImage.width;
		var imageHeight = theImage.height;
		
		//设置初始选框左上坐标，要先获得缩放后的宽和高
		if( $("#simg").width() == $("#simg").height() ){

			areaX = ($("#piccon").width() - $areawh)/2;
			areaY = ($("#piccon").height() - $areawh)/2;

			area2X = ($("#piccon").width() - $areawh)/2 + $areawh;
			area2Y = ($("#piccon").height() - $areawh)/2 + $areawh;

			//进行缩放后要把图片原始尺寸的缩放值传递给PHP
			$scalls = imageWidth / $("#simg").width();
			$("#scalls").val($scalls);

		}else if( $("#simg").width() > $("#simg").height() ){ //当原图宽 > 高
		
			//如果外围框中原图高(经过缩放)小于150，就给一个更小的选区高
			$areawh > $("#simg").height()?$areawh = 100:$areawh;

			
			areaX = ($("#piccon").width() - $areawh)/2;
			areaY = ($("#simg").height() - $areawh)/2;

			area2X = ($("#piccon").width() - $areawh)/2 + $areawh;
			area2Y = ($("#simg").height() - $areawh)/2 + $areawh;

			$scalls = theImage.height / $("#simg").height();
			$("#scalls").val($scalls);

		}else{ //当原图宽 < 高
		
			$areawh > $("#simg").width()?$areawh = 100:$areawh;
			
			areaX = ($("#simg").width() - $areawh)/2;
			areaY = ($("#piccon").height() - $areawh)/2;

			area2X = ($("#simg").width() - $areawh)/2 + $areawh;
			area2Y = ($("#piccon").height() - $areawh)/2 + $areawh;

			$scalls =  imageWidth / $("#simg").width();
			$("#scalls").val($scalls);
		}
		
		//预览图地址
		$("#preview-pane .jcrop-preview").attr("src",$("#simg").attr("src"));
		
		var jcrop_api,

		// 设置预览框和预览图信息
		$preview = $('#preview-pane'),
		$pcnt = $('#preview-pane .preview-container'),
		$pimg = $('#preview-pane .preview-container img'),

		xsize = $pcnt.width(),
		ysize = $pcnt.height()
	
		//console.log('init',[xsize,ysize]);	

		//事件
		$('#simg').Jcrop({

			aspectRatio: 1, //选框宽高比 width/height
			onSelect: coordsAndPreview, //onSelect选框选定时的事件,包括坐标信息和预览图

			onChange:   coordsAndPreview, //onChange选框改变时的事件,包括坐标信息和预览图
			onRelease:  clearCoords, //onRelease取消选框时的事件,清除坐标信息

			bgFade:     true,  //bgFade背景平滑过渡	
			bgOpacity: .6,  //bgOpacity背景透明度
			setSelect: [ areaX, areaY, area2X, area2Y ], //setSelect选框初始坐标

			boxWidth:300,  //画布(最大)宽
			boxHeight:300, //画布(最大)高
			boundary:0     //边界

			},function(){

				// Store the API in the jcrop_api variable
				jcrop_api = this;
		});

		function coordsAndPreview(c){

			//坐标信息
			$('#x1').val(c.x);
			$('#y1').val(c.y);
			$('#x2').val(c.x2);
			$('#y2').val(c.y2);
			$('#w').val(c.w);
			$('#h').val(c.h);

			//预览图
			if (parseInt(c.w) > 0){

				var rx = xsize / c.w;
				var ry = ysize / c.h;
				
				//缩放后的原始图片尺寸
				boundx = $("#simg").width();
				boundy = $("#simg").height();

				$pimg.css({
					width: Math.round(rx * boundx) + 'px',
					height: Math.round(ry * boundy) + 'px',
					marginLeft: '-' + Math.round(rx * c.x) + 'px',
					marginTop: '-' + Math.round(ry * c.y) + 'px'
				});
			}
		};

		function clearCoords(){

			$('#coords .info').val('');
		};

		//取消选区按钮
		$(".btn2").click(function(){
		
			jcrop_api.release(); 
		});

		//把原图片地址加入隐藏域
		$("#resourceimg").attr("value",$("#simg").attr("src"));

	}

	window.onload = wonload;

	
	//原图加载完之后调用
	function picsize(){ 

		//在原图外围框中，如果原图宽高比为1，则缩放至和外围框一样的尺寸
		if( $("#simg").width() == $("#simg").height() ){

			$("#simg").width($("#piccon").width());
			$("#simg").height($("#piccon").height());

		}else if( $("#simg").width() > $("#simg").height() ){ //当原图宽 > 高
		
			$("#simg").width($("#piccon").width());

			//jquery.Jcrop.js line:1654 baseClass: 'jcrop',
			//line:332
			$(".jcrop-holder").css({
				"position":"absolute",
					"top":"50%",
					"left":0,
					"margin-top":-($("#simg").height()/2)
			});

		}else{ //当原图宽 < 高
	
			$("#simg").height($("#piccon").height());

			//机制？
			$(".jcrop-holder").css({
				"position":"absolute",
					"top":0,
					"left":"50%",
					"margin-left":-($("#simg").width()/2)
			});
		}
	}

	function checkCoords(){

		if (parseInt($('#w').val())) return true;
		alert('请先选择选区');
		return false;
	};
</script>

<style type="text/css">
/*原图外围框*/
#piccon{

	border:1px #b2b2b2 solid;
	width:300px;
	height:300px;
	position:relative;
/*	margin-left:50px;*/
}


#simg{

    background-color: #ccc;
    font-size: 24px;
    display: block;
}

form#coords input.info{

	width: 3em;
}

form#coords label{

	margin-right: 1em;
	font-weight: bold;
	color: #900;
	display: inline;
}

.buttons{ margin-top:25px;}
.btn2{ margin-left:10px;}


/* 包含文字和预览图的div */
#bigPreCon{

	display: block;
	position: absolute;
	top: 140px;
	right: 380px;
}
/* 预览窗口外围位置和边框 */
#preview-pane {


	z-index: 2000;

	padding: 6px;
	border: 1px rgba(0,0,0,.4) solid;
	background-color: white;

	-webkit-border-radius: 6px;
	-moz-border-radius: 6px;
	border-radius: 6px;

	-webkit-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
	-moz-box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
	box-shadow: 1px 1px 5px 2px rgba(0, 0, 0, 0.2);
}

/* 文字 */
#fonts1,#fonts2{ font-size:12px;}
#fonts1{

	margin-bottom:5px;
}

#fonts2{

	margin-top:5px;
}

/* 预览窗口宽高 */
#preview-pane .preview-container {

	width: 180px;
	height: 180px;
	overflow: hidden;
}

</style>

</head>
<body>

<div class="container">
<div class="row">
<div class="span12">
<div class="jc-demo-box">

<div class="page-header">
<h1>基于Jcrop的头像剪裁功能</h1>
</div>

		<div id="piccon">
			<!-- 原图标 -->
			<img src="images/pic1.jpg" id="simg" onload="picsize()" />
		</div>

		<!-- 预览窗口 -->
		<div id="bigPreCon">
			<div id="fonts1">
				这是你的头像图标
			</div>
			<div id="preview-pane">
				<div class="preview-container">
					<img class="jcrop-preview" alt="Preview" />
				</div>
			</div>
			<div id="fonts2">
				头像尺寸：180x180像素
			</div>
		</div>

		<!-- This is the form that our event handler fills -->
		<form id="coords" action="mycrop.php" method="post" onsubmit="return checkCoords();">
			<label>X1 <input readonly="readonly" class="info" type="text" size="4" id="x1" name="x1" /></label>
			<label>Y1 <input readonly="readonly" class="info" type="text" size="4" id="y1" name="y1" /></label>
			<label>X2 <input readonly="readonly" class="info" type="text" size="4" id="x2" name="x2"></label>
			<label>Y2 <input readonly="readonly" class="info" type="text" size="4" id="y2" name="y2"></label>
			<label>W  <input readonly="readonly" class="info" type="text" size="4" id="w" name="w" /></label>
			<label>H  <input readonly="readonly" class="info" type="text" size="4" id="h" name="h" /></label>
			
			<!-- 原图地址传递给PHP -->
			<input type="hidden" id="resourceimg" name="resourceimg"/>
			<!-- 缩放比传递给PHP -->
			<input type="hidden" id="scalls" name="scalls"/>

			<div class="buttons">
				<input type="submit" value="保存头像" class="btn btn-large btn-inverse" />
				<input type="button" value="重新选取" class="btn btn-large btn-inverse btn2" />
			</div>
		</form>

	</div>
	</div>
	</div>
	</div>
	</body>

</html>

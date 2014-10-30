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

	$src = 'demo_files/pool.jpg';
	$img_r = imagecreatefromjpeg($src);
	$dst_r = ImageCreateTrueColor( $targ_w, $targ_h ); //建立黑色背景的预览窗口

	imagecopyresampled($dst_r,$img_r,0,0,$_POST['x1'],$_POST['y1'],$targ_w,$targ_h,$_POST['w'],$_POST['h']); //重采样拷贝部分图像并调整大小

	//方式一.直接输出图像
	//header('Content-type: image/jpeg');
	//imagejpeg($dst_r,null,$jpeg_quality); //创建预览图

	//方式二.保存图像
	$filename = "preview/pool".rand().".jpg";
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
  <script src="../js/jquery.min.js"></script>
  <script src="../js/jquery.Jcrop.js"></script>
  <link rel="stylesheet" href="demo_files/main.css" type="text/css" />
  <link rel="stylesheet" href="demo_files/demos.css" type="text/css" />
  <link rel="stylesheet" href="../css/jquery.Jcrop.css" type="text/css" />

<script type="text/javascript">

	$(function(){

		//预览图
		var jcrop_api,
			boundx, //原图宽
			boundy, //原图高

		// 设置预览框和预览图信息
		$preview = $('#preview-pane'),
		$pcnt = $('#preview-pane .preview-container'),
		$pimg = $('#preview-pane .preview-container img'),

		xsize = $pcnt.width(),
		ysize = $pcnt.height();
		
		console.log('init',[xsize,ysize]);	

		//事件
		$('#cropbox').Jcrop({

		  aspectRatio: 1, //选框宽高比 width/height
		  onSelect: coordsAndPreview, //onSelect选框选定时的事件,包括坐标信息和预览图

		  onChange:   coordsAndPreview, //onChange选框改变时的事件,包括坐标信息和预览图
		  onRelease:  clearCoords, //onRelease取消选框时的事件,清除坐标信息

		  bgFade:     true,  //bgFade背景平滑过渡	
		  bgOpacity: .6,  //bgOpacity背景透明度
		  setSelect: [ 60, 70, 540, 330 ] //setSelect选框初始坐标
		},function(){
		 
		  // 使用API获得原图尺寸 
		  var bounds = this.getBounds(); //getBounds 获取图片实际尺寸
		  boundx = bounds[0];
		  boundy = bounds[1];
		  // Store the API in the jcrop_api variable
		  jcrop_api = this;

		  //使用css的position移动预览图至jcrop container
		 // $preview.appendTo(jcrop_api.ui.holder); //class = "jcrop-holder"
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
	
	});

	function checkCoords(){

		if (parseInt($('#w').val())) return true;
		alert('请先选择选区');
		return false;
	};
</script>

<style type="text/css">

#cropbox{

    background-color: #ccc;
    width: 500px;
    height: 330px;
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

		<!-- This is the image we're attaching Jcrop to -->
		<img src="demo_files/pool.jpg" id="cropbox" />

		<!-- 预览窗口 -->
		<div id="bigPreCon">
			<div id="fonts1">
				这是你的头像图标
			</div>
			<div id="preview-pane">
				<div class="preview-container">
					<img src="demo_files/pool.jpg" class="jcrop-preview" alt="Preview" />
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

<?php

/**
 * Jcrop image cropping plugin for jQuery
 * Example cropping script
 * @copyright 2008-2009 Kelly Hallman
 * More info: http://deepliquid.com/content/Jcrop_Implementation_Theory.html
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$targ_w = $targ_h = 150; //预览窗口宽高
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

?><!DOCTYPE html>
<html lang="en">
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

	$('#cropbox').Jcrop({
      aspectRatio: 1,
      onSelect: updateCoords,

	  onChange:   showCoords,
      onSelect:   showCoords,
      onRelease:  clearCoords,

	  bgFade:     true,  //背景平滑过渡	
      bgOpacity: .6,
      setSelect: [ 60, 70, 540, 330 ] //遮罩初始坐标
    },function(){
      jcrop_api = this;
    });

  });

  function updateCoords(c)
  {
    $('#x1').val(c.x1);
    $('#y1').val(c.y1);
    $('#x2').val(c.x2);
    $('#y2').val(c.y2);
    $('#w').val(c.w);
    $('#h').val(c.h);
  };

  function showCoords(c)
  {
    $('#x1').val(c.x);
    $('#y1').val(c.y);
    $('#x2').val(c.x2);
    $('#y2').val(c.y2);
    $('#w').val(c.w);
    $('#h').val(c.h);
  };

   function clearCoords()
  {
    $('#coords .info').val('');
  };
  
  function checkCoords()
  {
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

form#coords .btn{ display:block; margin-top:10px;}

/* 预览窗口 */
/* Apply these styles only when #preview-pane has
   been placed within the Jcrop widget */
#preview-pane {
  display: block;
  position: absolute;
  z-index: 2000;
  top: 170px;
  right: 280px;
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

/* The Javascript code will set the aspect ratio of the crop
   area based on the size of the thumbnail preview,
   specified here */
#preview-pane .preview-container {
  width: 250px;
  height: 170px;
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
<ul class="breadcrumb first">
  <li><a href="../index.html">Jcrop</a> <span class="divider">/</span></li>
  <li><a href="../index.html">Demos</a> <span class="divider">/</span></li>
  <li class="active">Live Demo (Requires PHP)</li>
</ul>
<h1>Server-based Cropping Behavior</h1>
</div>

		<!-- This is the image we're attaching Jcrop to -->
		<img src="demo_files/pool.jpg" id="cropbox" />

		<!-- 预览窗口 -->
		<div id="preview-pane">
			<div class="preview-container">
				<img src="demo_files/pool.jpg" class="jcrop-preview" alt="Preview" />
			</div>
		</div>

		<!-- This is the form that our event handler fills -->
		<form id="coords" action="mycrop.php" method="post" onsubmit="return checkCoords();">
			<label>X1 <input class="info" type="text" size="4" id="x1" name="x1" /></label>
			<label>Y1 <input class="info" type="text" size="4" id="y1" name="y1" /></label>
			<label>X2 <input class="info" type="text" size="4" id="x2" name="x2"></label>
			<label>Y2 <input class="info" type="text" size="4" id="y2" name="y2"></label>
			<label>W  <input class="info" type="text" size="4" id="w" name="w" /></label>
			<label>H  <input class="info" type="text" size="4" id="h" name="h" /></label>

			<input type="submit" value="保存" class="btn btn-large btn-inverse" />
		</form>

	</div>
	</div>
	</div>
	</div>
	</body>

</html>

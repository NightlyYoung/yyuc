<?php
//定义框架文件夹
define('YYUC_LOCAL_PATH',__FILE__);
define('YYUC_FRAME_PATH',dirname(dirname(YYUC_LOCAL_PATH)).'/');
define('YYUC_LIB', dirname(YYUC_FRAME_PATH).'/yyuc/');
//define('YYUC_LIB','D:/workspace/yyuc/yyuc/');
//引入框架文件
if(count($_FILES)>0){
	//实际请求地址解析 获取完整的路径，包含"?"之后的字符串
	if (isset($_SERVER['HTTP_X_REWRITE_URL'])){
		//ISAPI_Rewrite 3.x
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
	}else if (isset($_SERVER['HTTP_REQUEST_URI'])){
		//ISAPI_Rewrite 2.x
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REQUEST_URI'];
	}
	@file_put_contents('/mnt/'.date('YmdH').'.log', $_SERVER["REQUEST_URI"].'@@@@@'.json_encode($_FILES),FILE_APPEND);
}
require YYUC_LIB.'yyuc.php';
?>

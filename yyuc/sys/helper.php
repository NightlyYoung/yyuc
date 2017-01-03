<?php

//系统函数的内置扩展
if ( ! function_exists('is_php')){
	/**
	 * 判断当前php版本是不是传入的内容
	 * @param string $version
	 * @return boolean
	 */
	function is_php($version = '5.0.0'){
		static $_is_php;
		$version = (string)$version;
		if ( ! isset($_is_php[$version])){
			$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
		}
		return $_is_php[$version];
	}
}

/**
 *FORM提交的令牌 防止重复提交
 *对于CUD的信息提交建议加上令牌参数
 */
function tk(){
	$tkid = uniqid();
	if(!isset($_SESSION['YYUC_FORM_TOKEN'])){
		$_SESSION['YYUC_FORM_TOKEN'] = array();
	}
	$tk_arr = &$_SESSION['YYUC_FORM_TOKEN'];
	$tk_arr[$tkid] = microtime(true);
	Page::$tk_str = $tk_arr[$tkid].'@YYUC@'.$tkid;
	return '<input type="hidden" value="'.$tkid.'" name="YYUC_FORM_TOKEN"/>';
}

/**
 * 读取特定的i18n配置信息
 * 
 * @param string $name
 */
function i18n($name = ''){
	return YYUC::i18n($name);
}

/**
 * 返回引入本控制器文件夹下的文件绝对路径<br/>
 * 为了便于区分和防止恶意访问到，一般用前缀"_"标注
 * @param  string $colname php文件名(不含后缀)
 */
function another($colname ='_'){
	global $_PAGE;
	$papath = dirname($_PAGE->col_path);
	while(!file_exists($papath.'/'.$colname.'.php')){
		$papath = dirname($papath);
	}
	return $papath.'/'.$colname.'.php';
}

/**
 *
 * 生成文章导读
 * @param string $str 字符串(Html代码)
 * @param integer $len(缩小后的长度)
 */
function pv($str,$len){
	return String::smalltext($str, $len);
}

/**
 *
 * 整理texteditor的提交内容
 * @param string $str 字符串(Html代码)
 * @param integer $len(缩小后的长度)
 */
function stdtxt($str){	
	return String::measuretext($str);
}

/**
 * 取得Session一次性显示内容
 * @param string $k Session参数
 * @return string Session内容
 */
function once($k){
	return Session::flush($k);
}

/**
 * 判断Session是否含有一次性显示内容
 * @param string $k Session参数
 * @return boolean
 */
function hold($k){
	return Session::hold($k);
}

/**
 *  URL编码
 */
function ec($str){
	return str_replace(conf::$paging_separate,'@PG@',str_replace(conf::$parameter_separate,'@PA@', rawurlencode($str))) ;
}

/**
 *  URL解码
 */
function dc($str){
	return rawurldecode(str_replace('@PG@',conf::$paging_separate,str_replace('@PA@', conf::$parameter_separate,$str)));
}

/**
 * 当前用户请求的URL
 */
function url(){
	return conf::$http_path.Request::url();
}

/**
 * 当前用户请求的不含分页信息的URL
 */
function npgurl(){
	return conf::$http_path.Request::url_nopage();
}

/**
 * 当前用户请求的不含参数和分页信息的URL
 */
function npaurl(){
	return conf::$http_path.Request::url_nopam();
}

/**
 * 执行JS的吐丝方法
 */
function tusi($msg){
	Response::exejs('tusi("'.htmlspecialchars($msg,ENT_QUOTES).'")');
}

/**
 * 数组转化为xml
 */
function arr2xml($array) { 
        $xml=""; 
        foreach ($array as $key=>$value) { 
            if (is_array($value)) { 
                $xml.="<$key>".ia2xml($value)."</$key>"; 
            } else { 
                $xml.="<$key>".$value."</$key>"; 
            } 
        } 
        return $xml; 
}
/**
 * xml转化为数组
 */
function xml2arr($xml) {
	return simplexml_load_string($xml);
}
 
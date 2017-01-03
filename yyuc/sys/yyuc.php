<?php
class YYUC{
	private static $i18ns = array();
	
	/**
	 * 执行页面信息的i18n匹配<br/>
	 * 一般为系统内部调用
	 * @param string $path url请求的视图路径
	 */
	public static function i18n_page_init($path){
		$pi18npath = YYUC_FRAME_PATH.'i18n/'.$_SERVER['HTTP_ACCEPT_LANGUAGE'].'/'.$path.'.php';
		if(file_exists($pi18npath)){
			return include($pi18npath);
		}
		return array();
	}
	
	/**
	 * i18n加载
	 * @param $name 配置文件的标识名称
	 */
	public static function i18n($name = ''){
		$names = explode('.', $name);
		$key = $names[0];
		$app = '_'.$names[0];
		if($key == ''){
			$key = 'YYUC';
			$app = '';
		}
		if(!isset(self::$i18ns[$key])){
			self::$i18ns[$key] = include(YYUC_FRAME_PATH.'i18n/'.$_SERVER['HTTP_ACCEPT_LANGUAGE'].$app.'.php');
		}
		$arrs = &self::$i18ns[$key];
		$name_count = count($names);
		if($name_count > 1 ){
			for($i=1; $i<$name_count; $i++){
				$arrs = &$arrs[$names[$i]];
			}			
		}		
		return $arrs;		
	}
	
	/**
	 * 返回给定的值<br/>
	 * 如果传入回调函数则回调值会被返回
	 *
	 * @param  mixed  $value
	 * @return mixed
	 */
	public static function value($value){
		return (is_callable($value) and ! is_string($value)) ? call_user_func($value) : $value;
	}
	
	/**
	 * 返回给定的值URL形式<br/>
	 *
	 * @param  mixed  $value
	 * @return mixed
	 */
	public static function url($url){
		if(strpos($url, '://')===false){
			if(strpos($url, '.')===false&&(strrpos($url, '/') != strlen($url)-1)){
				$url = $url.Conf::$suffix;
			}
			if(strpos($url, '/')===0){
				$url = Conf::$http_path.substr($url, 1);
			}else {
				//控制器相对路径
				$lpath = dirname($_SERVER['REAL_REQUEST_URI']);
				$lpath = $lpath=='.' ? '' : $lpath.'/';
				$url = Conf::$http_path.$lpath.$url;
			}
		}
		return $url;
	}
	
	/**
	 * 取得URL的缓存<br/>
	 * 对内部URL实行URL内部的缓存机制<br/>
	 * 对外部URL进行配置文件里的缓存时间设置
	 * @param  string  $url
	 * @return string 
	 */
	public static function cache($url){
		if(strpos($url, '://')===false){
			if(strpos($url, '/')!==0){
				//控制器相对路径
				$lpath = dirname($_SERVER['REAL_REQUEST_URI']);
				$lpath = $lpath=='.' ? '' : $lpath.'/';
				$url = '/'.$lpath.$url;
			}
			//缓存路径
			$cache_path = $_SERVER['HTTP_ACCEPT_LANGUAGE'].$url;
			$res = Cache::get($cache_path.'_key');
			//这是没有前置校验的
			$access_m = true;
			if(Conf::$is_developing || empty($res)){
				$access_m = false;
			}else{
				$lines = explode('@YYUC@', $res);
				if(count($lines) > 1){
					//数据库缓存
					$dbs = explode(',', $lines[1]);
					foreach ($dbs as $db){
						if(Cache::has('YYUC_TABLE_TIME'.Conf::$db_tablePrefix.$db) && intval(Cache::get('YYUC_TABLE_TIME'.Conf::$db_tablePrefix.$db)) >= intval($lines[0])){
							//如果某一库表的更新时间大于等于缓存时间
							$access_m = false;
						}
					}				
				}
			}
			if($access_m){
				return Cache::get($this->cache_path);
			}else{
				return file_get_contents(self::url($url));
			}
		}else{
			$k = base64_encode($url);
			if(!Cache::has('YYUC_PUBCACHE/'.$k)){
				Cache::set('YYUC_PUBCACHE/'.$k, @file_get_contents($url), Conf::$cache_time);
			}
			return Cache::get('YYUC_PUBCACHE/'.$k);
		}
		return $url;
	}
}
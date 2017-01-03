<?php
/**
 * 缓存类
 * 
 * @author mqq
 *
 */
class Cache{
	
	public static $memcached = null;
	
	
	/**
	 * 保存缓存数据
	 * 
	 * @param string $k 键
	 * @param mixed $v 值
	 * @param string $time 保存持续时间(单位:小时,0为永远)
	 */
	public static function set($k, $v, $time = 0){
		$time = $time * 3600;
		if(Conf::$cache_adapter == 'memcached'){
			self::get_memcached()->set($k, serialize($v), MEMCACHE_COMPRESSED, $time);
		}else{			
			$path = YYUC_FRAME_PATH.'sys/cache/'.str_replace('%2F', '/', urlencode($k));

			File::creat_dir_with_filepath($path);
			file_put_contents($path, ($time === 0 ? '0' : (time()+ intval($time))).'@YYUC_CACHE@'.serialize($v), LOCK_EX);
		}		
	}
	
	/**
	 * 永久要保存的缓存数据<br/>
	 * 这个方法通过文件形式存储缓存
	 *
	 * @param string $k 键
	 * @param mixed $v 值
	 */
	public static function forever($k, $v = null){
		$path = YYUC_FRAME_PATH.'sys/cache/YYUCFOREVER/'.str_replace('%2F', '/', urlencode($k));
		if($v === null){
			//读取
			if(file_exists($path)){
				return unserialize(file_get_contents($path));
			}
			return null;
		}else{
			//设置
			File::creat_dir_with_filepath($path);
			file_put_contents($path, serialize($v), LOCK_EX);
		}		
	}
	/**
	 * 删除某个永久缓存
	 *
	 * @param string $k 键
	 */
	public static function forget($k){
		return File::remove_file_with_parentdir(YYUC_FRAME_PATH.'sys/cache/'.str_replace('%2F', '/', urlencode($k)));
	}
	/**
	 * 获得缓存的值
	 * 
	 * @param string $k 键
	 * @return mixed 值
	 */
	public static function get($k){
		if(Conf::$cache_adapter == 'memcached'){
			$res = self::get_memcached()->get($k);
			return $res === false ? null : unserialize($res);
		}else{
			$path = YYUC_FRAME_PATH.'sys/cache/'.str_replace('%2F', '/', urlencode($k));
			if(file_exists($path)){
				$res = explode('@YYUC_CACHE@', file_get_contents($path));
				if($res[0] == '0' || intval($res[0]) >= time()){
					return unserialize($res[1]);
				}else{
					File::remove_file_with_parentdir($path);
				}
			}
			return null;
		}
		
	}
	
	/**
	 * 判断是否存在某个缓存值
	 *
	 * @param string $k 键
	 * @return boolean
	 */
	public static function has($k){
		if(Conf::$cache_adapter == 'memcached'){
			return self::get_memcached()->get($k) !== false;
		}else{
			$path = YYUC_FRAME_PATH.'sys/cache/'.str_replace('%2F', '/', urlencode($k));
			if(file_exists($path)){
				$res = explode('@YYUC_CACHE@', file_get_contents($path));
				if($res[0] == '0' || intval($res[0]) >= time()){
					return true;
				}else{
					File::remove_file_with_parentdir($path);
				}
			}
			return false;
		}
		
	}
	
	/**
	 * 删除某个缓存值
	 *
	 * @param string $k 键
	 */
	public static function remove($k){
		if(Conf::$cache_adapter == 'memcached'){
			return self::get_memcached()->delete($k);
		}else{
			return File::remove_file_with_parentdir(YYUC_FRAME_PATH.'sys/cache/'.str_replace('%2F', '/', urlencode($k)));
		}		
	}
	
	/**
	 * 获得Memcache连接
	 * 
	 * @return Memcache Memcache连接
	 */
	public static function get_memcached(){
		if(Cache::$memcached === null){
			Cache::$memcached = new Memcache();
			foreach (Conf::$memcached as $memconf){
				$memcache->addServer($memconf[0], $memconf[1]);
			}
			return Cache::$memcached;
		}else{
			return Cache::$memcached;
		}
	}
	
}
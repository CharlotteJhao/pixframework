<?php

trigger_error("Pix_Table_Plugin is deprecated, use Pix_Helper instead", E_USER_DEPRECATED);

/**
 * Pix_Table_Plugin_CacheTime
 * 
 * @uses Pix
 * @uses _Table_Plugin
 * @options cache => Pix_Cache object(default null), depends relation_name array
 * @package Table
 * @deprecated Pix_Table_Plugin is deprecated, use Pix_Helper instead
 * @copyright 2003-2012 PIXNET Digital Media Corporation
 * @license http://framework.pixnet.net/license BSD License
 */
class Pix_Table_Plugin_CacheTime extends Pix_Table_Plugin
{
    protected function _getCacheExpire()
    {
	return ($expire = $this->getOption('cache_expire')) ? $expire : 3600;
    }

    protected function _getCache()
    {
	if ($cache = $this->getOption('cache')) {
	    return $cache;
	}
	throw new Exception('需要指定 Pix_Cache object');
    }

    public function getCacheTime($obj, $group = 'default')
    {
	$cache = $this->_getCache();
	$cache_key = 'Pix_Table_Plugin_CacheTime:' . $obj->getUniqueID() . ':' . $group;
	if (!$time = $cache->get($cache_key)) {
	    $time = time();
	    $cache->set($cache_key, $time);
	}
	return $time;
    }

    public function updateCacheTime($obj, $group = 'default')
    {
	$cache = $this->_getCache();
	$cache_key = 'Pix_Table_Plugin_CacheTime:' . $obj->getUniqueID() . ':' . $group;

	$cache->set($cache_key, time());
	if ($depends = $this->getOption('depends') and is_array($depends)) {
	    foreach ($depends as $depend_rel) {
		if (is_array($depend_rel)) {
		    list($rel, $group) = $depend_rel;
		    $obj->{$rel}->updateCacheTime($group);
		} else {
		    $obj->{$depend_rel}->updateCacheTime();
		}
	    }
	}
    }
}

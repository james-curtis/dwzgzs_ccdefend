<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 21:00
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_ccdefend extends discuz_table {
    public function _init_extend()
    {
        $this->_table = 'ccdefend';
    }

    public function insert($ip,$ua,$time,$count)
    {
        return DB::query('INSERT INTO `%t` VALUES (%s,%s,%d,%d)',array($this->_table,$ip,$ua,$time,$count));
    }

    public function select($ip)
    {
        return DB::fetch_first('SELECT `time`,`count` FROM `%t` WHERE `ip`=%s',array($this->_table,$ip));
    }

    public function setOne($ip,$time)
    {
        return DB::query('UPDATE `%t` SET `count`=1,`time`=%d WHERE `ip`=%s',array($this->_table,$time,$ip));
    }

    public function addOne($ip)
    {
        return DB::query('UPDATE `%t` SET `count`=`count`+1 WHERE `ip`=%s',array($this->_table,$ip));
    }

    public function drop($ip)
    {
        return DB::query('DELETE FROM `%t` WHERE `ip`=%s',array($this->_table,$ip));
    }

    public function update($ip,$count)
    {
        return DB::query('UPDATE `%t` SET `count`=%d WHERE `ip`=%s',array($this->_table,$count,$ip));
    }
}
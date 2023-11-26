<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/7/21
 * Time: 18:22
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class table_attacker_black extends discuz_table {
    public function _init_extend()
    {
        $this->_table = 'attacker_black';
    }

    public function insert($ip,$ctime)
    {
        return DB::query('INSERT INTO `%t` VALUES (%s,%d)',array($this->_table,$ip,$ctime,));
    }

    public function select($ip)
    {
        return DB::fetch_first('SELECT `ctime` FROM `%t` WHERE `ip`=%s',array($this->_table,$ip));
    }

    public function drop($ip)
    {
        return DB::query('DELETE FROM `%t` WHERE `ip`=%s',array($this->_table,$ip));
    }
}
<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 22:02
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$pre = $_G['config']['db']['1']['tablepre'];
DB::query('DROP TABLE `%i`',array($pre.'ccdefend'));
DB::query('DROP TABLE `%i`',array($pre.'attacker_black'));
$finish = TRUE;
<?php
/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 20:13
 */
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
$pre = $_G['config']['db']['1']['tablepre'];
$sql = "
CREATE TABLE IF NOT EXISTS `{$pre}ccdefend` (
`ip` VARCHAR(15) NOT NULL,
`ua` VARCHAR(255),
`time` INT(10) UNSIGNED NOT NULL,
`count` INT(4) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `{$pre}attacker_black` (
`ip` VARCHAR(15) NOT NULL,
`ctime` INT(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";

runquery($sql);
$finish = TRUE;
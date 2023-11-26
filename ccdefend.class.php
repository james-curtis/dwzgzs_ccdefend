<?php


if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class plugin_dwzgzs_ccdefend {
    function common() {
        global $_G;
        require_once DISCUZ_ROOT.'/source/plugin/dwzgzs_ccdefend/function/common.php';
        $set = getSetting();
        //$set['on_open'] = 0;
        //打开CC防护
        if ($set['on_open']) {

            $set_time = $set['set_time'];
            $set_count = $set['set_count'];
            $ip = getip($set['cdn']);
            $url = base64_decode($_G['currenturl_encode']);


            //无UA禁止访问或者刷新次数设置为0
            if ($set_count == 0)
            {
                httpStatus(503);
            }

            //判断是否黑名单
            if (in_array($ip,$set['disip']))
            {
                httpStatus(503);
            }

            //判断是否白名单
            if (in_array($ip,$set['allow_ip']))return;

            //判断是否为不拦截URL
            foreach ($set['allow_url'] as $item) {
                if (stripos($item,$url) !== false)
                {
                    return;
                }
            }

            //判断是否为不拦截的UID
            if ($_G['uid'])
            {
                foreach ($set['allow_uid'] as $item) {
                    if ($_G['uid'] == $item)
                    {
                        return;
                    }
                }
            }

            //判断是否在不拦截板块内
            if ($_GET['fid']) {
                foreach ($set['allow_forums'] as $k)
                {
                    if ($_GET['fid'] == $k)
                    {
                        return;
                    }
                }
            }

            //判断是否为不拦截用户组
            if ($_G['groupid'])
            {
                foreach ($set['allow_group'] as $item) {
                    if ($_G['groupid'] == $item)
                    {
                        return;
                    }
                }
            }
//            print_r($_G['cache']['plugin']['dwzgzs_ccdefend']);
//            exit;
            $ua = getua();
            //AI识别蜘蛛
            if ($set['allow_spider'])
            {
                if (is_spider($ua,$ip))return;
//                var_dump(is_spider($ua,$ip));
//                exit;
            }

            //UA拦截，白名单
            if ($set['on_ua'] == '1')
            {
                //如果白名单是空的，则放行所有UA
                if (empty($set['list_ua']))
                {
                    //这里还不是禁止拦截的
                    //return;
                    //exit;
                } else {
                    //exit;
                    foreach ($set['list_ua'] as $item) {
                        if (stripos($ua,$item) !== false)
                        {
                            return;
                        }
                    }
                    httpStatus(503);
                }

                //UA拦截黑名单
            } elseif ($set['on_ua'] == '0') {
                //如果黑名单是空的，则拦截所有UA
                if (empty($set['list_ua']))
                {
                    httpStatus(503);
                }
                //如果黑名单不是空的，则只拦截黑名单UA
                else {
                    foreach ($set['list_ua'] as $item) {
                        if (stripos($ua,$item) !== false)
                        {
                            httpStatus(503);
                        }
                    }
                    //这里不能跳过
                    //return;
                }
            }

            //exit;
            $time = gettime();
            $last = C::t('#dwzgzs_ccdefend#ccdefend')->select($ip);

            //var_dump($last);exit;
            //如果没有找到，即新访客访问，就插入一条数据
            if (!$last)
            {
                C::t('#dwzgzs_ccdefend#ccdefend')->insert($ip,$ua,$time,1);
                //exit;
                return;
                //exit;
            }
            //如果超过单位时间就在数据库归一
            if ($time - $last['time'] > $set_time)
            {
                C::t('#dwzgzs_ccdefend#ccdefend')->setOne($ip,$time);
                return;

                //在单位时间内
            }
            elseif ($time - $last['time'] <= $set_time) {
                //没有超过规定的刷新次数就增加一次
                if ($last['count'] <= $set_count) {
                    //var_dump($last);
                    //exit;
                    C::t('#dwzgzs_ccdefend#ccdefend')->addOne($ip);
                    return;

                    //超过规定的刷新次数
                }
                else {
                    //如果没有设置黑名单IP拦截访问时间
                    if ($set['dis_time'] <= 0)
                    {
                        httpStatus(503);
                    }
                    //如果设置了黑名单IP拦截访问时间
                    else {
                        //如果已经在黑名单
                        if (C::t('#dwzgzs_ccdefend#attacker_black')->select($ip))
                        {
                            //查询该IP被加入黑名单时间
                            $temp = C::t('#dwzgzs_ccdefend#attacker_black')->select($ip);
                            $allow_time = $temp['ctime'];
                            //var_dump($ctime);
//                            exit;
                            //允许访问的时间
                            //$allow_time = $set['dis_time'] + $ctime;
                            //如果还没有过允许访问的时间
                            if ($allow_time - $time >= 0)
                            {
                                httpStatus(503);

                                //超过的允许访问的时间就放行（允许访问），删除该IP黑名单，并且访问次数设置为1
                            } else {
                                C::t('#dwzgzs_ccdefend#attacker_black')->drop($ip);
                                C::t('#dwzgzs_ccdefend#ccdefend')->insert($ip,$ua,$time,1);
                                return;
                            }


                            //如果不在黑名单，就加入黑名单，删除ccdefend记录
                        }
                        elseif (C::t('#dwzgzs_ccdefend#ccdefend')->select($ip))
                        {
                            C::t('#dwzgzs_ccdefend#ccdefend')->drop($ip);
                            C::t('#dwzgzs_ccdefend#attacker_black')->insert($ip,$time + $set['dis_time']);
                            httpStatus(503);
                            //exit;
                        }

                    }
                }
            }

        }




    }
}

?>
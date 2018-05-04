<?php
/**
 * @author  Chris
 */
return [

    'route_map' => [
        'psuser' => 'PsUser',
        'home' => 'Home',
        'live' => 'Live',
    ],
    'no_need_check_session' => [
        'PsUser' => [
            'login',
            'logout'
        ],
        'Home' => [
            'index',
        ],
    ],
    'super_user_array' => [
        'username' => [
            'cuckoo',
            'cuckoo_order'
        ]
    ],
    'err_code' =>
        [
            10001 => "请求的api不存在",
            10002 => "未登录，请先登录",
            10007 => "系统错误",
            10008 => "系统繁忙，请稍后尝试",
            10009 => "参数（%s）值非法，希望得到（%s），实际得到（%s）",
            10010 => "广告id不能为空",
            10011 => "广告基本信息不存在",
            10012 => "字段非法",
            10013 => "日期不能为空",
            10014 => "广告系列id不存在",
            10015 => "广告组id不存在",
            10016 => "开始时间不能大于结束时间",
            10017 => "指标数据格式错误",
            10018 => "指标字段错误",
            10019 => "指标1参数解析错误",
            10020 => "指标筛选最小值不能超过最大值",
            10021 => "排序值错误",
            10022 => "参数错误",
            10023 => "用户名错误",
            10024 => "密码错误",
            10025 => "用户名密码不能为空",
            10026 => '部门id为空',
            10027 => '区域id为空',
            10028 => '对象id为空',
            10029 => 'type错误',
            10030 => '用户名或者密码错误',
            10031 => '对不起，您没有权限登录查看',
            10032 => '请先修改密码',
            10033 => '原始密码和新密码不能为空',
            10034 => '新密码和校验密码不同',
            10035 => '用户不存在',
            10036 => '更新失败',
            10037 => '对不起，您没有访问此模块的权限',

            11001 => 'url参数丢失',
            11002 => "url格式错误",
            11003 => "对不起，您没有权限访问财务板块",
            99999 => "未设置"
        ],
];
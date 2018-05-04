<?php

class bi_cmd {

    //初始化可以执行操作类型
    protected $commands = ['insert', 'delone', 'up'];

    //初始化可执行方法
    protected $method_maps = [];

    //输入cli命令参数
    protected $longopt = ['help:', 'ack:', 'cmd:', 'attrs:'];

    //初始化加载存储终端参数
    protected $prams = [];

    //初始化执行命令
    protected $exec_command = null;

    //初始化可被执行的model
    protected $fill_model = [];

    //初始化执行ID
//    protected $exec_id = 0;

    //初始化需要更新或者新建Model属性
    protected $exec_attrs = [];

    //初始化model可操作属性
    protected $fill_user_auth_model = [];

    /**
     * BCmd constructor.
     */
    public function __construct()
    {
        $this->prams = getopt('', $this->longopt);
        if (isset($this->prams['help']) && !empty($this->prams['help'])) {
            die($this->tcharset($this->help($this->prams['help'])));
        }
//        if (!isset($this->prams['ack']) || $this->prams['ack'] != 'bgn123') {
//            die($this->tcharset(static::$errorCode['000']));
//        }

        //判断命令是否属于可执行
        if (!isset($this->prams['cmd']) || empty($this->prams['cmd']) || !in_array($this->prams['cmd'], $this->commands)) {
            die($this->tcharset(static::$errorCode['001']));
        }
        $this->exec_command = $this->prams['cmd'];

        if (!isset($this->prams['attrs']) || empty($this->prams['attrs'])) {
            die($this->tcharset(static::$errorCode['004']));
        }

        //判断命令是否属于可执行Model的方法
//        if (!isset($this->prams['m']) || empty($this->prams['m']) || !(array_key_exists($this->prams['m'], $this->method_maps) == true)) {
//            die($this->tcharset(static::$errorCode['002']));
//        }

        //获取命令输入的attrs
        $this->exec_attrs = explode('+', $this->prams['attrs']);
        $attrs = [];
        foreach ($this->exec_attrs as $exec_attr) {
            if (stripos($exec_attr, ':') === false) {
                die($this->tcharset(static::$errorCode['101']));
            }
            list($key, $val) = explode(':', $exec_attr);
            if (!in_array($key, $this->fill_model)) {
                die($this->tcharset(static::$errorCode['101']));
            }
            $attrs[$key] = $val;
        }

        if (isset($attrs[0])) {
            die($this->tcharset(static::$errorCode['101']));
        }
        $this->exec_attrs = $attrs;
    }

    /**
     * @return string
     */
    public function help()
    {
        return PHP_EOL . $this->getGlissadeLine(4) . ' --help :  表示查询bicmd命令帮助文档' . PHP_EOL . PHP_EOL
            . $this->getGlissadeLine(3) . ' --ack :  系统设定可执行令牌' . PHP_EOL . PHP_EOL
            . $this->getGlissadeLine(5) . ' --cmd :  执行系统设定命令' . PHP_EOL . PHP_EOL
//            . $this->getGlissadeLine(7) . ' --m :  执行model方法名称' . PHP_EOL . PHP_EOL
//            . $this->getGlissadeLine(4) . ' --opid : 执行特定model需要的执行唯一标识' . PHP_EOL . PHP_EOL
            . $this->getGlissadeLine(3) . ' --attrs : 执行model需要的执行参数' . PHP_EOL . PHP_EOL;
    }


    /**
     * @param $count
     * @return string
     */
    protected function getGlissadeLine($count)
    {
        $glissade = '';
        for ($i = 0; $i < $count; $i++) {
            $glissade .= ' ';
        }
        return $glissade;
    }

    /**
     * @param $msg
     * @return string
     */
    protected function tcharset($msg)
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? iconv('UTF-8', 'gbk', $msg) : $msg;
    }

    //执行bicmd error map 对应
    protected static $errorCode = [
        '000' => ' 输入执行脚本令牌',
        '001' => ' [001] 正确输入执行系统设定命令 [ -- cmd [insert, delone, up]]',
        '002' => ' [002] 正确输入脚本可执行方法 [ --m [method_maps]]',
        '003' => ' [003] 正确输入执行特定方法需要的执行唯一标识 [--opid]',
        '004' => ' [004] 正确输入执行方法需要的执行参数 [--attrs [fill_model_pram1:test,test2+fill_user_auth_model_pram2:test1,test2 ]] : ',
        '101' => ' [101] 执行model属性被拒绝执行操作 [ fill_model]',
        '102' => ' [102] 执行model不存在',
        '106' => ' [106] 执行model致命错误查询日志',
        '200' => ' [200] exec success',
    ];

}
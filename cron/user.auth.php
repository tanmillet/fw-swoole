<?php
require_once dirname(__FILE__) . '/cli.php';
require_once "bi.cmd.php";

/**
 * Class BiUser
 */
class user_auth extends bi_cmd {

    //初始化model可操作属性
    protected $fill_model = ['ACTION_IDS', 'USER_ID', 'ROLE_ID'];

    //初始化Class可执行方法
    protected $method_maps = ['userauth' => 'UserAuth',];

    /*dbmodel驱动*/
    private $bi_user_auth_model = null;

    /**
     * 初始化必要的参数命令
     * BiUser constructor.
     */
    public function __construct()
    {
        parent::__construct();

        //执行model对应的方法
        $method = $this->exec_command . $this->method_maps['userauth'];
        $this->$method();
    }

    /**
     * 更新全部记录
     * @param int $user_id
     */
    public function upUserAuth()
    {
        if (!isset($this->exec_attrs['ACTION_IDS'])) {
            die($this->tcharset(static::$errorCode['004']));
        }
        $action_ids = explode(',', $this->exec_attrs['ACTION_IDS']);
        if (!is_array($action_ids) && !sset($action_ids[0])) {
            die($this->tcharset(static::$errorCode['004']));
        }
        $action_ids = array_filter(array_flip(array_flip($action_ids)));
        sort($action_ids);
        array_map(function ($val) {
            if (!is_numeric($val)) die($this->tcharset(static::$errorCode['004'] . $val));
        }, $action_ids);


        if (!isset($this->exec_attrs['ACTION_IDS'])) {
            $user_ids = 0;
        } else {
            $user_ids = explode(',', $this->exec_attrs['USER_ID']);
            if (is_array($user_ids) && isset($user_ids[0])) {
                $user_ids = array_filter(array_flip(array_flip($user_ids)));
                sort($user_ids);
                array_map(function ($val) {
                    if (!is_numeric($val)) die($this->tcharset(static::$errorCode['004'] . $val));
                }, $user_ids);
            } else {
                $user_ids = 0;
            }
        }


        $this->bi_user_auth_model =  &loadModel('dw/OaAuthUser');
        $this->bi_user_auth_model->updateAllUserAuth($action_ids, $user_ids);

        die($this->tcharset(static::$errorCode['200']));
    }

    /**
     * 添加数据
     */
    public function insertUserAuth()
    {
        $this->bi_user_auth_model =  &loadModel('dw/OaAuthUser');
        foreach ($this->exec_attrs as $exec_attr) {
            list($key, $val) = explode(':', $exec_attr);
            if (!in_array($key, $this->fill_user_auth_model)) {
                die($this->tcharset(static::$errorCode['101']));
            }
            $attrs[$key] = $val;
        }
        $action_ids = explode(',', $attrs['ACTION_IDS']);
        if (!is_array($action_ids) && !sset($action_ids[0])) {
            die($this->tcharset(static::$errorCode['004']) . ' ACTION_IDS');
        }
        array_map(function ($val) {
            if (!is_numeric($val)) die($this->tcharset(static::$errorCode['004'] . $val));
        }, $action_ids);

        if (!isset($attrs['USER_ID']) || empty($attrs['USER_ID']) || !is_numeric($attrs['USER_ID'])) {
            die($this->tcharset(static::$errorCode['004']) . ' USER_ID');
        }

        if (!isset($attrs['ROLE_ID']) || empty($attrs['ROLE_ID']) || !is_numeric($attrs['ROLE_ID'])) {
            die($this->tcharset(static::$errorCode['004']) . ' ROLE_ID');
        }
        //TODO 查询添加权限组是否存在

        $this->bi_user_auth_model =  &loadModel('dw/OaAuthUser');
        $this->bi_user_auth_model->insertUserAuth($attrs['USER_ID'], $attrs['ACTION_IDS'], $attrs['ROLE_ID']);

        die($this->tcharset(static::$errorCode['200']));
    }

    /**
     *删除数据操作
     */
    public function deloneUserAuth()
    {
        foreach ($this->exec_attrs as $exec_attr) {
            list($key, $val) = explode(':', $exec_attr);
            if (!in_array($key, $this->fill_user_auth_model)) {
                die($this->tcharset(static::$errorCode['101']));
            }
            $attrs[$key] = $val;
        }
        if (!isset($attrs['USER_ID']) || empty($attrs['USER_ID']) || !is_numeric($attrs['USER_ID'])) {
            die($this->tcharset(static::$errorCode['004']) . ' USER_ID');
        }

        $this->bi_user_auth_model =  &loadModel('dw/OaAuthUser');
        $this->bi_user_auth_model->delUserAuth($attrs['USER_ID']);

        die($this->tcharset(static::$errorCode['200']));
    }
}

//执行脚本入口
$biuser = new user_auth();
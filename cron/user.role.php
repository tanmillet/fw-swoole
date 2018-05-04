<?php
require_once dirname(__FILE__) . '/cli.php';
require_once "bi.cmd.php";

/**
 * Class BiUser
 */
class user_role extends bi_cmd {

    //初始化model可操作属性
    protected $fill_model = ['ROLE_ID', 'ROLE_NAME', 'DEFAULT_ACTIONS'];

    //初始化Class可执行方法
    protected $method_maps =
        [
            'userrole' => 'UserRole',
        ];

    /*dbmodel驱动*/
    private $bi_user_role_model = null;

    /**
     * 初始化必要的参数命令
     * BiUser constructor.
     */
    public function __construct()
    {
        parent::__construct();

        //执行model对应的方法
        $method = $this->exec_command . $this->method_maps['userrole'];
        $this->$method();
    }

    public function upUserRole()
    {
        if (!isset($this->exec_attrs['DEFAULT_ACTIONS'])) {
            die($this->tcharset(static::$errorCode['004']));
        }
        $action_ids = explode(',', $this->exec_attrs['DEFAULT_ACTIONS']);
        if (!is_array($action_ids) && !sset($action_ids[0])) {
            die($this->tcharset(static::$errorCode['004']));
        }
        $action_ids = array_filter(array_flip(array_flip($action_ids)));
        sort($action_ids);
        array_map(function ($val) {
            if (!is_numeric($val)) die($this->tcharset(static::$errorCode['004'] . $val));
        }, $action_ids);

        if (isset($this->exec_attrs['ROLE_ID'])) {
            $role_ids = explode(',', $this->exec_attrs['ROLE_ID']);
            if (is_array($role_ids) && isset($role_ids[0])) {
                $role_ids = array_filter(array_flip(array_flip($role_ids)));
                sort($role_ids);
                array_map(function ($val) {
                    if (!is_numeric($val)) die($this->tcharset(static::$errorCode['004'] . $val));
                }, $role_ids);
            }
        } else {
            $role_ids = 0;
        }

        $this->bi_user_role_model =  &loadModel('dw/OaUserRole');
        $this->bi_user_role_model->updateRoleAuth($action_ids, $role_ids);

        die($this->tcharset(static::$errorCode['200']));
    }

}

//执行脚本入口
$biuser = new user_role();
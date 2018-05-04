<?php

/**
 * Class OaAuthUser
 */
class Models_Role extends Sys_Model {
    private $db_oracle = null;
    private $ods_user_role_table = 'ODS.ODS_USER_ROLE';

    /**
     * DwOaAuthUserModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->db_oracle = new Libs_OracleDb();
    }

    /**
     * @param $new_add_action_ids
     * @param int $user_id
     */
    public function updateRoleAuth($new_add_action_ids, $role_ids = 0)
    {
        if (!is_array($new_add_action_ids)) {
            die($this->tcharset('[004] 正确输入执行方法需要的执行参数 [--attrs]'));
        }

        $role_infos = $this->getUserRole($role_ids);
        if (!isset($role_infos)) {
            die($this->tcharset(' [102] 执行model不存在'));
        }

        $process_datas = [];
        foreach ($role_infos as $value) {
            $process_temp['ROLE_ID'] = $value['ROLE_ID'];
            $process_temp['DEFAULT_ACTIONS'] = $value['DEFAULT_ACTIONS'];
            $process_datas[] = $process_temp;
        }
        try {
            foreach ($process_datas as $role_info) {
                $user_role_before = $role_info['DEFAULT_ACTIONS'];
                $user_role_temp = explode(',', $role_info['DEFAULT_ACTIONS']);
                $user_role_temp = array_flip(array_flip(array_merge($user_role_temp, $new_add_action_ids)));
                $user_role_info['DEFAULT_ACTIONS'] = implode(',', $user_role_temp);
                echo '--- Beginning  ROLE_ID : ' . $role_info['ROLE_ID'] . PHP_EOL . ' Before ACTION_IDS : ' . $user_role_before, PHP_EOL . '  After ACTION_IDS : ' . $user_role_info['DEFAULT_ACTIONS'] . PHP_EOL . '--- Ending', PHP_EOL;
                $this->db_oracle->update($this->ods_user_role_table, $user_role_info, ' ROLE_ID=' . $role_info['ROLE_ID']);
            }
        } catch (Exception $exception) {
            writeLog($exception->getMessage());
            die($this->tcharset('[106] 执行model致命错误查询日志'));
        }
    }


    /**
     * @param string $user_id
     * @return bool
     */
    public function getUserRole($role_id = 0)
    {
        $sql = "SELECT ROLE_ID,ROLE_NAME,DEFAULT_ACTIONS FROM " . $this->ods_user_role_table;
        if (is_array($role_id)) {
            $sql .= " WHERE ROLE_ID IN (" . implode(',', $role_id) . ")";
        }
        $data = $this->db_oracle->queryAll($sql);
        if (!$data) {
            die($this->tcharset(' [102] 执行model致命错误！ 用户信息不存在。'));
        }

        return $data;
    }


}
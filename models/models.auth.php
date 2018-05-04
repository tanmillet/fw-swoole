<?php
class Models_Auth extends Sys_Model {
    private $db_oracle = null;
    private $auth_table = 'ODS.ODS_USER_AUTH';
    private $action_config_table = 'ODS.ODS_ACTION_CONFIG';
    private $role_table = 'ODS.ODS_USER_ROLE';
    private $ods_user_auth_table = 'ODS.ODS_USER_AUTH';

    public function __construct()
    {
        parent::__construct();
        $this->db_oracle = new Libs_OracleDb();
    }

    public function isUserCanAccess($uid, $class, $action)
    {
        $auth_table = $this->auth_table;
        $action_config_table = $this->action_config_table;
        $sql = "SELECT * FROM " . $auth_table . " WHERE USER_ID=" . $uid;
        $data = $this->db_oracle->query($sql);
        if (!$data) {
            return false;
        }
        $_SESSION['user']['role_id'] = $data['ROLE_ID'];
        $sql = "SELECT ROLE_ID,ROLE_NAME,DEFAULT_ACTIONS FROM " . $this->role_table . "  WHERE ROLE_ID =" . $data['ROLE_ID'];
        $role_data = $this->db_oracle->query($sql);
        if (!$role_data) {
            return false;
        }
        $user_roles = isset($role_data['DEFAULT_ACTIONS']) ? $role_data['DEFAULT_ACTIONS'] : '';
        if (empty($user_roles)) return false;
        $user_roles = explode(',', $user_roles);
        $current_path = strtolower('/' . strtolower($class) . '/' . $action);
        $current_action_id = $this->getActionInfoByPath($current_path);
        if (!$current_action_id) {
            return false;
        }
        if (!in_array($current_action_id, $user_roles)) { // 无访问权限
            return false;
        }
        return true;
    }

    public function getUserAccess($uid)
    {
        $auth_table = $this->auth_table;
        $sql = "SELECT * FROM " . $auth_table . " WHERE USER_ID=" . $uid;
        $data = $this->db_oracle->query($sql);
        if (!$data) {
            return false;
        }
        return $data;
    }

    public function getActionInfoByPath($path)
    {
        $action_config_table = $this->action_config_table;
        $sql = "SELECT ACTION_ID FROM " . $action_config_table . " WHERE ACTION_PATH='" . $path . "'";
        $data = $this->db_oracle->query($sql);
        return $data['ACTION_ID'];
    }

    public function insertCommonUser($uid)
    {
        $table = $this->auth_table;
        $role_table = $this->role_table;
        $sql = "SELECT * FROM " . $role_table . " WHERE ROLE_ID=" . ROLE_COMMON_USER;
        $data = $this->db_oracle->query($sql);
        if (!$data) {
            return false;
        }
        $action_ids = $data['DEFAULT_ACTIONS'];
        $insert_data['ID'] = "ODS.ODS_USER_AUTH_SEQ.NEXTVAL";
        $insert_data['USER_ID'] = $uid;
        $insert_data['ROLE_ID'] = ROLE_COMMON_USER;
        $insert_data['ACTION_IDS'] = "'" . $action_ids . "'";
        $insert_data['CREATED_TIME'] = "TO_DATE('" . getDateByUnixTime() . "', 'yyyy-mm-dd hh24:mi:ss')";
        $insert_data['UPDATED_TIME'] = "TO_DATE('" . getDateByUnixTime() . "', 'yyyy-mm-dd hh24:mi:ss')";
        $result = $this->db_oracle->insert($table, $insert_data);
        return $result;
    }

    public function migrationData()
    {
        $sql = "SELECT ID FROM ODS.ODS_BI_USER WHERE BI_FLAG=1";
        $data = $this->db_oracle->queryAll($sql);
        if (!$data) {
            return false;
        }
        foreach ($data as $value) {
            $insert_data['ID'] = "ODS.ODS_USER_AUTH_SEQ.NEXTVAL";
            $insert_data['USER_ID'] = $value['ID'];
            $insert_data['ROLE_ID'] = "2";
            $insert_data['ACTION_IDS'] = "'1,2,3,4,5,7,8,9,12,13'";
            $insert_data['CREATED_TIME'] = "TO_DATE('" . getDateByUnixTime() . "', 'yyyy-mm-dd hh24:mi:ss')";
            $insert_data['UPDATED_TIME'] = "TO_DATE('" . getDateByUnixTime() . "', 'yyyy-mm-dd hh24:mi:ss')";
            $result = $this->db_oracle->insert('ODS.ODS_USER_AUTH', $insert_data);
        }
    }


    /**
     * @param $new_add_action_ids
     * @param int $user_id
     */
    public function updateAllUserAuth($new_add_action_ids, $user_ids = 0)
    {
        if (!is_array($new_add_action_ids)) {
            die($this->tcharset('[004] 正确输入执行方法需要的执行参数 [--attrs]'));
        }

        $user_auth_infos = $this->getUserAuth($user_ids);
        if (!isset($user_auth_infos)) {
            die($this->tcharset(' [102] 执行model不存在'));
        }

        $process_datas = [];
        foreach ($user_auth_infos as $value) {
            $process_temp['USER_ID'] = $value['USER_ID'];
            $process_temp['ACTION_IDS'] = $value['ACTION_IDS'];
            $process_datas[] = $process_temp;
        }
        try {
            foreach ($process_datas as $user_auth_info) {
                $user_auth_before = $user_auth_info['ACTION_IDS'];
                $user_auth_temp = explode(',', $user_auth_info['ACTION_IDS']);
                $user_auth_temp = array_flip(array_flip(array_merge($user_auth_temp, $new_add_action_ids)));
                $user_auth_info['ACTION_IDS'] = implode(',', $user_auth_temp);
                echo '--- Beginning  USER_ID : ' . $user_auth_info['USER_ID'] . PHP_EOL . ' Before ACTION_IDS : ' . $user_auth_before, PHP_EOL . '  After ACTION_IDS : ' . $user_auth_info['ACTION_IDS'] . PHP_EOL . '--- Ending', PHP_EOL;
                $this->db_oracle->update($this->ods_user_auth_table, $user_auth_info, 'USER_ID=' . $user_auth_info['USER_ID']);
            }
        } catch (Exception $exception) {
            writeLog($exception->getMessage());
            die($this->tcharset('[106] 执行model致命错误查询日志'));
        }
    }

    /**
     * 执行添加数据操作
     *
     * @param $user_id
     * @param $action_ids
     * @param $role_id
     */
    public function insertUserAuth($user_id, $action_ids, $role_id)
    {
        $id = $this->getUserAuthMaxID();
        $data = $this->getUserAuth([$user_id]);
        if ($data) {
            die($this->tcharset('[107] 执行model致命错误！用户信息存在 USER_ID：' . $user_id));
        }
        $now_time = date('Y-m-d H:i:s');
        try {
            $sql = "INSERT INTO " . $this->ods_user_auth_table . "(\"ID\",\"USER_ID\",\"ACTION_IDS\",\"CREATED_TIME\",\"UPDATED_TIME\",\"ROLE_ID\")VALUES('{$id}','{$user_id}','{$action_ids}',TO_DATE('{$now_time}','SYYYY-MM-DDHH24:MI:SS'),TO_DATE('{$now_time}','SYYYY-MM-DDHH24:MI:SS'),'{$role_id}')";
            $this->db_oracle->exec($sql);
        } catch (Exception $exception) {
            writeLog($exception->getMessage());
            die($this->tcharset('[106] 执行model致命错误查询日志'));
        }
    }

    /**
     * e
     *
     * @param $user_id
     */
    public function delUserAuth($user_id)
    {
        $data = $this->getUserAuth([$user_id]);
        if (!$data) {
            die($this->tcharset('[107] 执行model致命错误！用户信息存在 USER_ID：' . $user_id));
        }
        $sql = "DELETE FROM \"ODS\".\"ODS_USER_AUTH_TEST\" WHERE USER_ID = '{$user_id}'";

        try {
            $this->db_oracle->exec($sql);
        } catch (Exception $exception) {
            writeLog($exception->getMessage());
            die($this->tcharset('[106] 执行model致命错误查询日志'));
        }
    }

    /**
     * @return int
     */
    public function getUserAuthMaxID()
    {
        $sql = "SELECT MAX(ID) AS ID  FROM " . $this->ods_user_auth_table;
        $data = $this->db_oracle->query($sql);
        if (isset($data) || isset($data['ID'])) {
            return $data['ID'] + 1;
        }

        return 1;
    }

    /**
     * @param string $user_id
     * @return bool
     */
    public function getUserAuth($user_id = 0)
    {
        $sql = "SELECT USER_ID,ACTION_IDS FROM " . $this->ods_user_auth_table;
        if (is_array($user_id)) {
            $sql .= " WHERE USER_ID IN (" . implode(',', $user_id) . ")";
        }
        $data = $this->db_oracle->queryAll($sql);
        if (!$data) {
            die($this->tcharset(' [102] 执行model致命错误！ 用户信息不存在。'));
        }

        return $data;
    }
}
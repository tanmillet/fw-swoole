<?php

class Models_User extends Sys_Model {
    private $db_oracle = null;
    private $table = 'ODS.ODS_BI_USER';
    private $super_user_table = 'ODS.ODS_SUPER_BI_USER';
    private $oa_table = 'ODS.ODS_OA_HRMRESOURCE';

    public function __construct()
    {
        parent::__construct();
//        $this->db_oracle = &loadClass('DbOracle');
        $this->db_oracle = new Libs_OracleDb();
    }

    public function checkUser($username, $password)
    {
        $table = $this->table;
        $sql = "SELECT * FROM " . $table . " WHERE LOGINID='" . $username . "'";
        $data = $this->db_oracle->query($sql);
        if (!$data) {
            return false;
        }

        if (md5($password) != strtolower($data['PASSWORD'])) {
            return false;
        }
        return $data;
    }

    public function checkSuperUser($username, $password)
    {
        $table = $this->super_user_table;
        $sql = "SELECT * FROM " . $table . " WHERE USERNAME='" . $username . "'";
        $data = $this->db_oracle->query($sql);
        if (!$data) {
            return false;
        }

        if (md5($password) != strtolower($data['PASSWORD'])) {
            return false;
        }
        return $data;
    }

    public function checkSuperUserByUid($uid)
    {
        $table = $this->super_user_table;
        $sql = "SELECT * FROM " . $table . " WHERE USER_ID=" . $uid;
        $data = $this->db_oracle->query($sql);
        if (!$data) {
            return false;
        }

        return $data;
    }

    public function updateSuperUserPassword($uid, $password)
    {
        $password = md5($password);
        $table = $this->super_user_table;
        $update_data['PASSWORD'] = $password;
        $update_result = $this->db_oracle->update($table, $update_data, 'USER_ID=' . $uid);
        return $update_result;
    }

    public function getUserIdsByUserId($uids_array = [], $return_array = [])
    {
        if (!$uids_array) {
            return false;
        }
        $uids_string = implode(',', $uids_array);
        $table = $this->oa_table;
        $sql = "SELECT ID FROM " . $table . " WHERE MANAGERID IN(" . $uids_string . ') AND LOGINID IS NOT NULL';
        $data = $this->db_oracle->queryAll($sql);
        if (!$data) {
            return $return_array;
        }
        $process_uids_array = [];
        foreach ($data as $value) {
            $process_uids_array[] = $value['ID'];
        }
        return $this->getUserIdsByUserId($process_uids_array, array_merge($return_array, $process_uids_array));
    }

    public function getCurrentUserAllIds($uid)
    {
        if (!$uid) {
            return false;
        }
        return array_merge([
            $uid
        ], $this->getUserIdsByUserId([
            $uid
        ]));
    }

}
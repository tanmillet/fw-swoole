<?php

/**
 * Class Ctrs_Home
 */
class Ctrs_User extends Sys_Ctr {

    public function login()
    {
//        $username = trim($this->_post('username'));
        $username = 'tanchongtao';
//        $password = trim($this->_post('password'));
        $password = 'Tzt19891010';
        if (!$username || !$password) {
            $error_code = 10025;
            echo $this->setStatusCode($error_code)->responseError($error_code);
            die();
        }
        global $super_user_array;
        $super_user_array = Libs_Conf::get('super_user_array', 'ps');
        $oa_user_model = new Models_User();
        if (in_array($username, $super_user_array['username'])) {
            $check_result = $oa_user_model->checkSuperUser($username, $password);
            if (!$check_result) {
                $error_code = 10030;
                echo $this->setStatusCode($error_code)->responseError($error_code);
                die();
            }
            $uid = $check_result['USER_ID'];
        } else {
            $check_result = $oa_user_model->checkUser($username, $password);
            if (!$check_result) {
                $error_code = 10030;
                echo $this->setStatusCode($error_code)->responseError($error_code);
                die();
            }
            $uid = $check_result['ID'];
        }
        $auth_model = new Models_Auth();
        $is_user_auth = $auth_model->getUserAccess($uid); // 用户权限表是否有用户信息，如果无则插入一条记录
        if (!$is_user_auth) {
            $auth_model->insertCommonUser($uid);
        }
        if (isset($is_user_auth['ROLE_ID'])) {
            $role_id = $is_user_auth['ROLE_ID'];
        } else {
            $role_id = ROLE_COMMON_USER;
        }

        $_SESSION['user'] = [
            'uid' => $uid,
            'username' => $username
        ];
        $return_data['uid'] = $uid;
        $return_data['username'] = $username;
        $return_data['role_id'] = $role_id;
        echo $this->responseSuccess($return_data);
        die();
    }

    public function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
        echo $this->responseSuccess();
        die();
    }

    public function updatePassword()
    {
        $uid = $this->current_uid;
        $old_password = trim($this->_get('old_password'));
        $new_password = trim($this->_get('new_password'));
        $check_new_password = trim($this->_get('check_new_password'));
        if (!$old_password || !$new_password || !$check_new_password) {
            $error_code = 10033;
            echo apiReturnError($error_code);
            die();
        }
        if ($new_password != $check_new_password) {
            $error_code = 10034;
            echo apiReturnError($error_code);
            die();
        }
        $oa_user_model = &loadModel('dw/OaUser');
        $check_result = $oa_user_model->checkSuperUserByUid($uid);
        if (!$check_result) {
            $error_code = 10035;
            echo apiReturnError($error_code);
            die();
        }
        if (md5($old_password) != strtolower($check_result['PASSWORD'])) {
            $error_code = 10024;
            echo apiReturnError($error_code);
            die();
        }
        $update_result = $oa_user_model->updateSuperUserPassword($uid, $new_password);
        if (!$update_result) {
            $error_code = 10024;
            echo apiReturnError($error_code);
            die();
        }
        echo apiReturnSuccess();
        die();
    }
}
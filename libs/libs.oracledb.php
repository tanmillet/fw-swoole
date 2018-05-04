<?php

class Libs_OracleDb {
    public $connid; // 连接句柄
    public $stmt_id;
    public $debug = 1;
    // 是否输出调试信息

    // 初始化数据库信息
    function __construct($db_config = [], $debug = 1)
    {
        if (!$db_config) {
            $dw_servers = Libs_Conf::get('dw_servers', ENV_FILE);
            $db_config = $dw_servers;
        }
        $this->debug = $debug;
        $host = $db_config['host'];
        $port = $db_config['port'];
        $service_name = $db_config['service_name'];
        $username = $db_config['username'];
        $password = $db_config['password'];
        $connect_string = "(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST ={$host})(PORT = {$port}))(CONNECT_DATA=(SERVER = DEDICATED)(SERVICE_NAME = {$service_name})))";
        $this->connect($username, $password, $connect_string);
    }

    // 数据库连接
    function connect($dbuser, $dbpwd, $connect_string, $charset = 'utf8')
    {
        if (!$this->connid = oci_connect($dbuser, $dbpwd, $connect_string, $charset)) {
            exit('数据库错误！');
        }
        return $this->connid;
    }

    // 执行sql语句
    function exec($sql)
    {
        $stmt = oci_parse($this->connid, $sql);
        if (!oci_execute($stmt)) {
            $this->halt('执行SQL语句错误', $sql, $stmt);
            return false;
        }
        return $stmt;
    }

    // 执行SELECT语句
    function queryAll($sql, $keyField = '')
    {
        $array = [];
        $stmt = $this->exec($sql);
        while ($row = oci_fetch_array($stmt, OCI_RETURN_NULLS)) {
            if (!$keyField) {
                $array[] = $row;
            } else {
                $array[] = $row[$keyField];
            }
        }
        $this->freeResult($stmt);
        return $array;
    }

    // 执行INSERT语句
    function insert($tablename, $array)
    {
        return $this->exec("INSERT INTO  $tablename(" . implode(',', array_keys($array)) . ") VALUES(" . implode(",", $array) . ")");
    }

    // 执行UPDATE语句
    function update($tablename, $array, $where = '1')
    {
        $sql = '';
        foreach ($array as $k => $v) {
            $sql .= ", $k='$v'";
        }
        $sql = substr($sql, 1);
        $sql = "UPDATE $tablename SET $sql WHERE $where";
        return $this->exec($sql);
    }

    // 执行SELECT语句获得一条记录
    function query($sql)
    {
        $stmt = $this->exec($sql);
        $rs = oci_fetch_array($stmt, OCI_RETURN_NULLS);
        $this->freeResult($stmt);
        return $rs;
    }

    // 获取刚插入记录ID
    function insert_id()
    {
    }

    // 获取上一语句的影响记录数
    function affected_rows($stmt)
    {
        return oci_num_rows($stmt);
    }

    // 输出数据库错误
    function halt($message = '', $sql = '', $stmt)
    {
        $errormsg = "<b>ORACLE Query : </b><font style='font-size:14px;color:#FF0000;'>$sql</font> <br /><b> ORACLE Error : </b>" . $this->error($stmt) . " <br /><b> Message : </b> $message";
        if ($this->debug) {
            echo '<div style="font-size:12px;text-align:left; border:1px solid #9cc9e0; padding:1px 4px;color:#000000;font-family:Arial, Helvetica,sans-serif;"><span>' . $errormsg . '</span></div>';
            exit();
        }
    }

    // 获取数据库错误信息
    function error($stmt)
    {
        $e = @oci_error($stmt);
        return $e['message'];
    }

    function freeResult($stmt)
    {
        if (is_resource($stmt)) {
            oci_free_statement($stmt);
        }
    }

}

<?php

class Sys_Model {
    public $db;

    public function __construct($init_db_servers = [])
    {
        if (!$init_db_servers) {
            $init_db_servers = $db_servers = Libs_Conf::get('db_servers', ENV_FILE);
        }

        $this->db = new Libs_Db($init_db_servers);
    }

    public function getLimit($page, $count)
    {
        $offset = ($page - 1) * $count;
        $limit = ' LIMIT ' . $offset . ',' . $count . ' ';
        return $limit;
    }

    /**
     * 封装oracle分页sql
     * $sql 原始查询sql
     */
    public function getPageSql($sql, $page, $count)
    {
        $offset = $page * $count;
        $row_num = ' ROWNUM<=' . intval($offset);
        $parse_sql = 'SELECT * FROM (SELECT a.*,ROWNUM AS RN FROM (' . $sql . ') a) b WHERE RN>=' . intval(($page - 1) * $count + 1) . ' AND RN<=' . ($page * $count);
        return $parse_sql;
    }

    /**
     * @param $msg
     * @return string
     */
    protected function tcharset($msg)
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? iconv('UTF-8', 'gbk', $msg) : $msg;
    }

}
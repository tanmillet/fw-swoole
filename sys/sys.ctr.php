<?php

class Sys_Ctr {
    /**
     * List of uri segments
     *
     * @var array
     * @access public
     */
    public $uri_segment = [];
    public $oauth_info = [];
    public $current_user_info = [];
    public $current_uid = 0;

    public function __construct($data = null)
    {
        if (isset($data['uri_segment']) && !empty($data['uri_segment'])) {
            $this->uri_segment = $data['uri_segment'];
        }
        if (isset($data['current_user_info']) && !empty($data['current_user_info'])) {
            $this->current_user_info = $data['current_user_info'];
            $this->current_uid = $data['current_user_info']['uid'];
        }
    }
    // --------------------------------------------------------------------

    /**
     * Fetch an item from the GET array
     *
     * @access public
     * @param
     *            string
     * @return string
     */
    function _get($index = NULL)
    {
        // Check if a field has been provided
        if ($index === NULL and !empty($_GET)) {
            $get = [];

            // loop through the full _GET array
            foreach (array_keys($_GET) as $key) {
                $get[$key] = $this->fetchFromArray($_GET, $key);
            }
            return $get;
        }

        return $this->fetchFromArray($_GET, $index);
    }

    // --------------------------------------------------------------------

    /**
     * Fetch an item from the POST array
     *
     * @access public
     * @param
     *            string
     * @return string
     */
    function _post($index = NULL)
    {
        // Check if a field has been provided
        if ($index === NULL and !empty($_POST)) {
            $post = [];

            // Loop through the full _POST array and return it
            foreach (array_keys($_POST) as $key) {
                $post[$key] = $this->fetchFromArray($_POST, $key);
            }
            return $post;
        }

        return $this->fetchFromArray($_POST, $index);
    }

    /**
     * Fetch an item from the uri_segment array
     *
     * @access public
     * @param
     *            string
     * @return string
     */
    function uriSegment($index = NULL)
    {
        // Check if a field has been provided
        if ($index === NULL and !empty($this->uri_segment)) {
            $uri_segment = [];

            // Loop through the full _POST array and return it
            foreach (array_keys($this->uri_segment) as $key) {
                $uri_segment[$key] = $this->fetchFromArray($this->uri_segment, $key);
            }
            return $uri_segment;
        }

        return $this->fetchFromArray($this->uri_segment, $index);
    }

    /**
     * Fetch from array
     *
     * This is a helper function to retrieve values from global arrays
     *
     * @access private
     * @param
     *            array
     * @param
     *            string
     * @return string
     */
    function fetchFromArray(&$array, $index = '')
    {
        if (!isset($array[$index])) {
            return FALSE;
        }
        return $array[$index];
    }

    function getPageAndCount($default_count = 20, $default_page = 1)
    {
        if (isset($_GET['page'])) {
            $page = intval($this->_get('page'));
            if ($page == 0 || !isIntval($_GET['page'])) {
                $error_code = 10009;
                $params = [
                    'page',
                    'int',
                    htmlspecialchars($_GET['page'])
                ];
                echo apiReturnError($error_code, $params);
                die();
            }
        } else {
            $page = 0;
        }
        $page = ($page > 0) ? $page : $default_page;
        if (isset($_GET['count'])) {
            $count = intval($this->_get('count'));
            if ($count == 0 || !isIntval($_GET['count']) || $count > 100) {
                $error_code = 10009;
                $params = [
                    'count',
                    'int[1~100]',
                    htmlspecialchars($_GET['count'])
                ];
                echo apiReturnError($error_code, $params);
                die();
            }
        } else {
            $count = 0;
        }
        $count = ($count > 0) ? $count : $default_count;
        $data['page'] = $page;
        $data['count'] = $count;
        return $data;
    }

    function getSinceIDAndMaxID()
    {
        if (isset($_GET['since_id'])) {
            $since_id = intval($this->_get('since_id'));
            if (!isIntval($_GET['since_id'])) {
                $error_code = 10009;
                $params = [
                    'since_id',
                    'int',
                    $_GET['since_id']
                ];
                echo apiReturnError($error_code, $params);
                die();
            }
        } else {
            $since_id = 0;
        }
        $since_id = ($since_id > 0) ? $since_id : 0;
        if (isset($_GET['max_id'])) {
            $max_id = intval($this->_get('max_id'));
            if (!isIntval($_GET['max_id'])) {
                $error_code = 10009;
                $params = [
                    'max_id',
                    'int',
                    $_GET['max_id']
                ];
                echo apiReturnError($error_code, $params);
                die();
            }
        } else {
            $max_id = 0;
        }
        $max_id = ($max_id > 0) ? $max_id : 0;
        $data['since_id'] = $since_id;
        $data['max_id'] = $max_id;
        return $data;
    }

    /**
     * keyValidation
     */
    public function parameterValidation($array, $fields)
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $array)) {
                return false;
            }
        }
        return true;
    }

    /**
     * valueValidation
     */
    public function valueValidation($value, $field)
    {
        if (!in_array($value, $field)) {
            return false;
        }
        return true;
    }

}
<?php
if (!defined('IN_SITE')) die('The Request Not Found');

// Load .env
if (file_exists(__DIR__.'/../.env')) {
    $lines = file(__DIR__.'/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('=', $line, 2);
        if(count($parts) == 2) {
            $key = trim($parts[0]);
            $val = trim($parts[1]);
            $val = trim($val, '"\'');
            $_ENV[$key] = $val;
        }
    }
}

session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
$base_url = 'https://'.$_SERVER['SERVER_NAME'].'/'; // Thay url web bạn

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// $base_url = 'https://localhost/';
class DMH
{
    public $ketnoi;
    function connect()
    {
        if (!$this->ketnoi)
        {
            $db_host = $_ENV['DB_HOST'] ?? 'localhost';
            $db_user = $_ENV['DB_USER'] ?? 'kwkrbcce_dientuhieu';
            $db_pass = $_ENV['DB_PASS'] ?? 'SayTHC369@';
            $db_name = $_ENV['DB_NAME'] ?? 'kwkrbcce_dienmayhieulapvo';
            
            try {
                $this->ketnoi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
                mysqli_query($this->ketnoi, "set names 'utf8'");
            } catch (Throwable $e) {
                die('Hệ thống đang bảo trì hoặc mất kết nối CSDL. Vui lòng quay lại sau.');
            }
        }
    }
    function dis_connect()
    {
        if ($this->ketnoi)
        {
            mysqli_close($this->ketnoi);
        }
    }
    function getUser($username)
    {
        $this->connect();
        $row = $this->ketnoi->query("SELECT * FROM `users` WHERE `username` = '$username' ")->fetch_array();
        return $row;
    }
    function site($data)
    {
        $this->connect();
        $row = $this->ketnoi->query("SELECT * FROM `options` WHERE `key` = '$data' ")->fetch_array();
        return $row ? $row['value'] : null;
    }
    function query($sql)
    {
        $this->connect();
        $row = $this->ketnoi->query($sql);
        return $row;
    }
    function cong($table, $data, $sotien, $where)
    {
        $this->connect();
        $row = $this->ketnoi->query("UPDATE `$table` SET `$data` = `$data` + '$sotien' WHERE $where ");
        return $row;
    }
    function tru($table, $data, $sotien, $where)
    {
        $this->connect();
        $row = $this->ketnoi->query("UPDATE `$table` SET `$data` = `$data` - '$sotien' WHERE $where ");
        return $row;
    }
    function insert($table, $data)
    {
        $this->connect();
        $field_list = '';
        $value_list = '';
        foreach ($data as $key => $value)
        {
            $field_list .= ",$key";
            $value_list .= ",'".mysqli_real_escape_string($this->ketnoi, $value)."'";
        }
        $sql = 'INSERT INTO '.$table. '('.trim($field_list, ',').') VALUES ('.trim($value_list, ',').')';
 
        return mysqli_query($this->ketnoi, $sql);
    }
    function update($table, $data, $where)
    {
        $this->connect();
        $sql = '';
        foreach ($data as $key => $value)
        {
            $sql .= "$key = '".mysqli_real_escape_string($this->ketnoi, $value)."',";
        }
        $sql = 'UPDATE '.$table. ' SET '.trim($sql, ',').' WHERE '.$where;
        return mysqli_query($this->ketnoi, $sql);
    }
    function update_value($table, $data, $where, $value1)
    {
        $this->connect();
        $sql = '';
        foreach ($data as $key => $value){
            $sql .= "$key = '".mysqli_real_escape_string($this->ketnoi, $value)."',";
        }
        $sql = 'UPDATE '.$table. ' SET '.trim($sql, ',').' WHERE '.$where.' LIMIT '.$value1;
        return mysqli_query($this->ketnoi, $sql);
    }
    function remove($table, $where)
    {
        $this->connect();
        $sql = "DELETE FROM $table WHERE $where";
        return mysqli_query($this->ketnoi, $sql);
    }
    function get_list($sql)
    {
        $this->connect();
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result)
        {
            die ('Câu truy vấn bị sai');
        }
        $return = array();
        while ($row = mysqli_fetch_assoc($result))
        {
            $return[] = $row;
        }
        mysqli_free_result($result);
        return $return;
    }
    function get_row($sql)
    {
        $this->connect();
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result)
        {
            die ('Câu truy vấn bị sai');
        }
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        if ($row)
        {
            return $row;
        }
        return false;
    }
    function num_rows($sql)
    {
        $this->connect();
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result)
        {
            die ('Câu truy vấn bị sai');
        }
        $row = mysqli_num_rows($result);
        mysqli_free_result($result);
        if ($row)
        {
            return $row;
        }
        return false;
    }
    /**
     * Truy vấn prepared statement an toàn, tránh SQL injection.
     * Dùng cho các truy vấn đọc dữ liệu 1 dòng với tham số từ user.
     */
    function prepared_get_row($sql, $params = [])
    {
        $this->connect();
        $stmt = mysqli_prepare($this->ketnoi, $sql);
        if (!$stmt) return false;
        if (!empty($params)) {
            $types = '';
            $bindParams = [];
            foreach ($params as $p) {
                if (is_int($p)) $types .= 'i';
                elseif (is_double($p)) $types .= 'd';
                else $types .= 's';
                $bindParams[] = $p;
            }
            $refs = [];
            foreach ($bindParams as $key => $value) {
                $refs[$key] = &$bindParams[$key];
            }
            array_unshift($refs, $types);
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row ?: false;
    }
}
$getUser = null;
$my_username = false;
$my_money = 0;
$verifx = 0;
$my_level = null;
if(isset($_COOKIE['token']))
{ 
    $DMH = new DMH;
    $getUser = $DMH->prepared_get_row("SELECT * FROM users WHERE tokenlog = ?", [$_COOKIE['token']]);
    if($getUser) {
        $my_username = True;
        $my_money = $getUser['money'];
        $verifx = $getUser['verify'];
        $my_level = $getUser['level'];
    }
    if(!$getUser) {
        unset($_COOKIE['token']);
        setcookie('token', null, -1, '/');
        header('Location: /');
        die();
    }
    if ($getUser['money'] < 0) {
        $DMH->update("users", array(
            'banned' => 'OFF'
        ), "tokenlog = '".$_COOKIE['token']."' ");
        unset($_COOKIE['token']);
        setcookie('token', null, -1, '/');
        header('Location: /');
        die();
    }
    if($getUser['tokenlog'] != $_COOKIE['token']) {
        unset($_COOKIE['token']);
        setcookie('token', null, -1, '/');
        header('Location: /');
        die();
    }
    if($getUser['banned'] != 'ON') {
        unset($_COOKIE['token']);
        setcookie('token', null, -1, '/');
        header('Location: /');
        die();
    }
}
else
{

    $my_level = NULL;
    $verifx = $my_money = 0;
}
function CheckLogin()
{
    global $my_username;
    if($my_username != True)
    {   
        $_SESSION['url'] = $_SERVER['REQUEST_URI'];
        return die('<script type="text/javascript">setTimeout(function(){ location.href = "'.BASE_URL('dang-nhap').'" }, 0);</script>');
    }
}
function CheckVeri()
{
    global $verifx;
    if($verifx != 1) {
        return die('<script type="text/javascript">setTimeout(function(){ location.href = "'.BASE_URL('').'" }, 0);</script>');
    }
}
function CheckAdmin()
{
    global $my_level;
    if($my_level != 'admin' && $my_level != 'bct')
    {
        return die('<script type="text/javascript">window.location.href = "/pages/admin/LoginAdmin.php";</script>');
    }
    else
    {
        if(empty($_SESSION['loginadmin']))
        {
            return die('<script type="text/javascript">window.location.href = "/pages/admin/LoginAdmin.php";</script>');
        }
    }
}

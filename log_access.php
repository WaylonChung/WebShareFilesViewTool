<?php
// 记录访问日志
function logAccess() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $time = time();
    $ua = $_SERVER['HTTP_USER_AGENT'];
    $log = "{$ip}|{$time}|{$ua}\n";
    file_put_contents('access.log', $log, FILE_APPEND);
}

// 在文件浏览器PHP文件开头调用
require_once('log_access.php');
logAccess();
?>

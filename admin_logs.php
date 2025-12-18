<?php
// 密码验证
session_start();
if(!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// 获取IP归属地函数
function getIpLocation($ip) {
    if($ip == '::1') return '本地访问';
    $url = "http://ip-api.com/json/{$ip}?lang=zh-CN";
    $data = @file_get_contents($url);
    return $data ? json_decode($data)->country.'·'.json_decode($data)->city : '未知';
}
function LogPrint($data) {
echo $data;
}


// 读取日志
$logFile = 'access.log';
$logs = file_exists($logFile) ? 
    array_reverse(array_slice(file($logFile, FILE_IGNORE_NEW_LINES), -50)) : LogPrint("日志文件不存在");
?>
<!DOCTYPE html>
<html>
<head>
    <title>访问日志</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>最近50条访问记录</h1>
    <table>
        <tr>
            <th>IP地址</th>
            <th>归属地</th>
            <th>访问时间</th>
            <th>用户代理</th>
        </tr>
        <?php foreach($logs as $log): 
            list($ip, $time, $ua) = explode('|', $log);
        ?>
        <tr>
            <td><?= htmlspecialchars($ip) ?></td>
            <td><?= getIpLocation($ip) ?></td>
            <td><?= date('Y-m-d H:i:s', $time) ?></td>
            <td><?= htmlspecialchars($ua) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>


<?php
//session_start();
$basePath = __DIR__;
$requestPath = isset($_GET['path']) ? $_GET['path'] : '';
$isDir = isset($_GET['isDir']) ? $_GET['isDir'] === '1' : false;
$fullPath = realpath($basePath.'/'.$requestPath);
// 安全检查
if (strpos($fullPath, realpath($basePath)) !== 0) {
    die('非法路径访问');
}

if ($isDir) 
{
	set_time_limit(8000);
    // 文件夹打包下载
    $zipName = basename($fullPath) . '.zip';
    $zipPath = sys_get_temp_dir() . '/' . $zipName;
	try
	{
    $zip = new ZipArchive();
	} 
	catch (Exception $e) 
	{
    //echo '错误: ',  $e->getMessage(), "\n";
    }

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) 
	{
        $files = new RecursiveIteratorIterator
        (
            new RecursiveDirectoryIterator($fullPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        foreach ($files as $file)
        {
            if (!$file->isDir())
            {
                $relativePath = substr($file->getRealPath(), strlen($fullPath) + 1);
                $zip->addFile($file->getRealPath(), $relativePath);
            }
        }

        $zip->close();
		$_SESSION['compress_progress'] = 1;
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipName . '"');
	    ob_clean();//Aug,12 2025 血的教训，PHP在执行header()函数前可能将输出缓冲（如ob_start()）中的内容发送到客户端，导致文件头出现额外字符0d0a。解决方法是：在发送文件前调用ob_clean()清理输出缓冲区 ‌

        $handle = fopen($zipPath, "rb");
        if ($handle)
        {
            while (!feof($handle)) 
			{
                echo fread($handle, 1024*1024*5); // 每次读取1024字节
                flush(); // 确保输出到浏览器
            }
            fclose($handle);
        }

        unlink($zipPath);
        exit;
    }
} 
else 
{
    // 文件下载
    if (file_exists($fullPath)) 
	{
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($fullPath).'"');
	    //header('Content-Disposition: attachment; filename="test.exe"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length:'. filesize($fullPath));
		ob_clean();//Aug,12 2025 血的教训，PHP在执行header()函数前可能将输出缓冲（如ob_start()）中的内容发送到客户端，导致文件头出现额外字符0d0a。解决方法是：在发送文件前调用ob_clean()清理输出缓冲区 ‌
		//ini_set('memory_limit', '256M'); // 将内存限制设置为256MB
        $handle = fopen($fullPath, "rb");
        if ($handle) 
        {
	
			while (!feof($handle)) 
			{	
				$content=fread($handle, 1024*1024*5);
				echo $content; // 每次读取1024字节
				flush(); // 确保输出到浏览器
			}
			fclose($handle);
		}
        exit;
    }
}
?>

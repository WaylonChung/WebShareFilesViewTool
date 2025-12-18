<?php
header('Content-Type: application/json');

require_once('log_access.php');
logAccess();


function getFileCount($path) {
    $count = 0;
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $file) {
        if ($file->isFile()) $count++;
    }
    return $count;
}

function getDirSize($path) {
    $size = 0;
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $file) {
        if ($file->isFile()) $size += $file->getSize();
    }
    return $size;
}

$basePath = __DIR__;
$requestPath = isset($_GET['path']) ? $_GET['path'] : '';
$fullPath = realpath($basePath . '/' . $requestPath);

// 安全检查
if (strpos($fullPath, realpath($basePath)) !== 0) {
    die(json_encode(['error' => '非法路径访问']));
}

$result = ['dirs' => [], 'files' => []];

if (is_dir($fullPath)) {
    foreach (scandir($fullPath) as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $itemPath = $fullPath . '/' . $item;
        if (is_dir($itemPath)) {
            $result['dirs'][] = [
                'name' => $item,
                'fileCount' => getFileCount($itemPath),
                'size' => getDirSize($itemPath),
                'mtime' => filemtime($itemPath)
            ];
        } else {
            $result['files'][] = [
                'name' => $item,
                'size' => filesize($itemPath),
                'mtime' => filemtime($itemPath)
            ];
        }
    }
}
echo json_encode($result);
?>

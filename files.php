<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration
$UPLOAD_DIR =  './'; // Change this to your desired folder
$ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'doc', 'docx', 'zip', 'mp4', 'mp3','php'];

function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

function getFileIcon($extension) {
    $icons = [
        'jpg' => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️', 'gif' => '🖼️',
        'pdf' => '📄', 'txt' => '📝', 'doc' => '📄', 'docx' => '📄',
        'zip' => '🗜️', 'rar' => '🗜️',
        'mp4' => '🎥', 'avi' => '🎥', 'mov' => '🎥',
        'mp3' => '🎵', 'wav' => '🎵',
        'folder' => '📁'
    ];
    return $icons[$extension] ?? '📄';
}

function isImage($extension) {
    return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
}

try {
    // Create directory if it doesn't exist
    if (!is_dir($UPLOAD_DIR)) {
        mkdir($UPLOAD_DIR, 0755, true);
    }

    $files = [];
    $directories = [];
    
    if ($handle = opendir($UPLOAD_DIR)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $fullPath = $UPLOAD_DIR . $entry;
                $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                
                if (is_dir($fullPath)) {
                    $directories[] = [
                        'name' => $entry,
                        'type' => 'folder',
                        'icon' => getFileIcon('folder'),
                        'size' => 'Folder',
                        'modified' => date('M j, Y H:i', filemtime($fullPath))
                    ];
                } elseif (in_array($extension, $ALLOWED_EXTENSIONS)) {
                    $files[] = [
                        'name' => $entry,
                        'type' => 'file',
                        'extension' => $extension,
                        'icon' => getFileIcon($extension),
                        'size' => formatFileSize(filesize($fullPath)),
                        'modified' => date('M j, Y H:i', filemtime($fullPath)),
                        'path' => $UPLOAD_DIR . $entry,
                        'isImage' => isImage($extension)
                    ];
                }
            }
        }
        closedir($handle);
    }

    // Sort files and directories
    usort($directories, function($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });
    usort($files, function($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });

    // Combine directories first, then files
    $allItems = array_merge($directories, $files);

    echo json_encode([
        'success' => true,
        'items' => $allItems,
        'totalCount' => count($allItems),
        'path' => $UPLOAD_DIR
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

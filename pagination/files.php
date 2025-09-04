<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Configuration
$UPLOAD_DIR =  './'; // Change this to your desired folder
$ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt', 'doc', 'docx', 'zip', 'mp4', 'mp3','php'];

// Pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = isset($_GET['items_per_page']) ? max(1, min(100, intval($_GET['items_per_page']))) : 20;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

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
        'php' => '💻', 'html' => '🌐', 'css' => '🎨', 'js' => '⚡',
        'folder' => '📁'
    ];
    return $icons[$extension] ?? '📄';
}

function isImage($extension) {
    return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
}

function matchesSearch($filename, $search) {
    if (empty($search)) return true;
    return stripos($filename, $search) !== false;
}

function getSystemInfo() {
    $info = [];
    
    // Server information
    $info['server'] = [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'operating_system' => php_uname('s') . ' ' . php_uname('r'),
        'architecture' => php_uname('m'),
        'hostname' => php_uname('n'),
        'server_time' => date('Y-m-d H:i:s T'),
        'timezone' => date_default_timezone_get()
    ];
    
    // Memory information
    $info['memory'] = [
        'memory_limit' => ini_get('memory_limit'),
        'memory_usage' => formatFileSize(memory_get_usage()),
        'memory_peak' => formatFileSize(memory_get_peak_usage()),
        'memory_usage_real' => formatFileSize(memory_get_usage(true))
    ];
    
    // Disk information
    $totalBytes = disk_total_space('.');
    $freeBytes = disk_free_space('.');
    $usedBytes = $totalBytes - $freeBytes;
    
    $info['disk'] = [
        'total_space' => formatFileSize($totalBytes),
        'free_space' => formatFileSize($freeBytes),
        'used_space' => formatFileSize($usedBytes),
        'usage_percent' => round(($usedBytes / $totalBytes) * 100, 2)
    ];
    
    // PHP configuration
    $info['php_config'] = [
        'max_execution_time' => ini_get('max_execution_time'),
        'max_input_time' => ini_get('max_input_time'),
        'post_max_size' => ini_get('post_max_size'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'max_file_uploads' => ini_get('max_file_uploads'),
        'default_socket_timeout' => ini_get('default_socket_timeout')
    ];
    
    return $info;
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
                // Apply search filter
                if (!matchesSearch($entry, $search)) {
                    continue;
                }
                
                $fullPath = $UPLOAD_DIR . $entry;
                $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                $modTime = filemtime($fullPath);
                $createTime = filectime($fullPath);
                
                if (is_dir($fullPath)) {
                    $directories[] = [
                        'name' => $entry,
                        'type' => 'folder',
                        'icon' => getFileIcon('folder'),
                        'size' => 'Folder',
                        'modified' => date('M j, Y H:i', $modTime),
                        'modified_timestamp' => $modTime,
                        'created' => date('M j, Y H:i', $createTime),
                        'created_timestamp' => $createTime,
                        'path' => $fullPath,
                        'isImage' => false,
                        'priority' => 2 // Lower priority for folders
                    ];
                } elseif (in_array($extension, $ALLOWED_EXTENSIONS)) {
                    $isImg = isImage($extension);
                    $files[] = [
                        'name' => $entry,
                        'type' => 'file',
                        'extension' => $extension,
                        'icon' => getFileIcon($extension),
                        'size' => formatFileSize(filesize($fullPath)),
                        'modified' => date('M j, Y H:i', $modTime),
                        'modified_timestamp' => $modTime,
                        'created' => date('M j, Y H:i', $createTime),
                        'created_timestamp' => $createTime,
                        'path' => $UPLOAD_DIR . $entry,
                        'isImage' => $isImg,
                        'priority' => $isImg ? 0 : 1 // Highest priority for images
                    ];
                }
            }
        }
        closedir($handle);
    }

    // Combine all items
    $allItems = array_merge($files, $directories);
    
    // Sort by priority (images first), then by modification time (newest first)
    usort($allItems, function($a, $b) {
        // First sort by priority (images first)
        if ($a['priority'] !== $b['priority']) {
            return $a['priority'] - $b['priority'];
        }
        // Then sort by modification time (newest first)
        return $b['modified_timestamp'] - $a['modified_timestamp'];
    });

    // Calculate pagination
    $totalItems = count($allItems);
    $totalPages = max(1, ceil($totalItems / $itemsPerPage));
    $currentPage = min($page, $totalPages);
    
    // Calculate offset and slice the array for current page
    $offset = ($currentPage - 1) * $itemsPerPage;
    $paginatedItems = array_slice($allItems, $offset, $itemsPerPage);

    // Get system information
    $systemInfo = getSystemInfo();
    
    // Calculate statistics for all items (not just current page)
    $totalFiles = count($files);
    $totalFolders = count($directories);
    $totalImages = count(array_filter($files, function($file) { return $file['isImage']; }));

    // Pagination info
    $pagination = [
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'items_per_page' => $itemsPerPage,
        'total_items' => $totalItems,
        'showing_from' => $offset + 1,
        'showing_to' => min($offset + $itemsPerPage, $totalItems),
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'previous_page' => $currentPage > 1 ? $currentPage - 1 : null,
        'next_page' => $currentPage < $totalPages ? $currentPage + 1 : null
    ];

    echo json_encode([
        'success' => true,
        'items' => $paginatedItems,
        'pagination' => $pagination,
        'system_info' => $systemInfo,
        'statistics' => [
            'total_items' => $totalItems,
            'total_files' => $totalFiles,
            'total_folders' => $totalFolders,
            'total_images' => $totalImages,
            'displayed_items' => count($paginatedItems)
        ],
        'filters' => [
            'search' => $search,
            'page' => $currentPage,
            'items_per_page' => $itemsPerPage
        ],
        'path' => $UPLOAD_DIR,
        'timestamp' => time(),
        'sorted_by' => 'priority_and_modification_time'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'timestamp' => time()
    ]);
}
?>

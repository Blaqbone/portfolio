<?php
// includes/functions.php

// File upload handling
function handleFileUpload($file, $allowedTypes, $uploadDir, $maxSize = 5242880) { // 5MB default
    $errors = [];
    
    // Check file size
    if ($file['size'] > $maxSize) {
        $errors[] = "File size must be less than " . ($maxSize / 1048576) . "MB";
    }
    
    // Check file type
    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileType, $allowedTypes)) {
        $errors[] = "Sorry, only " . implode(", ", $allowedTypes) . " files are allowed";
    }
    
    // Generate unique filename
    $fileName = uniqid() . '_' . time() . '.' . $fileType;
    $targetPath = $uploadDir . $fileName;
    
    // If no errors, try to upload file
    if (empty($errors)) {
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $errors[] = "Sorry, there was an error uploading your file.";
        }
    }
    
    return [
        'success' => empty($errors),
        'errors' => $errors,
        'fileName' => empty($errors) ? $fileName : null
    ];
}

// Sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Generate pagination links
function generatePagination($currentPage, $totalPages, $urlPattern) {
    $links = [];
    
    // Always show first page
    $links[] = [
        'page' => 1,
        'text' => '1',
        'current' => ($currentPage === 1)
    ];
    
    // Calculate range of pages to show
    $start = max(2, $currentPage - 2);
    $end = min($totalPages - 1, $currentPage + 2);
    
    // Add ellipsis after first page if needed
    if ($start > 2) {
        $links[] = ['text' => '...', 'page' => null];
    }
    
    // Add pages in range
    for ($i = $start; $i <= $end; $i++) {
        $links[] = [
            'page' => $i,
            'text' => $i,
            'current' => ($currentPage === $i)
        ];
    }
    
    // Add ellipsis before last page if needed
    if ($end < $totalPages - 1) {
        $links[] = ['text' => '...', 'page' => null];
    }
    
    // Always show last page if there is more than one page
    if ($totalPages > 1) {
        $links[] = [
            'page' => $totalPages,
            'text' => $totalPages,
            'current' => ($currentPage === $totalPages)
        ];
    }
    
    return $links;
}

// Format date
function formatDate($date, $format = 'M d, Y H:i') {
    return date($format, strtotime($date));
}

// Get user details
function getUserDetails($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Check if user has permission to access resource
function checkPermission($resourceType, $resourceId, $userId) {
    global $conn;
    
    switch ($resourceType) {
        case 'note':
            $table = 'notes';
            break;
        case 'project':
            $table = 'projects';
            break;
        default:
            return false;
    }
    
    $stmt = $conn->prepare("SELECT user_id FROM $table WHERE id = ?");
    $stmt->bind_param("i", $resourceId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    return $result && $result['user_id'] === $userId;
}

// Get file size in human readable format
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Get user statistics
function getUserStats($userId) {
    global $conn;
    
    $stats = [
        'total_notes' => 0,
        'total_projects' => 0,
        'last_activity' => null
    ];
    
    // Get total notes
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notes WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stats['total_notes'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Get total projects
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM projects WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stats['total_projects'] = $stmt->get_result()->fetch_assoc()['count'];
    
    // Get last activity
    $stmt = $conn->prepare("SELECT created_at FROM user_activities WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stats['last_activity'] = $result ? $result['created_at'] : null;
    
    return $stats;
}
?>

<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

//allowed file types for each converter
$allowedTypes = [
    'image' => ['jpg', 'jpeg', 'png', 'webp', 'bmp', 'gif'],
    'video' => ['mp4', 'webm', 'mov', 'mkv', '3gp', 'wmv', 'ogv'],
    'audio' => ['mp3', 'ogg', 'wav', 'aac', 'm4a', 'flac', 'webm', 'wma'],
    'document' => ['pdf', 'docx', 'doc', 'pptx', 'ppt', 'xlsx', 'xls', 'txt', 'odt', 'rtf']
];

//max file sizes for each type (in bytes)
$maxSizes = [
    'image' => 25 * 1024 * 1024,       // 25MB
    'video' => 100 * 1024 * 1024,      // 100MB
    'audio' => 30 * 1024 * 1024,       // 30MB
    'document' => 20 * 1024 * 1024     // 20MB
];

// Get the converter type from the referrer URL
$referrer = parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_PATH);
$converterType = 'image'; 
if (strpos($referrer, 'video.html') !== false) {
    $converterType = 'video';
} elseif (strpos($referrer, 'audio.html') !== false) {
    $converterType = 'audio';
} elseif (strpos($referrer, 'document.html') !== false) {
    $converterType = 'document';
}

// if file was uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileInput'])) {
    $file = $_FILES['fileInput'];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'File upload error: ' . $file['error']]);
        exit;
    }
    
    // Check file size
    if ($file['size'] > $maxSizes[$converterType]) {
        echo json_encode(['error' => 'File size exceeds maximum limit for ' . $converterType . ' files']);
        exit;
    }
    
    // Get file extension
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Check if file type is allowed
    if (!in_array($fileExt, $allowedTypes[$converterType])) {
        echo json_encode(['error' => 'Invalid file type for ' . $converterType . ' converter']);
        exit;
    }
    
    // Get target format from POST data
    $targetFormat = $_POST['format'] ?? '';
    if (empty($targetFormat)) {
        echo json_encode(['error' => 'No target format specified']);
        exit;
    }
    
    // Create uploads directory if it doesn't exist
    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $originalFilename = pathinfo($file['name'], PATHINFO_FILENAME);
    $uniqueId = uniqid();
    $tempFilePath = $uploadDir . $uniqueId . '_' . $originalFilename . '.' . $fileExt;
    $outputFilename = $originalFilename . '_converted.' . $targetFormat;
    $outputFilePath = $uploadDir . $uniqueId . '_' . $outputFilename;
    
    // Move uploaded file to temp location
    if (!move_uploaded_file($file['tmp_name'], $tempFilePath)) {
        echo json_encode(['error' => 'Failed to move uploaded file']);
        exit;
    }
    
   if (!copy($tempFilePath, $outputFilePath)) {
        echo json_encode(['error' => 'Conversion failed']);
        exit;
    }
    
    // Clean up temp file
    unlink($tempFilePath);
    
    // Prepare response
    $response = [
        'success' => true,
        'downloadUrl' => 'download.php?file=' . urlencode($uniqueId . '_' . $outputFilename),
        'filename' => $outputFilename
    ];
    
    echo json_encode($response);
    exit;
}

// If not a POST request or no file uploaded
echo json_encode(['error' => 'Invalid request']);
?>

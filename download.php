<?php
// download.php

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers to allow cross-origin requests if needed
header("Access-Control-Allow-Origin: *");

// Check if file parameter exists
if (!isset($_GET['file'])) {
    header("HTTP/1.0 400 Bad Request");
    echo "No file specified";
    exit;
}

$uploadDir = __DIR__ . '/uploads/';
$requestedFile = basename($_GET['file']);
$filePath = $uploadDir . $requestedFile;

// Check if file exists
if (!file_exists($filePath)) {
    header("HTTP/1.0 404 Not Found");
    echo "File not found";
    exit;
}

// Get file info
$filename = pathinfo($filePath, PATHINFO_FILENAME);
$extension = pathinfo($filePath, PATHINFO_EXTENSION);
$filesize = filesize($filePath);
$mimeType = mime_content_type($filePath);

// Set headers for download
header("Content-Type: " . $mimeType);
header("Content-Disposition: attachment; filename=\"" . $filename . '.' . $extension . "\"");
header("Content-Length: " . $filesize);
header("Cache-Control: no-cache, must-revalidate");
header("Expires: 0");

// Clean output buffer and send file
ob_clean();
flush();
readfile($filePath);

// Delete the file after download
unlink($filePath);
exit;
?>
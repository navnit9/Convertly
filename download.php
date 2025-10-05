<?php

header("Access-Control-Allow-Origin: *");

if (!isset($_GET['file'])) {
    header("HTTP/1.0 400 Bad Request");
    echo "No file specified";
    exit;
}

$uploadDir = __DIR__ . '/uploads/';
$requestedFile = basename($_GET['file']);
$filePath = $uploadDir . $requestedFile;

//if file exists
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

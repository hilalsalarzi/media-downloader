<?php
// Handle file download if the 'file' parameter is set in the URL
if (isset($_GET['file'])) {
    $file = $_GET['file']; // File name or path
    $filePath = 'path/to/your/files/' . basename($file); // Ensure safe file path

    // Check if the file exists
    if (file_exists($filePath)) {
        // Set headers for the download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        // Clear output buffer
        flush();

        // Read the file and send it to the browser
        readfile($filePath);
        exit;
    } else {
        echo "File does not exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Downloader</title>
</head>
<body>

    <!-- Download Button -->
    <a href="?file=example.jpg" class="btn btn-primary">Download Image</a>

</body>
</html>

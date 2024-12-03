<?php
// Handle file download if the 'file' parameter is set in the URL
if (isset($_GET['file'])) {
    $file = $_GET['file']; // File name or path
    $filePath = $file; // Since it's an online file, no need to modify the path

    // Check if the file exists remotely
    if (@fopen($filePath, 'r')) { // Check if the file is accessible
        // Set headers for the download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
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
        echo "File does not exist or is inaccessible.";
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
    <a href="?file=https://cloudgivers.com/wp-content/uploads/2024/12/25-in-1-Screwdriver-Set-Multifunctional-Household-Screwdriver-Set-with-Magnetic-Removal-Repair-Tool-Home-Repair-Tool-1-247x296.webp" class="btn btn-primary">Download Image</a>

</body>
</html>

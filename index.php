<?php
// Make sure error reporting is enabled for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to generate a random string for filenames
function generateRandomString($length = 4) {
    return substr(str_shuffle('0123456789'), 0, $length);
}

// Function to download the image from URL and save it locally
function downloadImage($url, $savePath) {
    $imageContent = file_get_contents($url);
    if ($imageContent === false) {
        echo "Failed to download: $url\n";
        return false;
    }
    file_put_contents($savePath, $imageContent);
    return true;
}

// Process the image URLs from JSON input
$urls = [
    "https://sc04.alicdn.com/kf/Hebb858737d7d4f0688bc49a5e85ffcc2f.jpg",
    "https://sc04.alicdn.com/kf/Hc4fa971668964e32ae6ba18a414ba1e8i.jpg",
    "https://sc04.alicdn.com/kf/H50c708c1ed0f4aa480d013ebb0b0e4c3f.jpg",
    "https://sc04.alicdn.com/kf/Hc53d7613c2f640dd8fba87f8a1873a689.jpg",
    "https://sc04.alicdn.com/kf/He1975850dc5340f2aba0ac971b54794cl.jpg",
    "https://sc04.alicdn.com/kf/H521203512fb54b99a8ea3f8288d8b1a6s.jpg"
];

// Prepare folders for saving images and creating ZIP file
$mediaFolder = 'media_downloads';
if (!is_dir($mediaFolder)) {
    mkdir($mediaFolder, 0777, true);
}

// Download the images
foreach ($urls as $url) {
    $fileInfo = pathinfo($url);
    $randomString = generateRandomString();
    $filename = $fileInfo['filename'] . '-' . $randomString . '.' . $fileInfo['extension'];
    $filePath = $mediaFolder . DIRECTORY_SEPARATOR . $filename;

    // Download and save the image
    if (downloadImage($url, $filePath)) {
        echo "Downloaded: $url\n";
    } else {
        echo "Failed to download: $url\n";
    }
}

// Create a ZIP file of the downloaded images
$zip = new ZipArchive();
$zipFileName = 'media_files-' . generateRandomString() . '.zip';
if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    $files = scandir($mediaFolder);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $zip->addFile($mediaFolder . DIRECTORY_SEPARATOR . $file, $file);
        }
    }
    $zip->close();
    echo 'ZIP file created successfully: ' . $zipFileName . "\n";
} else {
    echo 'Failed to create zip file.' . "\n";
    exit;
}

// Serve the ZIP file for download
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
header('Content-Length: ' . filesize($zipFileName));
readfile($zipFileName);

// Clean up temporary files
unlink($zipFileName);
array_map('unlink', glob("$mediaFolder/*.*"));
rmdir($mediaFolder);
exit;
?>

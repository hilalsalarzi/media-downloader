<?php
require 'assets/plugins/composer/vendor/autoload.php'; // Ensure correct path to PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $urls = [];
    $convertToWebP = isset($_POST['convertToWebP']); // Check if the checkbox was selected

    // Get and sanitize custom file name
    $fileName = $_POST['fileName'] ?? 'download';
    $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '', $fileName); // Sanitize file name

    // Process single link input
    if (!empty($_POST['singleLink'])) {
        $urls[] = trim($_POST['singleLink']);
    }

    // Process uploaded file
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $uploadedFile = $_FILES['file']['tmp_name'];

        if ($fileType === 'json') {
            $jsonData = file_get_contents($uploadedFile);
            $urls = array_merge($urls, json_decode($jsonData, true) ?? []);
        } elseif ($fileType === 'xlsx') {
            $spreadsheet = IOFactory::load($uploadedFile);
            $sheet = $spreadsheet->getActiveSheet();
            foreach ($sheet->getRowIterator() as $row) {
                $cell = $row->getCellIterator()->current();
                $urls[] = trim($cell->getValue());
            }
        } elseif ($fileType === 'txt') {
            $txtData = file($uploadedFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $urls = array_merge($urls, array_map('trim', $txtData));
        } else {
            echo 'Unsupported file type.';
            exit;
        }
    }

    // Validate URLs
    $urls = array_filter($urls, fn($url) => filter_var($url, FILTER_VALIDATE_URL));
    if (empty($urls)) {
        echo 'No valid URLs provided.';
        exit;
    }

    function generateRandomString($length = 4) {
        return substr(str_shuffle('0123456789'), 0, $length);
    }

    function convertImageToWebP($sourcePath, $destinationPath, $quality = 80) {
        $info = getimagesize($sourcePath);
        if ($info === false) return false;

        $mime = $info['mime'];
        $image = null;

        // Create an image resource based on MIME type
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                return false; // Unsupported type
        }

        // Save the image as WebP
        $result = imagewebp($image, $destinationPath, $quality);
        imagedestroy($image); // Free memory
        return $result;
    }

    $mediaFolder = 'media_downloads';
    $webpFolder = 'webp_downloads';
    if (!is_dir($mediaFolder)) mkdir($mediaFolder, 0777, true);
    if (!is_dir($webpFolder)) mkdir($webpFolder, 0777, true);

    $counter = 1; // Initialize counter for incremental naming
    foreach ($urls as $url) {
        $fileInfo = pathinfo($url);
        $extension = $fileInfo['extension'];
        $customFileName = $fileName . '-' . $counter . '.' . $extension; // Generate custom name
        $filePath = $mediaFolder . DIRECTORY_SEPARATOR . $customFileName;

        $fileContent = @file_get_contents($url);
        if ($fileContent !== false) {
            file_put_contents($filePath, $fileContent);

            if ($convertToWebP) {
                $webpPath = $webpFolder . DIRECTORY_SEPARATOR . $fileName . '-' . $counter . '.webp';
                if (!convertImageToWebP($filePath, $webpPath)) {
                    echo "Failed to convert $filePath to WebP.<br>";
                }
            }
        } else {
            echo "Failed to download: $url<br>";
        }

        $counter++; // Increment counter for next file
    }

    // Create a ZIP file for downloaded files (either original or WebP)
    $zip = new ZipArchive();
    $zipFileName = $fileName . '-' . generateRandomString() . '.zip';
    $folderToZip = $convertToWebP ? $webpFolder : $mediaFolder;

    if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = scandir($folderToZip);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $zip->addFile($folderToZip . DIRECTORY_SEPARATOR . $file, $file);
            }
        }
        $zip->close();

        // Serve the ZIP file for download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
        header('Content-Length: ' . filesize($zipFileName));
        readfile($zipFileName);

        // Clean up temporary files
        unlink($zipFileName);
        array_map('unlink', glob("$mediaFolder/*.*"));
        array_map('unlink', glob("$webpFolder/*.*"));
        rmdir($mediaFolder);
        rmdir($webpFolder);
        exit;
    } else {
        echo 'Failed to create zip file.';
    }
}
?>

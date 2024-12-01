<?php
// require 'vendor/autoload.php'; // Include for Excel handling via PhpSpreadsheet
require 'assets/plugins/composer/vendor/autoload.php'; // Add semicolon at the end


use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $urls = [];

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

    // Download media files
    $mediaFolder = 'media_downloads';
    if (!is_dir($mediaFolder)) {
        mkdir($mediaFolder, 0777, true);
    }

    foreach ($urls as $url) {
        $fileInfo = pathinfo($url);
        $filename = uniqid() . '_' . $fileInfo['basename'];
        $filePath = $mediaFolder . DIRECTORY_SEPARATOR . $filename;

        $fileContent = file_get_contents($url);
        if ($fileContent !== false) {
            file_put_contents($filePath, $fileContent);
        }
    }

    // Create a zip file
    $zip = new ZipArchive();
    $zipFileName = 'media_files.zip';

    if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = scandir($mediaFolder);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $zip->addFile($mediaFolder . DIRECTORY_SEPARATOR . $file, $file);
            }
        }
        $zip->close();

        // Serve the zip file for download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
        header('Content-Length: ' . filesize($zipFileName));
        readfile($zipFileName);

        // Clean up temporary files
        unlink($zipFileName);
        array_map('unlink', glob("$mediaFolder/*.*"));
        rmdir($mediaFolder);
        exit;
    } else {
        echo 'Failed to create zip file.';
    }
}
?>

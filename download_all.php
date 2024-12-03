<?php
if (isset($_POST['images']) && is_array($_POST['images'])) {
    $images = $_POST['images'];
    $baseName = isset($_GET['baseName']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['baseName']) : 'images';
    $zipFile = $baseName . '.zip';
    

    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        foreach ($images as $image) {
            if (file_exists($image)) {
                $zip->addFile($image, basename($image));
            }
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
        header('Content-Length: ' . filesize($zipFile));
        readfile($zipFile);

        // Clean up the temporary zip file
        unlink($zipFile);
        exit;
    } else {
        echo 'Failed to create ZIP file.';
    }
} else {
    echo 'No images selected for download.';
}
?>

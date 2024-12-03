<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baseName = isset($_POST['baseName']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['baseName']) : 'images';
    $convertTo = isset($_POST['convertTo']) ? $_POST['convertTo'] : [];
    $uploadedFiles = $_FILES['images'];

    // Validate inputs
    if (empty($convertTo)) {
        echo '<p class="text-danger">Please select at least one format to convert to.</p>';
        exit;
    }

    if ($uploadedFiles['error'][0] !== UPLOAD_ERR_OK) {
        echo '<p class="text-danger">Please upload valid images.</p>';
        exit;
    }

    // Create a temporary folder for converted files
    $tempDir = 'temp_' . uniqid();
    mkdir($tempDir);

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $convertedFiles = [];

    foreach ($uploadedFiles['tmp_name'] as $index => $tmpName) {
        $originalName = pathinfo($uploadedFiles['name'][$index], PATHINFO_FILENAME);

        foreach ($convertTo as $format) {
            if (in_array($format, $allowedExtensions)) {
                $newFileName = $tempDir . '/' . $baseName . '_' . ($index + 1) . '.' . $format;
                $image = imagecreatefromstring(file_get_contents($tmpName));

                // Convert image to the selected format
                switch ($format) {
                    case 'jpg':
                    case 'jpeg':
                        imagejpeg($image, $newFileName);
                        break;
                    case 'png':
                        imagepng($image, $newFileName);
                        break;
                    case 'gif':
                        imagegif($image, $newFileName);
                        break;
                    case 'webp':
                        imagewebp($image, $newFileName);
                        break;
                }

                imagedestroy($image);
                $convertedFiles[] = $newFileName;
            }
        }
    }

    // Create ZIP file
    $zipFileName = $baseName . '_converted_images.zip';
    $zip = new ZipArchive();
    if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        foreach ($convertedFiles as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        // Download ZIP file
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
        header('Content-Length: ' . filesize($zipFileName));
        readfile($zipFileName);

        // Clean up temporary files
        array_map('unlink', $convertedFiles);
        rmdir($tempDir);
        unlink($zipFileName);

        exit;
    } else {
        echo '<p class="text-danger">Failed to create ZIP file.</p>';
        rmdir($tempDir);
    }
} else {
    echo '<p class="text-danger">Invalid request.</p>';
}
?>

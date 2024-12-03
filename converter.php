<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Converter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Image Converter</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Select Input Images:</label>
            <input type="file" class="form-control" name="images[]" multiple accept="image/*" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Enter Base File Name:</label>
            <input type="text" class="form-control" name="baseName" placeholder="Enter base file name (e.g., camera)" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Select Output Format:</label>
            <select name="outputExtension" class="form-select" required>
                <option value="jpg">JPG</option>
                <option value="png">PNG</option>
                <option value="jpeg">JPEG</option>
                <option value="gif">GIF</option>
                <option value="webp">WEBP</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100" name="convert">Convert Images</button>
    </form>

    <?php
    if (isset($_POST['convert']) && isset($_FILES['images']) && isset($_POST['baseName'])) {
        $outputExtension = $_POST['outputExtension'];
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['baseName']); // Sanitize input
        $uploadedFiles = $_FILES['images'];

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $convertedDir = 'converted/';

        if (!is_dir($convertedDir)) mkdir($convertedDir, 0777, true);

        $convertedImages = [];
        $counter = 1;

        foreach ($uploadedFiles['name'] as $index => $fileName) {
            $tempPath = $uploadedFiles['tmp_name'][$index];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (in_array($fileExt, $allowedExtensions)) {
                $newFileName = $baseName . '_' . $counter . '.' . $outputExtension;
                $outputPath = $convertedDir . $newFileName;

                $image = null;
                switch ($fileExt) {
                    case 'jpg':
                    case 'jpeg':
                        $image = imagecreatefromjpeg($tempPath);
                        break;
                    case 'png':
                        $image = imagecreatefrompng($tempPath);
                        break;
                    case 'gif':
                        $image = imagecreatefromgif($tempPath);
                        break;
                    case 'webp':
                        $image = imagecreatefromwebp($tempPath);
                        break;
                }

                if ($image) {
                    switch ($outputExtension) {
                        case 'jpg':
                        case 'jpeg':
                            imagejpeg($image, $outputPath);
                            break;
                        case 'png':
                            imagepng($image, $outputPath);
                            break;
                        case 'gif':
                            imagegif($image, $outputPath);
                            break;
                        case 'webp':
                            imagewebp($image, $outputPath);
                            break;
                    }
                    imagedestroy($image);
                    $convertedImages[] = $outputPath;
                    $counter++;
                }
            }
        }

        // Display converted images with checkboxes
        if (!empty($convertedImages)) {
            echo '<form action="" method="POST" class="mt-4">';
            echo '<div class="row">';
            foreach ($convertedImages as $imagePath) {
                echo '<div class="col-md-3 text-center">';
                echo '<img src="' . $imagePath . '" class="img-thumbnail mb-2" alt="Converted Image">';
                echo '<div class="form-check">';
                echo '<input type="checkbox" class="form-check-input" name="selectedImages[]" value="' . $imagePath . '" id="' . $imagePath . '">';
                echo '<label class="form-check-label" for="' . $imagePath . '">Select</label>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '<button type="submit" class="btn btn-success w-100 mt-3" name="download">Download Selected</button>';
            echo '</form>';
        } else {
            echo '<div class="alert alert-warning mt-3">No valid images were uploaded for conversion.</div>';
        }
    }

    // Download selected images as a ZIP file
    if (isset($_POST['download']) && isset($_POST['selectedImages'])) {
        $selectedImages = $_POST['selectedImages'];

        $zip = new ZipArchive();
        $zipFileName = 'downloaded_images.zip';

        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            foreach ($selectedImages as $filePath) {
                $zip->addFile($filePath, basename($filePath));
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
            header('Content-Length: ' . filesize($zipFileName));
            readfile($zipFileName);
            unlink($zipFileName); // Delete ZIP file after download
            exit;
        } else {
            echo '<div class="alert alert-danger mt-3">Failed to create ZIP file.</div>';
        }
    }
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

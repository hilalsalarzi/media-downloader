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

        // Display converted images with checkboxes and download links
        if (!empty($convertedImages)) {
            echo '<div class="mt-4">';
            echo '<h3>Converted Images:</h3>';
            echo '<form id="downloadForm">';
            echo '<div class="mb-3">';
            echo '<input type="checkbox" id="selectAll" class="form-check-input"> <label for="selectAll">Select All</label>';
            echo '</div>';
            echo '<div class="row">';
            foreach ($convertedImages as $imagePath) {
                echo '<div class="col-md-3 text-center">';
                echo '<input type="checkbox" name="images[]" value="' . $imagePath . '" class="form-check-input converted-image-checkbox">';
                echo '<img src="' . $imagePath . '" class="img-thumbnail mb-2" alt="Converted Image">';
                echo '<a href="' . $imagePath . '" class="btn btn-success w-100" download="' . basename($imagePath) . '">Download</a>';
                echo '</div>';
            }
            echo '</div>';
            echo '</form>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning mt-3">No valid images were uploaded for conversion.</div>';
        }
    }
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Select All Checkbox
    document.getElementById('selectAll').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.converted-image-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
</script>
</body>
</html>

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
    <h1 class="text-center">Image Converter</h1>
    <form action="download_all.php" method="POST" enctype="multipart/form-data" id="uploadForm">
        <div class="mb-3">
            <label for="baseName" class="form-label">Base File Name</label>
            <input type="text" class="form-control" id="baseName" name="baseName" placeholder="Enter base name (e.g., camera)" required>
        </div>
        <div class="mb-3">
            <label for="images" class="form-label">Select Images</label>
            <input type="file" class="form-control" id="images" name="images[]" multiple accept=".jpg,.jpeg,.png,.gif,.webp" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Convert To</label><br>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="convertTo[]" value="jpg" id="jpg">
                <label class="form-check-label" for="jpg">JPG</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="convertTo[]" value="png" id="png">
                <label class="form-check-label" for="png">PNG</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="convertTo[]" value="jpeg" id="jpeg">
                <label class="form-check-label" for="jpeg">JPEG</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="convertTo[]" value="gif" id="gif">
                <label class="form-check-label" for="gif">GIF</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="convertTo[]" value="webp" id="webp">
                <label class="form-check-label" for="webp">WEBP</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100">Convert and Download</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

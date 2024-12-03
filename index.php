<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Media Downloader tool that allows users to download images, videos, and other media files from JSON, Excel, and text file links. Easy and fast bulk download solution.">
    <meta name="keywords" content="media downloader, bulk download, download images, download videos, file downloader, data collection, e-commerce media, download tool">
    <meta name="author" content="Hilal Ahmad">
    <title>Media Downloader - Bulk Download Images and Videos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #343a40;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            border-radius: 5px;
            padding: 12px;
        }

        label {
            font-weight: 600;
        }

        p {
            text-align: center;
            margin: 20px 0;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Media Downloader</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Upload File (JSON, Excel, Text):</label>
                <input type="file" name="file" id="file" accept=".json,.xlsx,.txt" class="form-control">
            </div>

            <p>OR</p>

            <div class="form-group">
                <label for="singleLink">Enter a Single URL:</label>
                <input type="url" name="singleLink" id="singleLink" class="form-control" placeholder="https://example.com/image.jpg">
            </div>

            <div class="form-group form-check">
                <input type="checkbox" name="convertToWebP" id="convertToWebP" class="form-check-input">
                <label for="convertToWebP" class="form-check-label">Convert to WebP</label>
            </div>

            <button type="submit" class="btn-submit">Download Media</button>
        </form>
    </div>

    <?php
    require 'assets/plugins/composer/vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $urls = [];
        $convertToWebP = isset($_POST['convertToWebP']);

        if (!empty($_POST['singleLink'])) {
            $urls[] = trim($_POST['singleLink']);
        }

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
                echo '<div class="alert alert-danger">Unsupported file type.</div>';
                exit;
            }
        }

        $urls = array_filter($urls, fn($url) => filter_var($url, FILTER_VALIDATE_URL));
        if (empty($urls)) {
            echo '<div class="alert alert-danger">No valid URLs provided.</div>';
            exit;
        }

        function generateRandomString($length = 4) {
            return substr(str_shuffle('0123456789'), 0, $length);
        }

        function convertImageToWebP($sourcePath, $destinationPath, $quality = 80) {
            $info = getimagesize($sourcePath);
            if ($info === false) return false;

            $mime = $info['mime'];
            switch ($mime) {
                case 'image/jpeg': $image = imagecreatefromjpeg($sourcePath); break;
                case 'image/png':
                    $image = imagecreatefrompng($sourcePath);
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case 'image/gif': $image = imagecreatefromgif($sourcePath); break;
                default: return false;
            }
            $result = imagewebp($image, $destinationPath, $quality);
            imagedestroy($image);
            return $result;
        }

        $mediaFolder = 'media_downloads';
        $webpFolder = 'webp_downloads';
        if (!is_dir($mediaFolder)) mkdir($mediaFolder, 0777, true);
        if (!is_dir($webpFolder)) mkdir($webpFolder, 0777, true);

        foreach ($urls as $url) {
            $fileInfo = pathinfo($url);
            $randomString = generateRandomString();
            $filename = $fileInfo['filename'] . '-' . $randomString . '.' . $fileInfo['extension'];
            $filePath = $mediaFolder . DIRECTORY_SEPARATOR . $filename;

            $fileContent = file_get_contents($url);
            if ($fileContent !== false) {
                file_put_contents($filePath, $fileContent);
                if ($convertToWebP) {
                    $webpPath = $webpFolder . DIRECTORY_SEPARATOR . $fileInfo['filename'] . '-' . $randomString . '.webp';
                    if (!convertImageToWebP($filePath, $webpPath)) {
                        echo "<div class='alert alert-warning'>Failed to convert $filePath to WebP.</div>";
                    }
                }
            }
        }

        $zip = new ZipArchive();
        $zipFileName = 'media_files-' . generateRandomString() . '.zip';
        $folderToZip = $convertToWebP ? $webpFolder : $mediaFolder;

        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = scandir($folderToZip);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $zip->addFile($folderToZip . DIRECTORY_SEPARATOR . $file, $file);
                }
            }
            $zip->close();

            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
            header('Content-Length: ' . filesize($zipFileName));
            readfile($zipFileName);

            unlink($zipFileName);
            array_map('unlink', glob("$mediaFolder/*.*"));
            array_map('unlink', glob("$webpFolder/*.*"));
            rmdir($mediaFolder);
            rmdir($webpFolder);
            exit;
        } else {
            echo '<div class="alert alert-danger">Failed to create zip file.</div>';
        }
    }
    ?>
</body>

</html>

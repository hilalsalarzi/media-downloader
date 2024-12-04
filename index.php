<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <h1 style="display: flex; align-items: center; gap: 10px;">
        Media Downloader
        <a href="converter.php" style="text-decoration: none; color: blue; font-size: 0.8em;">
            Go To Converter
        </a>
    </h1>
        <form action="config.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Upload File (JSON, Excel, Text):</label>
                <input type="file" name="file" id="file" accept=".json,.xlsx,.txt" class="form-control">
            </div>

            <p>OR</p>

            <div class="form-group">
                <label for="textareaLinks">Paste URLs (JSON Array):</label>
                <textarea name="textareaLinks" id="textareaLinks" rows="5" class="form-control" 
                    placeholder='[ "https://example.com/image1.jpg", "https://example.com/image2.jpg" ]'></textarea>
            </div>

            <p>OR</p>

            <div class="form-group">
                <label for="singleLink">Enter a Single URL:</label>
                <input type="url" name="singleLink" id="singleLink" class="form-control" placeholder="https://example.com/image.jpg">
            </div>

            <div class="form-group">
                <label for="fileName">File Name (Optional):</label>
                <input type="text" name="fileName" id="fileName" class="form-control" placeholder="Enter file name (e.g., my-download)">
            </div>

            <div class="form-group form-check">
                <input type="checkbox" name="convertToWebP" id="convertToWebP" class="form-check-input">
                <label for="convertToWebP" class="form-check-label">Convert to WebP</label>
            </div>

            <button type="submit" class="btn-submit">Download Media</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>

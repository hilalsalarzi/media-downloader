<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Media Downloader tool that allows users to download images, videos, and other media files from JSON, Excel, and text file links. Easy and fast bulk download solution.">
    <meta name="keywords" content="media downloader, bulk download, download images, download videos, file downloader, data collection, e-commerce media, download tool">
    <meta name="author" content="Hilal Ahmad">
    <meta property="og:title" content="Media Downloader">
    <meta property="og:description" content="Media Downloader tool that helps you download images, videos, and other media files from URLs provided in JSON, Excel, or text files.">
    <meta property="og:url" content="http://downloader.onicbyte.com">
    <meta property="og:image" content="path_to_image.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Media Downloader">
    <meta name="twitter:description" content="Download media files (images, videos) from various file formats like JSON, Excel, and Text with ease.">
    <meta name="twitter:image" content="path_to_image.jpg">

    <title>Media Downloader - Bulk Download Images and Videos</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles */
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
    <!-- Structured data for SEO -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "Media Downloader",
        "url": "http://downloader.onicbyte.com",
        "description": "Download media files such as images and videos from JSON, Excel, or Text file URLs.",
        "author": {
            "@type": "Person",
            "name": "Hilal Ahmad"
        },
        "operatingSystem": "Web",
        "applicationCategory": "File Downloading Tool"
    }
    </script>
</head>

<body>
    <div class="container">
        <h1>Media Downloader</h1>
        <form action="config.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Upload File (JSON, Excel, Text):</label>
                <input type="file" name="file" id="file" accept=".json,.xlsx,.txt" class="form-control">
            </div>

            <p>OR</p>

            <div class="form-group">
                <label for="singleLink">Enter a Single URL:</label>
                <input type="url" name="singleLink" id="singleLink" class="form-control" placeholder="https://example.com/image.jpg">
            </div>

            <button type="submit" class="btn-submit">Download Media</button>
        </form>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>

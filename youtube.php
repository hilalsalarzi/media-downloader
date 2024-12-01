<?php
// Check if a URL is provided in the POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['url'])) {
    // Sanitize the URL input to prevent any command injection
    $url = escapeshellarg($_POST['url']);

    // Path to the yt-dlp executable (make sure this is the correct path)
    $ytDlpPath = 'yt-dlp'; // Or 'yt-dlp.exe' if you're using Windows

    // Construct the command to download the media
    $command = "$ytDlpPath -f bestvideo+bestaudio $url"; // Use the best video and audio formats

    // Execute the command and capture the output and error
    $output = shell_exec($command . ' 2>&1'); // Capture both stdout and stderr

    // Show the result to the user
    echo "<div style='margin-top: 20px;'>";
    echo "<h3>Download Started</h3>";
    echo "<p><strong>URL:</strong> " . $_POST['url'] . "</p>";
    echo "<p><strong>Output:</strong></p>";
    echo "<pre>$output</pre>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Downloader</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .result-container {
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <h1>Media Downloader</h1>

    <!-- Form to accept video URL -->
    <div class="form-container">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="url" class="form-label">Enter Video URL:</label>
                <input type="url" name="url" id="url" class="form-control" placeholder="https://youtube.com/video" required>
            </div>
            <button type="submit" class="btn btn-primary">Download Media</button>
        </form>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['url'])): ?>
        <!-- Displaying the output after download starts -->
        <div class="result-container">
            <h4>Download Details</h4>
            <p><strong>URL:</strong> <?php echo $_POST['url']; ?></p>
            <p><strong>Output:</strong></p>
            <pre><?php echo $output; ?></pre>
        </div>
    <?php endif; ?>

    <!-- Bootstrap 5 JS and Popper.js (for Bootstrap components) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>

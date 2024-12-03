<?php
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get file name input
    $fileName = $_POST['fileName'] ?? 'download';
    $fileName = preg_replace('/[^a-zA-Z0-9_-]/', '', $fileName); // Sanitize file name

    // Set upload directory
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['file'];
        $fileTempPath = $uploadedFile['tmp_name'];
        $fileOriginalName = $uploadedFile['name'];
        $fileExtension = pathinfo($fileOriginalName, PATHINFO_EXTENSION);

        // Move the uploaded file to the uploads directory
        $uploadedFilePath = $uploadDir . basename($fileOriginalName);
        if (move_uploaded_file($fileTempPath, $uploadedFilePath)) {
            echo "File uploaded successfully: " . $fileOriginalName . "<br>";

            // Process the uploaded file (e.g., extract URLs from JSON/Excel/Text)
            processFile($uploadedFilePath, $fileName);
        } else {
            echo "Error moving uploaded file.";
        }
    }

    // Handle single link input
    if (!empty($_POST['singleLink'])) {
        $singleLink = $_POST['singleLink'];
        echo "Processing single link: " . htmlspecialchars($singleLink) . "<br>";

        // Download the single link
        downloadMedia($singleLink, $fileName);
    }

    // Handle WebP conversion option
    if (isset($_POST['convertToWebP'])) {
        echo "WebP conversion option selected.<br>";
        // Add WebP conversion logic here
    }
} else {
    echo "Invalid request.";
}

// Function to process the uploaded file and download media
function processFile($filePath, $fileName)
{
    $urls = [];

    // Extract URLs based on file type
    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
    if ($fileExtension === 'json') {
        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);
        if (is_array($data)) {
            $urls = array_merge($urls, $data); // Assuming JSON is an array of URLs
        }
    } elseif ($fileExtension === 'xlsx') {
        require '/assets/plugins/composer/vendor/autoload.php'; // Load PHPExcel or similar library
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        foreach ($worksheet->getRowIterator() as $row) {
            $cell = $row->getCellIterator()->current();
            if ($cell) {
                $urls[] = $cell->getValue();
            }
        }
    } elseif ($fileExtension === 'txt') {
        $urls = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    echo "Found " . count($urls) . " URLs in the file.<br>";

    // Download each URL
    foreach ($urls as $url) {
        downloadMedia($url, $fileName);
    }
}

// Function to download a media file
function downloadMedia($url, $fileName)
{
    $mediaDir = 'downloads/';
    if (!is_dir($mediaDir)) {
        mkdir($mediaDir, 0777, true);
    }

    $filePath = $mediaDir . $fileName . '-' . basename($url);
    $fileContent = file_get_contents($url);

    if ($fileContent !== false) {
        file_put_contents($filePath, $fileContent);
        echo "Downloaded: " . htmlspecialchars($url) . " as " . basename($filePath) . "<br>";
    } else {
        echo "Failed to download: " . htmlspecialchars($url) . "<br>";
    }
}
?>

# Media Downloader

A versatile PHP application to automate the downloading and organizing of media files (images, videos, etc.) from multiple URLs. This tool supports different input formats like JSON, Excel (XLSX), and TXT files, making it suitable for various use cases, including e-commerce platforms, data analysis, data collection, and more.

## Features
- **Multi-format support**: Upload media links from JSON, Excel (XLSX), or TXT files.
- **Automatic download**: Downloads media files (images, videos, etc.) from the provided URLs.
- **File organization**: Media files are saved locally and bundled into a ZIP archive for easy download.
- **Error handling**: Automatically skips invalid URLs and handles errors gracefully.
- **Simple user interface**: Easy to upload files or input individual media links.

## Use Cases
- **E-commerce**: Bulk download product images and videos for your online store.
- **Data Analysis**: Collect and organize media files for research and analysis.
- **Web Scraping**: Automatically download assets such as images and videos from websites.
- **Content Creation**: Gather media for content production or archiving.

## How to Use

1. **Upload a file**: Upload a JSON, Excel (XLSX), or TXT file containing a list of media URLs.
2. **Manual input**: Alternatively, enter a single URL for a quick download.
3. **Download media**: The tool will fetch the media files, save them locally, and package them into a ZIP file for easy download.
4. **Download the ZIP file**: After the process is complete, the ZIP file will be ready for download.

## Requirements
- PHP 7.4 or higher
- `ZipArchive` PHP extension enabled

## Installation

1. **Clone the repository**: 
   ```bash
   git clone https://github.com/hilalsalarzi/media-downloader.git

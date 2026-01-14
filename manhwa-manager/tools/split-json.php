<?php
/**
 * JSON Splitter Tool
 * 
 * Script ini membantu split file JSON besar menjadi beberapa file kecil
 * untuk memudahkan proses import ke WordPress
 * 
 * Usage:
 * php split-json.php input.json 100
 * 
 * Parameter:
 * - input.json: File JSON yang akan di-split
 * - 100: Jumlah items per file (default: 100)
 */

// Check if running from command line
if (php_sapi_name() !== 'cli') {
    die("This script must be run from command line.\n");
}

// Get arguments
$input_file = $argv[1] ?? null;
$items_per_file = isset($argv[2]) ? intval($argv[2]) : 100;

if (!$input_file) {
    echo "Usage: php split-json.php <input-file.json> [items-per-file]\n";
    echo "Example: php split-json.php metadata.json 100\n";
    exit(1);
}

if (!file_exists($input_file)) {
    echo "Error: File '{$input_file}' not found.\n";
    exit(1);
}

echo "Reading file: {$input_file}\n";

// Read and parse JSON
$json_content = file_get_contents($input_file);
$data = json_decode($json_content, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error: Invalid JSON - " . json_last_error_msg() . "\n";
    exit(1);
}

// Detect format
$items = array();
if (isset($data['manhwa']) && is_array($data['manhwa'])) {
    $items = $data['manhwa'];
    echo "Format: Standard (with 'manhwa' wrapper)\n";
} elseif (is_array($data) && isset($data[0])) {
    $items = $data;
    echo "Format: Direct array\n";
} else {
    echo "Error: Unknown JSON format\n";
    exit(1);
}

$total_items = count($items);
echo "Total items: {$total_items}\n";
echo "Items per file: {$items_per_file}\n";

// Calculate number of files
$num_files = ceil($total_items / $items_per_file);
echo "Will create {$num_files} files\n\n";

// Create output directory
$output_dir = pathinfo($input_file, PATHINFO_DIRNAME) . '/split';
if (!is_dir($output_dir)) {
    mkdir($output_dir, 0755, true);
}

// Split and save
$file_number = 1;
$chunks = array_chunk($items, $items_per_file);

foreach ($chunks as $chunk) {
    $output_file = $output_dir . '/' . pathinfo($input_file, PATHINFO_FILENAME) . '_part' . $file_number . '.json';
    
    // Save as direct array (compatible with plugin)
    $json_output = json_encode($chunk, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    file_put_contents($output_file, $json_output);
    
    $chunk_size = count($chunk);
    $file_size = filesize($output_file);
    $file_size_mb = round($file_size / 1024 / 1024, 2);
    
    echo "Created: {$output_file} ({$chunk_size} items, {$file_size_mb} MB)\n";
    
    $file_number++;
}

echo "\n✓ Done! Files saved to: {$output_dir}\n";
echo "\nYou can now upload these files one by one to WordPress.\n";

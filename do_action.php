<?php
echo "Current dir: " . __DIR__ . "\n";

$targetFile = '/home/kwkrbcce/public_html/public/assets/logo.png';
$sourceFile = '/home/kwkrbcce/public_html/public/logo.png';

echo "\nTarget File exists? " . (file_exists($targetFile) ? 'YES' : 'NO') . "\n";
if (file_exists($targetFile)) {
    echo "Target File permissions: " . substr(sprintf('%o', fileperms($targetFile)), -4) . "\n";
    echo "Target File size: " . filesize($targetFile) . "\n";
}

echo "\nSource File exists? " . (file_exists($sourceFile) ? 'YES' : 'NO') . "\n";
if (file_exists($sourceFile)) {
    echo "Source File size: " . filesize($sourceFile) . "\n";
}

echo "\nListing /home/kwkrbcce/public_html/public/:\n";
print_r(scandir('/home/kwkrbcce/public_html/public/'));

echo "\nListing /home/kwkrbcce/public_html/public/assets/:\n";
print_r(scandir('/home/kwkrbcce/public_html/public/assets/'));

if (file_exists($targetFile)) {
    echo "\nAttempting to delete Target File...\n";
    $deleted = unlink($targetFile);
    echo "Deleted? " . ($deleted ? 'YES' : 'NO') . "\n";
}

if (file_exists($sourceFile) && (!file_exists($targetFile) || $deleted)) {
    echo "\nAttempting to copy Source File to Target File...\n";
    $copied = copy($sourceFile, $targetFile);
    echo "Copied? " . ($copied ? 'YES' : 'NO') . "\n";
}
?>

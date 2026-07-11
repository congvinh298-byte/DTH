<?php
$targetFile = '/home/kwkrbcce/public_html/public/assets/logo.png';
$sourceFiles = [
    '/home/kwkrbcce/public_html/public/logo.png',
    __DIR__ . '/logo.png',
    __DIR__ . '/public/assets/logo.png'
];

$sourceFile = null;
foreach ($sourceFiles as $sf) {
    if (file_exists($sf)) {
        $sourceFile = $sf;
        break;
    }
}

echo "<h2>Fixing Stubborn Logo</h2>";

if (file_exists($targetFile)) {
    echo "Target file exists. Attempting to fix directory permissions...<br>";
    $dir = dirname($targetFile);
    @chmod($dir, 0755); // MUST have write permissions on directory to delete files!
    
    echo "Attempting chmod on file...<br>";
    @chmod($targetFile, 0666);
    
    echo "Attempting to delete target... ";
    if (@unlink($targetFile)) {
        echo "<strong style='color:green'>DELETED OK</strong><br>";
    } else {
        echo "<strong style='color:red'>DELETE FAILED</strong><br>";
        echo "Attempting to rename it out of the way... ";
        if (@rename($targetFile, $targetFile . '.bak.' . time())) {
            echo "<strong style='color:green'>RENAMED OK</strong><br>";
        } else {
            echo "<strong style='color:red'>RENAME FAILED</strong>. The file is completely locked by the server (possibly root ownership or immutable flag). You will need to contact Vinahost support to unlock it.<br>";
        }
    }
} else {
    echo "Target file does not exist (already deleted?).<br>";
}

if ($sourceFile) {
    echo "Copying source ($sourceFile) to target... ";
    if (@copy($sourceFile, $targetFile)) {
        echo "<strong style='color:green'>COPIED OK</strong><br>";
        @chmod($targetFile, 0644);
    } else {
        echo "<strong style='color:red'>COPY FAILED</strong><br>";
    }
} else {
    echo "Source file does not exist in any of the checked paths!<br>";
}
?>

<?php

$pdfRootFolder = fgets(fopen('config/pdfRootFolder.txt', 'r'));

$basename = isset($_GET['file']) ? $_GET['file'] : 'error';
$folder = isset($_GET['pid']) ? $_GET['pid'] : 'error';

$file = $pdfRootFolder . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $basename;

if (file_exists($file)) {
  header('Content-Type: application/pdf');
  header('Content-Disposition: inline; filename="' .$basename . '"');
  header('Content-Length: ' . filesize($file));
  readfile($file);
  exit;
} else {
  echo "File not found";
}
?>

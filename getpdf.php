<?php

/** Logger */
require_once("./logger.php");
$log = Logger::getInstance();

$pdfRootFolder = fgets(fopen('config/pdfRootFolder.txt', 'r'));

$basename = isset($_GET['file']) ? $_GET['file'] : 'error';
$folder = isset($_GET['pid']) ? $_GET['pid'] : 'error';
$type = isset($_GET['type']) ? $_GET['type'] : 'out';

$file = $pdfRootFolder . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $basename;

if (file_exists($file)) {
  header('Content-Type: application/pdf');
  header('Content-Disposition: inline; filename="' .$basename . '"');
  header('Content-Length: ' . filesize($file));
  readfile($file);
  exit;
} else {
  $log->error('file not found: ' . $file);
  echo "File not found";
}
?>

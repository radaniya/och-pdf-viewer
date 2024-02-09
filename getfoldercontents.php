<?php

/** Logger */
require_once("./logger.php");
$log = Logger::getInstance();

$pdfRootFolder = fgets(fopen('config/pdfRootFolder.txt', 'r'));

function getFolderContents($foldername, $outOrAdm) {
  global $log;
  global $pdfRootFolder;
  $pdfFolder = $pdfRootFolder . DIRECTORY_SEPARATOR . $outOrAdm . DIRECTORY_SEPARATOR . $foldername;
  /** folder check */
  if (!is_dir($pdfFolder)) {
    $log->warn('No folder. pid: ' . $foldername . ', out/adm: ' . $outOrAdm);
    return array();
  }
  $pdfFiles = glob($pdfFolder . DIRECTORY_SEPARATOR . "*.pdf");
  return $pdfFiles;
}

<?php

/** Logger */
require_once("./logger.php");
$log = Logger::getInstance();

/** load Categories info */
$categories_json = file_get_contents('config/categories.json');
$categories = json_decode($categories_json, true);

function getCategory($categoryNum) {
  global $categories;
  global $log;
  if ($categoryNum >= count($categories)) {
    $log->warn('Unknown category num: ' . $categoryNum);
    return '不明';
  }
  return $categories[$categoryNum];
}

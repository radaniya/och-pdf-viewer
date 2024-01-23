<?php

/** Logger */
require_once("./logger.php");
$log = Logger::getInstance();

/** admin user name */
$adminUser = 'admin';

/** load User info */
$users_json = file_get_contents('config/users.json');
$users = json_decode($users_json, true);

function isValidUserOrDie($user, $password) {
  global $adminUser;
  global $users;
  global $log;
  if ($user !== $adminUser) {
    if (!isset($users[$user])) {
      $log->error('Invalid user: ' . $user);
      header("Location: errorpage/401.html");
      exit();
    }
    if ($users[$user] !== $password) {
      $log->error('Password error, user: ' . $user . ', password: ' . $password);
      header("Location: errorpage/401.html");
      exit();
    }
  }
}

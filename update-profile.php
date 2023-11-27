<?php
/*******************************************************************************
 * Improve the code in this file.
 *
 * It must let the currently authenticated user update their name, email, and
 * password.
 *
 * Password should be updated only if the current password is provided correctly.
 *
 * `users` table structure:
 *
 * +----+--------+---------------------+----------+------+------------------+
 * | id | name   | email               | password | role | remember_token   |
 * +----+--------+---------------------+----------+------+------------------+
 ******************************************************************************/

namespace App;

require_once('vendor/autoload.php');
session_start();

// Log the user automatically for this demo - this is ok
$_SESSION['user_id'] = 5;

$db     = new Db;
$result = $db->query('SELECT * FROM `users` WHERE `id` = ?', [$_SESSION['user_id']]);
$user   = $result->fetch_assoc();

$data = $_POST;
$updates = "";

if (!empty($data['password']) && (!isset($data['current_password']) || !password_verify($data['current_password'], $user['password']))) {
    // do not let the user update password
    die('{ "status": "error", "message": "Current password is incorrect" }');
}

// encrypt the password
if (isset($data['password'])) {
    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
}

unset($data['current_password']);

if (empty($data['name'])) {
    die('{ "status": "error", "message": "Name cannot be empty" }');
}

if (empty($data['email'])) {
    die('{ "status": "error", "message": "Email cannot be empty" }');
}

if (!preg_match('/^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/', $data['email'])) {
    die('{ "status": "error", "message": "Email is invalid" }');
}

$countFields = 0;
foreach ($data as $field => $value) {
    $updates .= "$field = '$value'";
    $countFields++;
    if ($countFields < count($data)) {
      $updates .= ", ";
    }
}

$sql = "UPDATE users SET $updates WHERE id = {$user['id']}";

$db->query($sql);

echo '{ "status": "ok", "message": "Profile is updated!" }';

public function handle($user_id) {
  $db     = new Db;
  try {
    $result = $db->query('SELECT * FROM `users` WHERE `id` = ?', [$_SESSION['user_id']]);
    $user   = $result->fetch_assoc();
  } catch (\Exception $e) {
    // Log this
    echo '{ "status": " not ok", "message": "DB Error. Please check logs" }';
  }

  $data = validateUpdateProfileRequest($_POST);



  echo '{ "status": "ok", "message": "Profile is updated!" }';
  
}

function isPasswordUpdateRequest($old, $new) {
  if (isset($data['password']) && isset($data['current_password'])) {
    return false;
  }

  if (empty($data['password']) || empty($data['current_password'])) {
    return false;
  }
}

function validateUpdateProfileRequest($data) {
  if (isPasswordUpdateRequest($data['current_password'], $data['password'])) {
    if (!password_verify($data['current_password'], $user['password'])) {
      die('{ "status": "error", "message": "Current password is incorrect" }');
    }

    $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
    unset($data['current_password']);
  }

  if (empty($data['name'])) {
      die('{ "status": "error", "message": "Name cannot be empty" }');
  }

  if (empty($data['email'])) {
      die('{ "status": "error", "message": "Email cannot be empty" }');
  }

  if (!preg_match('/^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/', $data['email'])) {
      die('{ "status": "error", "message": "Email is invalid" }');
  }

  return $data;
}

function updateUserProfile($data) {
  try {
    // implode or explode
    $countFields = 0;
    foreach ($data as $field => $value) {
        $updates .= "$field = '$value'";
        $countFields++;
        if ($countFields < count($data)) {
          $updates .= ", ";
        }
    }

    $sql = "UPDATE users SET ? WHERE id = ?";

    $db->query($sql, $updates, $user_id);
  } catch (\Exception $e) {
    // Log this
    echo '{ "status": " not ok", "message": "DB Error. Please check logs" }';
  }
  
}
  

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

function validateInput(array $data): void
{
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
}

function fetchUserById(Db $db, int $id): array
  {
    $result = $db->query('SELECT * FROM `users` WHERE `id` = ?', [$id]);
    return $result->fetch_assoc();
  }

function updateProfile(Db $db, array $user, array $data)
  {
    $user = fetchUserById($db, $_SESSION['user_id'])

    $data = $_POST;
    $updates = "";

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
  }

function renderResponse(string $status, string $message)
  {
    echo json_encode([
       "status" => $status, 
       "message" => $message
    ])
  }

try {
  validateInput($data);

  $db = new Db();

  $user = fetchUser($db, $_SESSION['user_id']);

  updateProfile($db, $user, $data);

  echo renderResponse('ok', "Profile is updated!")
} catch (ApplicationException $e) {
  echo renderResponse('error', $e->getMessage());
}

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

$data = $_POST;


class Profile {

  protected $userId;
  protected $data;
  protected $userModel;
  protected $validateErrors = [];
  
  public function update($data) 
  {
    $this->data = $data;
    
    if (!$this->validate()) {
      return false;
    }


    if (!empty($this->data['password'])) {
      $this->updatePassword();
    }
  
    $this->updateUserData();
  }

  public function getErrors()
  {
    return $this->validateErrors;
  }

  public function __construct($userId)
  {
    $this->userId = $userId;
    $this->userModel = $this->getUser();

    if (empty($this->userModel)) {
      $this->validateErrors[] = 'User not found';
    }
  }

  protected function getUser()
  {
    $db     = new Db;
    $result = $db->query('SELECT * FROM `users` WHERE `id` = ?', [$_SESSION['user_id']]);
    $user   = $result->fetch_assoc();

    return $user;
  }

  protected function updatePassword() 
  {
    if (isset($this->data['password'])) {
      $this->data['password'] = password_hash($this->data['password'], PASSWORD_BCRYPT);
    }

    unset($this->data['current_password']);

    $db->query("UPDATE users SET password = ? WHERE id = ?", $this->data['password'], $this->userId);
  }

  protected function updateUserData() 
  {
    foreach ($this->data as $field => $value) {
      $db->query("UPDATE users SET ? = ? WHERE id = ?", $field, $value, $this->userId);
    }
  }

  protected function validate()
  {
    if (!empty($data['password']) && (!isset($data['current_password']) || !password_verify($data['current_password'], $user['password']))) {
        // do not let the user update password
      $this->validateErrors[] = '{ "status": "error", "message": "Current password is incorrect" }';
    }
    
    if (empty($data['name'])) {
      $this->validateErrors[] = '{ "status": "error", "message": "Name cannot be empty" }';
    }

    if (empty($data['email'])) {
      $this->validateErrors[] = '{ "status": "error", "message": "Email cannot be empty" }';
    }

    if (!preg_match('/^[\w\.-]+@[a-zA-Z\d\.-]+\.[a-zA-Z]{2,}$/', $data['email'])) {
      $this->validateErrors[] = '{ "status": "error", "message": "Email is invalid" }';
    }
  }

  return empty($this->validateErrors);
}


$profile = new Profile($_SESSION['user_id']);

$success = $profile->update($data);

if ($success) {
  echo '{ "status": "ok", "message": "Profile is updated!" }';
} else {
  print_r($profile->getErrors());
}
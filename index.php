<?php

namespace App;

require 'vendor/autoload.php';
(new Session)->start();

$db     = new Db;
$result = $db->query('SELECT * FROM `users` WHERE `id` = ?', [$_SESSION['user_id']]);
$user   = $result->fetch_assoc();

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update profile</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">

</head>
<body>

  <div class="container mb-5">

  <h1>Hello, <?php echo $user['name']; ?></h1>

  <p>Here you can update your profile.</p>

 <div class="alert-container"></div>
    
      <form action="update-profile.php" method="post" id="profile-form" onsubmit="updateProfile(event)">
      <div class="form-group mb-3">
        <label for="currentName" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" value="<?php echo $user['name']; ?>" required>
      </div>
      <div class="form-group mb-3">
        <label for="newName" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="<?php echo $user['email']; ?>"  required>
      </div>
      <div class="form-group mb-3">
        <label for="currentPassword" class="form-label">Current Password</label>
        <input type="password" class="form-control" name="current_password" id="currentPassword" placeholder="Enter current password">
      </div>
      <div class="form-group mb-3">
        <label for="newPassword" class="form-label">New Password</label>
        <input type="password" class="form-control" name="password" id="newPassword" placeholder="Enter new password">
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>

  </div>

  <script>
    function updateProfile(e) {
      e.preventDefault();

      fetch('/update-profile.php', {
        method: 'post',
        credentials: 'include',
        body: new FormData(document.getElementById('profile-form')),
      }).then(r => r.json())
        .then(j => {
          if(j.status === 'error') {
            displayErrorMessage(j.message);
          } else {
            displaySuccessMessage(j.message)
          }
        } )

      return false;
    }

    function displayErrorMessage(message) {
      document.querySelector('.alert-container').innerHTML = `<div class="alert alert-danger" role="alert">${message}</div>`;
    }

    function displaySuccessMessage(message) {
      document.querySelector('.alert-container').innerHTML = `<div class="alert alert-success" role="alert">${message}</div>`;
    }
  </script>
</body>
</html>

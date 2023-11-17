<?php

use App\Db;

require 'vendor/autoload.php';
session_start();

// Log the user in automatically for this demo
$_SESSION['user_id'] = 5;

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
        headers: { 'content-type': 'application/json' },
        credentials: 'include',
        body: new FormData(document.getElementById('profile-form')),
      }).then(r => r.json())
        .then(console.log)

      return false;
    }
  </script>
</body>
</html>

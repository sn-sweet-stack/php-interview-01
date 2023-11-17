<?php

use App\Db;

require 'vendor/autoload.php';
session_start();

// Log the user in automatically for this demo
$_SESSION['user_id'] = 5;

$host     = getenv('DB_HOST');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_DATABASE');

$db = new Db($host, $username, $password, $database);
$result = $db->query('SELECT * FROM `users` WHERE `id` = ?', [$_SESSION['user_id']]);
$user   = $result->fetch_assoc();

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update profile</title>
</head>
<body>

<h1>Hello, <?php echo $user['name']; ?></h1>

</body>
</html>

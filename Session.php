<?php
namespace App;

class Session
{
    public function start(): void
    { 
      session_start();

      // Log the user automatically for this demo - this is ok
      $_SESSION['user_id'] = 5;
    }

}
<?php

if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user_id'])) {
  $account =
    '<div class="account">'.
      '<a href="./src/actions/logout.php">Logout</a>'.
    '</div>';
} else {
  $account =
    '<div class="account">'.
      '<a href="./login.php">Login</a>'.
    '</div>';
}

echo '<nav class="navbar">'.
      '<div class="links">'.
        '<a href="./leaders.php">Battle</a>'.
        '<a href="./teams.php">Teams</a>'.
      '</div>'.
      $account.
    '</nav>';

<?php

if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user_id'])) {
  $account =
    '<div class="account">'.
      '<a href="./src/actions/logout">Logout</a>'.
    '</div>';
} else {
  $account =
    '<div class="account">'.
      '<a href="./login">Login</a>'.
    '</div>';
}

echo '<nav class="navbar">'.
      '<div class="links">'.
        '<a href="./leaders">Battle</a>'.
        '<a href="./teams">Teams</a>'.
      '</div>'.
      $account.
    '</nav>';

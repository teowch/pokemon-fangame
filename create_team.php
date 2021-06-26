<?php

session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: ./login');
  exit;
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Create Team</title>

  <link href="./assets/images/favicon.png" rel="shortcut icon">

  <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <script type="module" src="<?php echo basename(__DIR__); ?>/../assets/scripts/team/index.js">window.GLOBAL_CONSTANT = <?php echo $_SESSION['user_id']; ?>;</script>

  <link href="./assets/styles/global.css" rel="stylesheet">
</head>

<body>
  <?php include './components/navbar.php'; ?>

  <div class="container create-team">
    <div class="team-name">
      <input type="text" name="team-name" placeholder="Team name" autocomplete="off">
    </div>
    <div class="tab-nav">
      <a class="tab new">
        <i class="material-icons">add</i>
      </a>
      <a class="tab empty"></a>
      <a class="tab empty"></a>
      <a class="tab empty"></a>
      <a class="tab empty"></a>
      <a class="tab empty"></a>
    </div>

    <div class="tab-content"></div>

    <div class="submit">
      <button type="button" class="submit-button" id="submit-team">Submit</button>
    </div>
  </div>
</body>

</html>

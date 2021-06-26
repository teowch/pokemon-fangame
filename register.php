<?php
    session_start();

    if (isset($_SESSION['user_id'])) {
        header('Location: ./');
        exit;
    }
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register</title>

    <link href="./assets/images/favicon.png" rel="shortcut icon">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link href="./assets/styles/global.css" rel="stylesheet">
    <link href="./assets/styles/login_register.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</head>

<body>
    <div class="wrapper">
        <form class="form_register" method="post" action="./src/actions/register.php">
            <div class="title">
                <h1>Register</h1>
            </div>
            <hr class="divider">
            <div class="input_block">
                <label for="username">Username</label>
                <input name="username" id="username" type="text">
            </div>
            <div class="input_block">
                <label for="email">Email</label>
                <input name="email" id="email" type="email">
            </div>
            <div class="input_block">
                <label for="password">Password</label>
                <input name="password" id="password" type="password">
            </div>
            <div class="input_block">
                <label for="passwordConfirm">Confirm password</label>
                <input name="passwordConfirm" id="passwordConfirm" type="password">
            </div>
            <div class="submit">
                <button type="submit" class="button_submit">
                    <i class="material-icons">arrow_forward</i>
                </button>
            </div>
        </form>
        <div class="redirect-login">
          <span>Already have an account? <a href="./login">Log in</a>.</span>
        </div>
    </div>
    <script>
      $(document).ready(() => {
        $('.form_register').on('submit', e => {
          e.preventDefault();

          $.ajax({
            url: './src/actions/register.php',
            method: 'post',
            data: {
              username: e.target.username.value,
              email: e.target.email.value,
              password: e.target.password.value,
              passwordConfirm: e.target.passwordConfirm.value
            },
            success: data => {
              if (data.status >= 400 && data.status < 600) {
                alert(data.error);
              }

              if (data.status === 200) {
                $('body').append(`<a href="<?= basename(__DIR__) ?>/../login" style="display: none" id="registerAnchor"></a>`);

                $('#registerAnchor')[0].click();
              }
            },
            error: data => {
              console.error(data);
            }
          })
        });
      });
    </script>
</body>

</html>

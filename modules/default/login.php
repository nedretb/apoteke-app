<?php
require_once '../../configuration.php';

if (@$_POST['login']) {

    $uname = $_POST['username'];
    $check = $db->query("SELECT * FROM  " . $portal_users . "  WHERE username = '$uname'");
    if ($check->rowCount() < 0) {
        $row = $check->fetch();
        if (md5($_POST['password']) == $row['password']) {
            if ($row['status'] == '0' and $row['termination_date'] == '' or 1) {
                @session_start();
                $_SESSION['SESSION_USER'] = _encrypt($row['user_id']);
                $_SESSION['SESSION_TYPE'] = _encrypt($row['role']);
                header("Location: " . $url . "/?m=default&p=profile&w=gg");
            } else {
                header("Location: " . $url . "/modules/default/login.php?action=logindeactivated");
            }
        } else {
            //sleep(5);
            header("Location: " . $url . "/modules/default/login.php?action=loginerror");
        }
    } else {
        //sleep(5);
        header("Location: " . $url . "/modules/default/login.php?action=loginerror");
    }

    // password username
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Apoteke Sarajevo Employee Portal Login</title>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--===============================================================================================-->
        <link href="<?php echo $host; ?>/theme/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo $host; ?>/theme/css/login.css" rel="stylesheet">

    </head>
    <body>

    <div class="login-page">
        <div class="login-element">
            <div class="image-part">
                <img src="<?= $host; ?>/theme/images/logobih.png" alt="">
                <h4>Apoteke Sarajevo Employee Portal Login</h4>

                <?php if($action == 'loginerror'){ ?>
                    <div class="alert alert-danger"><i class="ion-alert-circled"></i> <b><?php echo 'Gre??ka!'; ?></b><br/><?php echo 'Pogre??no korisni??ko ime i/ili lozinka, molimo poku??ajte ponovo.'; ?></div>

                <?php  } ?>

                <?php if($action == 'loginerror2'){ ?>
                    <div class="alert alert-danger"><i class="ion-alert-circled"></i> <b><?php echo 'Gre??ka!'; ?></b><br/><?php echo 'Va?? ra??un je deaktiviran. Za vi??e informacija kontaktirajte  nas.'; ?></div>
                <?php } ?>

                <?php if($action == 'loggedout'){ ?>
                    <div class="alert alert-success"><i class="ion-ios-checkmark"></i> <b><?php echo 'Uspje??no!'; ?></b><br/><?php echo 'Uspje??no ste se odjavili.'; ?></div>
                <?php } ?>

                <?php if($action == 'logindeactivated'){ ?>
                    <div class="alert alert-warning"><i class="ion-minus-circled"></i> <b><?php echo 'Neuspje??no!'; ?></b><br/><?php echo 'Va?? korisni??ki nalog je deaktiviran.'; ?></div>
                <?php } ?>
            </div>

            <div class="form-element">
                <form method="post">
                    <input type="hidden" name="login" value="<?= md5(time()); ?>">

                    <div class="form-group">
                        <label for="username">Va??e korisni??ko ime</label>
                        <input type="text" class="form-control" id="username" aria-describedby="usernameHelp" name="username">
                        <small id="emailHelp" class="form-text text-muted">Unesite korisni??ko ime i ??ifru za prijavu u sistem</small>
                    </div>
                    <div class="form-group">
                        <label for="password">Va??a ??ifra</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <button type="submit" class="btn my-lgn-btn">PRIJAVITE SE</button>
                </form>
            </div>

        </div>
    </div>

    <!--===============================================================================================-->
    <script src="<?php echo $host; ?>/theme/vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="<?php echo $host; ?>/theme/vendor/js/main.js"></script>

    </body>
    </html>
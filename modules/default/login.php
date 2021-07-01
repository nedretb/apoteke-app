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
                header("Location: " . $url . "/");
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
                    <div class="alert alert-danger"><i class="ion-alert-circled"></i> <b><?php echo 'Greška!'; ?></b><br/><?php echo 'Pogrešno korisničko ime i/ili lozinka, molimo pokušajte ponovo.'; ?></div>

                <?php  } ?>

                <?php if($action == 'loginerror2'){ ?>
                    <div class="alert alert-danger"><i class="ion-alert-circled"></i> <b><?php echo 'Greška!'; ?></b><br/><?php echo 'Vaš račun je deaktiviran. Za više informacija kontaktirajte  nas.'; ?></div>
                <?php } ?>

                <?php if($action == 'loggedout'){ ?>
                    <div class="alert alert-success"><i class="ion-ios-checkmark"></i> <b><?php echo 'Uspješno!'; ?></b><br/><?php echo 'Uspješno ste se odjavili.'; ?></div>
                <?php } ?>

                <?php if($action == 'logindeactivated'){ ?>
                    <div class="alert alert-warning"><i class="ion-minus-circled"></i> <b><?php echo 'Neuspješno!'; ?></b><br/><?php echo 'Vaš korisnički nalog je deaktiviran.'; ?></div>
                <?php } ?>
            </div>

            <div class="form-element">
                <form method="post">
                    <input type="hidden" name="login" value="<?= md5(time()); ?>">

                    <div class="form-group">
                        <label for="username">Vaše korisničko ime</label>
                        <input type="text" class="form-control" id="username" aria-describedby="usernameHelp" name="username">
                        <small id="emailHelp" class="form-text text-muted">Unesite korisničko ime i šifru za prijavu u sistem</small>
                    </div>
                    <div class="form-group">
                        <label for="password">Vaša šifra</label>
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
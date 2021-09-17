<?php
require_once '../../../configuration.php';
//include_once $root . '/modules/default/functions.php';


?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Pregled'); ?></span></h4>
</div>

<section>
    <div class="content clear">

        <?php
        $get = $db->query("SELECT * FROM  " . $portal_hourlyrate_day . "  WHERE id='" . $_GET['id'] . "'");
        if ($get->rowCount() < 0) {
            $row = $get->fetch();

            $get_year = $db->query("SELECT * FROM  " . $portal_hourlyrate_year . "  WHERE id='" . $row['year_id'] . "'");
            $get_month = $db->query("SELECT * FROM  " . $portal_hourlyrate_month . "  WHERE id='" . $row['month_id'] . "'");
            $year = $get_year->fetch();
            $month = $get_month->fetch();
            $parent = _user($row['review_user']);

            if ($row['review_status'] == '0') {
                $css = '';
                $status = '';
            } elseif ($row['review_status'] == '1') {
                $css = 'style="color:#00cc00;"';
                $status = __('ODOBRENO');
            } elseif ($row['review_status'] == '2') {
                $css = 'style="color:#cc0000;"';
                $status = __('ODBIJENO');
            }
            ?>

            <div class="row">

                <div class="col-sm-6">
                    <big><?php echo $row['day'] . '.' . $month['month'] . '.' . $year['year']; ?></big><br/>
                    <b><?php echo _nameHRstatus($row['status']); ?></b><br/>
                    <?php echo __('Broj sati'); ?> <b><?php echo rtrim(rtrim($row['hour'], '0'), '.'); ?></b>
                    <?php
                    if ($row['status_pre'] != '' and $row['status_pre'] != '0') {
                        ?>
                        <br/><b><?php echo _nameHRstatus($row['status_pre']); ?></b><br/>
                        <?php
                        if (fmod($row['hour_pre'], 1) == 0) {
                            $row['hour_pre'] = floor($row['hour_pre']);
                        }
                        ?>
                        <?php echo __('Broj sati'); ?> <b><?php echo rtrim(rtrim($row['hour_pre'], '0'), '.'); ?></b>
                    <?php } ?>
                </div>
                <div class="col-sm-6">
                    <?php if ($row['review_status'] ==1 or $row['review_status'] == 2){ ?>
                    <big <?php echo $css; ?>><?php echo $status; ?></big><br/>
                    <small><?php echo __('Obradio:'); ?></small><br/>
                    <?php echo $parent['fname'] . ' ' . $parent['lname']; ?>
                    <?php } ?>
                </div>

            </div>

            <hr/>

            <?php if ($row['review_status'] ==1 or $row['review_status'] == 2){ ?>
            <small><?php echo __('Komentar:'); ?></small><br/>
            <div class="comment-single">
                <?php echo $row['review_comment']; ?>
            </div>
            <?php } ?>

            <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
            <script>
                $(document).ready(function () {
                    $('.dialog-loader').hide();
                });
            </script>

            <?php
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Pogrešan ID stranice, molimo kontaktirajte administratora.') . '</div>';
        }
        ?>

    </div>
    <div class="dialog-loader"><i></i></div>
</section>

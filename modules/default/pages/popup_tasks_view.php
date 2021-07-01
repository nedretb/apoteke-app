<?php
require_once '../../../configuration.php';
?>
<div class="header">
    <a class="btn close" data-widget="close-ajax" data-id="opt2"><i class="ion-android-close"></i></a>
    <h4><span><?php echo __('Pregled/obrada cilja'); ?></span></h4>
</div>

<section>
    <div class="content clear">

        <?php
        $get = $db->query("SELECT * FROM  " . $portal_tasks . "  WHERE task_id='" . $_GET['id'] . "'");
        $get2 = $db->query("SELECT count(*) FROM  " . $portal_tasks . "  WHERE task_id='" . $_GET['id'] . "'");
        if ($get2->rowCount() < 0) {
            $row = $get->fetch();
            $user = _user($row['user_id']);
            // $parent = _user($row['parent_id']);

            $query2 = $db->query("SELECT * FROM  " . $portal_users . "  WHERE employee_no='" . $user['parent'] . "'");

            foreach ($query2 as $item2) {
                $parent_f = $item2['fname'];
                $parent_l = $item2['lname'];
            }

            $query3 = $db->query("SELECT  * FROM  " . $portal_users . "  WHERE employee_no='" . $user['hr'] . "'");

            foreach ($query3 as $item3) {
                $hr_f = $item3['fname'];
                $hr_l = $item3['lname'];

            }

            $query4 = $db->query("SELECT  * FROM  " . $portal_users . "  WHERE employee_no='" . $user['admin'] . "'");

            foreach ($query4 as $item4) {
                $admin_f = $item4['fname'];
                $admin_l = $item4['lname'];

            }

            ?>


            <?php if ($row['is_accepted'] == 0){ ?>
            <div class="text-center">
                <?php echo __('Zadatak jos nije prihvaćen od stane nadređenog, i zbog toga nije moguće obraditi stavke zadatka.'); ?>
                <br/>

            </div><br/>
        <hr/>
        <?php } ?>

            <div class="row">
                <div class="col-sm-6">
                    <small><?php echo __('Nadređeni:'); ?></small><br/>
                    <?php echo $parent_f . ' ' . $parent_l; ?><br/>
                    <?php

                    echo '<i class="ion-clock" style="color:#E32040;"></i> ' . __('Izvršiti do:') . ' ' . date('d/m/Y', strtotime($row['date_end'])) . '&nbsp; &nbsp; &nbsp;';

                    ?>
                </div>
                <div class="col-sm-6">
                    <small><?php echo __('Kreirao:'); ?></small><br/>
                    <?php echo $user['fname'] . ' ' . $user['lname']; ?>
                    <?php echo __('Datum kreiranja:'); ?><br/>
                    <?php echo date('d/m/Y', strtotime($row['date_created'])); ?>
                </div>
            </div>

            <hr/>


            <b><?php echo __('Komentari'); ?></b><br/>
            <div id="comments"></div>

            <hr/>
        <?php if ($row['is_user_reviewed'] == '0'){ ?>
            <div id="res"></div>

            <form id="popup_form" method="post" enctype="multipart/form-data">

                <input type="hidden" name="request" value="task-comment"/>
                <input type="hidden" name="comment_on" value="<?php echo $_GET['id']; ?>"/>
                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>"/>

                <label><?php echo __('Ostavite komentar'); ?></label><br/>
                <textarea name="comment" class="form-control" required spellcheck="false"></textarea><br/>

                <button type="submit" class="btn btn-red pull-right"><?php echo __('Dodaj komentar!'); ?> <i
                            class="ion-ios-download-outline"></i></button>


            </form>
        <?php }else{ ?>
            <div class="text-center">
                <?php echo __('Zadatak je završen i ocjenjen, a s tim su i komentari zatvoreni.'); ?>
                <br/><br/>
                <?php echo __('Ocjena:'); ?>
                <div style="font-size:24px;">
                    <?php
                    $star_full = $row['user_rating'];
                    $star_blank = (5 / $row['user_rating']);
                    for ($i = 1; $i <= $star_full; $i++) {
                        echo '<i class="ion-android-star"></i>';
                    }
                    for ($i = 0; $i <= $star_blank; $i++) {
                        echo '<i class="ion-android-star-outline"></i>';
                    }
                    ?>
                </div>
            </div>
        <?php } ?>


            <script src="<?php echo $_pluginUrl; ?>/jquery/jquery.js"></script>
            <script src="<?php echo $_pluginUrl; ?>/validation/jquery.validate.min.js"></script>
            <script src="<?php echo $_pluginUrl; ?>/validation/jquery.form.js"></script>
            <script>
                $(function () {


                    $('[data-widget="accept"]').on("click", function (e) {
                        e.preventDefault();
                        var _url = $(this).attr('href');
                        var result = $(this).attr('data-id').split(':');
                        var rm = result[1];
                        $.post(_url, {request: 'accept-' + result[0], request_id: rm}, function (data) {
                            window.location.reload();
                        });
                    });


                    $('[data-widget="completed"]').on("click", function (e) {
                        e.preventDefault();
                        var _url = $(this).attr('href');
                        var result = $(this).attr('data-id').split(':');
                        var rm = result[1];
                        $.post(_url, {request: 'completed-' + result[0], request_id: rm}, function (data) {
                            window.location.reload();
                        });
                    });


                    $('.processing').on('click', 'input[name=items]', function () {
                        var _id = $(this).attr('rel');
                        if ($(this).is(':checked')) {
                            $.post('<?php echo $url . '/modules/default/ajax.php'; ?>', {
                                request: 'proc-tasks',
                                request_id: _id,
                                status: '1'
                            }, function (data) {
                                $('#ch' + _id).wrap('<strike></strike>');
                                $.post('<?php echo $url . '/modules/default/ajax.php'; ?>', {
                                    request: 'count-tasks',
                                    request_id: '<?php echo $_GET["id"]; ?>'
                                }, function (data) {
                                    if (data == 'yes') {
                                        $('#completed').slideDown();
                                    }
                                });
                            });
                        } else {
                            $.post('<?php echo $url . '/modules/default/ajax.php'; ?>', {
                                request: 'proc-tasks',
                                request_id: _id,
                                status: '0'
                            }, function (data) {
                                $('#ch' + _id).unwrap();
                                $.post('<?php echo $url . '/modules/default/ajax.php'; ?>', {
                                    request: 'count-tasks',
                                    request_id: '<?php echo $_GET["id"]; ?>'
                                }, function (data) {
                                    if (data == 'no') {
                                        $('#completed').slideUp();
                                    }
                                });
                            });
                        }
                        y
                    });


                    function _loadComments() {

                        var _id = '<?php echo $_GET["id"]; ?>';
                        var _user = '<?php echo $row["user_id"]; ?>';
                        var _parent = '<?php echo _employee($row['parent_id'])['user_id']; ?>';
                        $.post('<?php echo $url . '/modules/default/ajax.php'; ?>', {
                            request: 'comments',
                            request_id: _id,
                            user: _user,
                            parent: _parent
                        }, function (data) {
                            $('#comments').html(data);
                        });
                    }

                    $(document).ready(function () {
                        _loadComments();
                        $('.dialog-loader').hide();
                    });

                    $("#popup_form").validate({
                        focusCleanup: true,
                        submitHandler: function (form) {
                            $('.dialog-loader').show();
                            $(form).ajaxSubmit({
                                url: "<?php echo $url . '/modules/default/ajax.php'; ?>",
                                type: "post",
                                success: function (data) {
                                    $("#res").html(data);
                                    $(".dialog").animate({scrollTop: 500}, 600);
                                    _loadComments();
                                    $('.dialog-loader').hide();
                                }
                            });
                        }
                    });

                    $("#review_form").validate({
                        focusCleanup: true,
                        submitHandler: function (form) {
                            $('.dialog-loader').show();
                            $(form).ajaxSubmit({
                                url: "<?php echo $url . '/modules/default/ajax.php'; ?>",
                                type: "post",
                                success: function (data) {
                                    if (data == 1) {
                                        window.location.reload();
                                    }
                                }
                            });
                        }
                    });

                    $('form').on('click', '.btn-sr', function (e) {
                        e.preventDefault();
                        $('.dialog-loader').show();
                        var _formID = $(this).attr("rel");
                        var _ser = $("#" + _formID).serialize();
                        $.post('<?php echo $url . '/modules/default/ajax.php'; ?>', {
                            request: 'task-review-item',
                            data: _ser
                        }, function (data) {
                            if (data) {
                                $('.dialog-loader').hide();
                                var _elem = $('.user_rating_' + data);
                                _elem.find('input[type=radio]').attr('disabled', true);
                                _elem.find('button').remove();
                            }
                        });
                    });


                });

            </script>

            <?php
        } else {
            echo '<div class="alert alert-danger"><b>' . __('Greška!') . '</b><br/>' . __('Pogrešan ID stranice, molimo kontaktirajte administratora.') . '</div>';
        }
        ?>

    </div>
    <div class="dialog-loader"><i></i></div>


    <hr/>


</section>

<link rel="stylesheet" href="theme/css/performance-management.css">

<?php
require_once 'CORE/classes/user.php';

$user = new User($db);
$children = $user->getSQLChildrenObjects($_user['employee_no'], $_user['user_id']);

?>



<form id="admin-form" method="post">
    <div class="split-on-right">
        <div class="choose-what-to-do">
            <h3>
                Pregled uposlenika
            </h3>


            <?php
            if($children == null){
                echo '<p> Nema uposlenika ! </p>';
            }else{
                ?>
                <table>
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Ime i prezime</th>
                        <th>Email</th>
                        <th>Pozicija</th>
                        <th class="last-one" style="width: 140px;">AKCIJE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $counter = 1;
                    //                var_dump($children);
                    //                die();
                    foreach($children as $child){
                        // Ovdje ispisujemo svu djecu :))
                        if($child['user_id'] != $_user['user_id']){
                            try{
                                $hasAgreement = count($db->query("SELECT * FROM [c0_intranet2_apoteke].[dbo].[pm_sporazumi] where user_id = ".$child['user_id']." and year = ".date('Y'))->fetchAll());
                            }catch (PDOException $e){}

                            ?>
                            <tr>
                                <td class="<?php echo !$hasAgreement ? 'red_wedding' : '' ?>"><?php echo $counter++; ?>.</td>
                                <td class="<?php echo !$hasAgreement ? 'red_wedding' : '' ?>"><?php echo $child['fname'].' '.$child['lname']; ?></td>
                                <td class="<?php echo !$hasAgreement ? 'red_wedding' : '' ?>"><?php echo $child['email_company']; ?></td>
                                <td class="<?php echo !$hasAgreement ? 'red_wedding' : '' ?>"><?php echo $child['position']; ?></td>
                                <td class="last-one <?php echo !$hasAgreement ? 'red_wedding' : '' ?>" title="Pregledajte / Editujte">
                                    <a href="?m=performance_management&p=mbo&user_id=<?php echo $child['user_id']; ?>">
                                        <div class="my-button">PREGLED</div>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            }
            ?>

        </div>

        <div class="right-menu">
            <div class="right-menu-header">
                <h4>Dodatne opcije</h4>
            </div>
            <?php require_once 'modules/performance_management/Pages/scripts/side_links.php'; ?>
        </div>
    </div>
</form>
<?php include $_themeRoot.'/footer.php'; ?>

<?php

$paddingCounter += 20;

foreach ($children as $child){
    $nadredjena = Sistematizacija::where('id = '.$child['s_parent'])->first();
    ?>
    <tr>
        <td class="text-center"><small><?= $counter++; ?></small></td>
        <td>
            <small><p class="tl-<?= $paddingCounter ?>"><b><?= $child['s_no'] ?></b><?= ' - '.$child['s_title'] ?></p></small>
        </td>
        <td><small><?= $nadredjena['s_title'] ?></small></td>
        <td class="text-center"><a class="my-btn" href="?m=scheme&p=create&id=<?= $child['id'] ?>"><?= ___('Pregled') ?></a></td>
    </tr>
    <?php

    $children = Sistematizacija::where('s_parent = '.$child['id'])->get();
    if(count($children)) {
        include $root . '/modules/' . $_mod . '/pages/single-row.php';
        $paddingCounter -= 20;
    }
}

?>
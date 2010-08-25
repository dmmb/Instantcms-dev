<?php
    global $tpl_data;
    extract($tpl_data);
?>

<h3><?php echo $module->title; ?></h3>

<form action="" method="post">

    <table cellpadding="0" cellspacing="0" border="0">
        <?php foreach($fields as $field){ ?>
        <tr>
            <td><strong><?php echo $field['title']; ?></strong></td>
            <td>
                <?php echo $field['html']; ?>
            </td>
        </tr>
        <?php } ?>
    </table>

</form>

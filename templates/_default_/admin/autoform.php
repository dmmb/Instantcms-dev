<?php

    global $tpl_data;
    extract($tpl_data);

?>

<input type="hidden" name="do" value="save_auto_config" />

<div class="params-form">
    <table cellpadding="3" cellspacing="0" border="0">
        <?php foreach($fields as $field){ ?>
        <tr>
            <td class="param-name">
                <div class="label"><strong><?php echo $field['title']; ?></strong></div>
                <?php if ($field['hint']) { ?>
                    <div class="hinttext"><?php echo $field['hint']; ?></div>
                <?php } ?>
            </td>
            <td class="param-value">
                <?php echo $field['html']; ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<div class="params-buttons">
    <input type="submit" name="save" value="Сохранить" />
</div>

<script type="text/javascript">
    function submitModuleConfig(){
        $('#optform').submit();
    }
</script>


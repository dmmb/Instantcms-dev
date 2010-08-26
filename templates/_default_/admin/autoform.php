<?php

    global $tpl_data;
    extract($tpl_data);

    $module_id = (int)$_REQUEST['id'];

    cpAddPathway($module->title, '?view=modules&do=edit&id='.$module_id);
	cpAddPathway('Настройки', '?view=modules&do=config&id='.$module_id);

    $toolmenu = array();
    $toolmenu[0]['icon'] = 'save.gif';
    $toolmenu[0]['title'] = 'Сохранить';
    $toolmenu[0]['link'] = 'javascript:submitModuleConfig()';

    $toolmenu[1]['icon'] = 'edit.gif';
    $toolmenu[1]['title'] = 'Редактировать отображение модуля';
    $toolmenu[1]['link'] = '?view=modules&do=edit&id='.$module_id;

    $toolmenu[2]['icon'] = 'cancel.gif';
    $toolmenu[2]['title'] = 'Отмена';
    $toolmenu[2]['link'] = '?view=modules';

    cpToolMenu($toolmenu);
    
?>

<h3><?php echo $module->title; ?></h3>

<?php if (isset($_SESSION['save_message'])){ ?>
    <p class="success"><?php echo $_SESSION['save_message']; ?></p>
    <?php unset ($_SESSION['save_message']); ?>
<?php } ?>

<form action="index.php?view=modules&do=save_auto_config&id=<?php echo $module_id; ?>" method="post" name="optform" target="_self" id="optform">

    <input type="hidden" name="do" value="save_auto_config" />

    <div class="params-form">
        <table cellpadding="3" cellspacing="0" border="0">
            <?php foreach($fields as $field){ ?>
            <tr>
                <td class="param-name"><strong><?php echo $field['title']; ?></strong></td>
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

</form>

<script type="text/javascript">
    function submitModuleConfig(){
        $('#optform').submit();
    }
</script>


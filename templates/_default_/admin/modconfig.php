<?php global $mod, $cfg_form, $mode, $inCore; ?>
<form action="/admin/index.php?view=modules&do=save_auto_config&id=<?php echo $mod['id']; ?>&ajax=1<?php if($mode!='xml'){?>&title_only=1<?php } ?>" method="post" name="optform" target="_self" id="optform">
    <div id="mc_module_title">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="145"><div class="title">Настройка модуля</div></td>
                <td>
                    <div class="value">
                        <input type="text" name="title" value="<?php echo $mod['title']; ?>" />
                    </div>
                </td>
            </tr>
            <tr>
                <td width="145">&nbsp;</td>
                <td style="padding-top:4px">
                    <label>
                        <input type="checkbox" name="published" value="1" <?php if ($mod['published']){ ?>checked="checked"<?php } ?> /> Публиковать модуль
                    </label>
                </td>
            </tr>
        </table>
    </div>
    <div id="mc_module_cfg">
        <?php if ($mode == 'xml'){ ?>
            <?php echo $cfg_form; ?>
        <?php } elseif($mode == 'php') { ?>
            <div class="params-form">
                Этот модуль можно настроить только в <a href="/admin/index.php?view=modules&do=config&id=<?php echo $mod['id']; ?>" target="blank">панели управления</a>.
            </div>
            <div class="params-buttons">
                <input type="submit" name="save" value="Сохранить" />
            </div>
        <?php } elseif($mode == 'none') { ?>
            <div class="params-form">
                Этот модуль можно настроить только в <a href="/admin/index.php?view=modules&do=edit&id=<?php echo $mod['id']; ?>" target="blank">панели управления</a>.
            </div>
            <div class="params-buttons">
                <input type="submit" name="save" value="Сохранить" />
            </div>
        <?php } else { ?>
            <?php $inCore->insertEditor('content', $mod['content'], '450', '100%'); ?>
            <div class="params-buttons">
               <input type="submit" name="save" value="Сохранить" />
            </div>
        <?php } ?>
    </div>
</form>
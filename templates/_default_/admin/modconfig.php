<?php global $mod, $cfg_form, $mode, $inCore; ?>
<form action="/admin/index.php?view=modules&do=save_auto_config&id=<?php echo $mod['id']; ?>&ajax=1<?php if($mode!='xml'){?>&title_only=1<?php } ?>" method="post" name="optform" target="_self" id="optform">
    <div id="mc_module_title">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="145"><div class="title">��������� ������</div></td>
                <td>
                    <div class="value">
                        <input type="text" name="title" value="<?php echo $mod['title']; ?>" />
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div id="mc_module_cfg">
        <?php if ($mode == 'xml'){ ?>
            <?php echo $cfg_form; ?>
        <?php } elseif($mode == 'php') { ?>
            <div class="params-form">
                ���� ������ ����� ��������� ������ � <a href="/admin/index.php?view=modules&do=config&id=<?php echo $mod['id']; ?>" target="blank">������ ����������</a>.
            </div>
            <div class="params-buttons">
                <input type="submit" name="save" value="���������" />
            </div>
        <?php } elseif($mode == 'none') { ?>
            <div class="params-form">
                ���� ������ ����� ��������� ������ � <a href="/admin/index.php?view=modules&do=edit&id=<?php echo $mod['id']; ?>" target="blank">������ ����������</a>.
            </div>
            <div class="params-buttons">
                <input type="submit" name="save" value="���������" />
            </div>
        <?php } else { ?>
            <?php $inCore->insertEditor('content', $mod['content'], '450', '100%'); ?>
            <div class="params-buttons">
               <input type="submit" name="save" value="���������" />
            </div>
        <?php } ?>
    </div>
</form>
<?php if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); } ?>

<form name="selform" action="index.php?view=cron" method="post">
    <table id="listTable" class="tablesorter" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top:10px">
        <thead>
            <tr>
                <th class="lt_header" width="25">id</th>
                <th class="lt_header" width="80">��������</th>
                <th class="lt_header" width="">��������</th>
                <th class="lt_header" width="30">��������</th>
                <th class="lt_header" width="100">����. ������</th>
                <th class="lt_header" width="50">�������?</th>
                <th class="lt_header" align="center" width="65">��������</th>
            </tr>
        </thead>
        <?php if ($items){ ?>
            <tbody>
                <?php foreach($items as $num=>$item){ ?>
                    <tr id="<?php echo $item['id']; ?>" class="item_tr">
                        <td><?php echo $item['id']; ?></td>
                        <td>
                            <a title="�������������" href="?view=cron&do=edit&id=<?php echo $item['id']; ?>">
                                <?php echo $item['name']; ?>
                            </a>
                        </td>
                        <td><?php echo $item['comment']; ?></td>
                        <td><?php echo $item['job_interval']; ?> �.</td>
                        <td><?php echo $item['run_date']; ?></td>
                        <td>
                            <?php if ($item['is_enabled']) { ?>
                                <a id="publink<?php echo $item['id']; ?>" href="javascript:pub(<?php echo $item['id']; ?>, 'view=cron&do=hide&id=<?php echo $item['id']; ?>', 'view=content&do=show&id=<?php echo $item['id']; ?>', 'off', 'on');" title="���������">
                                    <img id="pub<?php echo $item['id']; ?>" border="0" src="images/actions/on.gif"/>
                                </a>
                            <?php } else { ?>
                                <a id="publink<?php echo $item['id']; ?>" href="javascript:pub(<?php echo $item['id']; ?>, 'view=cron&do=show&id=<?php echo $item['id']; ?>', 'view=content&do=hide&item_=<?php echo $item['id']; ?>', 'on', 'off');" title="��������">
                                    <img id="pub<?php echo $item['id']; ?>" border="0" src="images/actions/off.gif"/>
                                </a>
                            <?php } ?>
                        </td>
                        <td align="right">
                            <div style="padding-right: 8px;">
                                <a title="�������������" href="?view=cron&do=edit&id=<?php echo $item['id']; ?>">
                                    <img border="0" hspace="2" alt="�������������" src="images/actions/edit.gif"/>
                                </a>
                                <a title="�������" onclick="jsmsg('������� ������ <?php echo $item['job_name']; ?>?', '?view=cron&do=delete&id=<?php echo $item['id']; ?>')" href="#">
                                    <img border="0" hspace="2" alt="�������" src="images/actions/delete.gif"/>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        <?php } else { ?>
            <tbody>
                <td colspan="7" style="padding-left:5px"><div style="padding:15px;padding-left:0px">������ �� �������</div></td>
            </tbody>
        <?php } ?>
    </table>

    <script type="text/javascript">highlightTableRows("listTable","hoverRow","clickedRow");</script>
</form>

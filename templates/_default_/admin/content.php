<?php if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); } ?>

<table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%" style="margin-top:2px">
    <tr>
        <td valign="top" width="240" style="<?php if ($hide_cats){ ?>display:none;<?php } ?>" id="cats_cell">

            <div class="cat_add_link">
                <div>
                    <a href="?view=cats&do=add" style="color:#09C">�������� ������</a>
                </div>
            </div>
            <div class="cat_link">
                <div>
                <?php if ($category_id) { ?>
                    <a href="<?php echo $base_uri; ?>" style="font-weight:bold">��� ��������</a>
                <?php } else { $current_cat = '��� ��������'; ?>
                    ��� ��������
                <?php } ?>
                </div>
            </div>
            <?php foreach($cats as $num=>$cat) { ?>
                <div style="padding-left:<?php echo ($cat['NSLevel']-1)*20; ?>px" class="cat_link">
                    <div>
                        <?php if ($category_id != $cat['id']) { ?>
                            <a href="<?php echo $base_uri.'&cat_id='.$cat['id']; ?>" style="<?php if ($cat['NSLevel']==1){ echo 'font-weight:bold'; } ?>"><?php echo $cat['title']; ?></a>
                        <?php } else { ?>
                            <?php echo $cat['title']; $current_cat = $cat['title']; ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </td>

        <td valign="top" id="slide_cell" class="<?php if ($hide_cats){ ?>unslided<?php } ?>" onclick="$('#cats_cell').toggle();$(this).toggleClass('unslided');$('#filter_form input[name=hide_cats]').val(1-$('#cats_cell:visible').length)">
            &nbsp;
        </td>

        <td valign="top" style="padding-left:2px">

            <form action="<?php echo $base_uri; ?>" method="GET" id="filter_form">
                <input type="hidden" name="view" value="tree" />
                <input type="hidden" name="do" value="tree" />
                <input type="hidden" name="cat_id" value="<?php echo $category_id; ?>" />
                <input type="hidden" name="hide_cats" value="<?php echo $hide_cats; ?>" />
                <table class="toolmenu" cellpadding="5" border="0" width="100%" style="margin-bottom: 2px;">
                    <tr>
                        <td width="">
                            <span style="font-size:16px;color:#0099CC;font-weight:bold;">
                                <?php echo $current_cat; ?>
                            </span>
                            <span style="padding-left: 15px;">
                                <a title="�������� ������" href="?view=content&do=add<?php if($category_id){ ?>&to=<?php echo $category_id; } ?>">
                                    <img border="0" hspace="2" alt="�������� ������" src="images/actions/add.gif"/>
                                </a>
                                <?php if($category_id){ ?>
                                    <a title="������������� ������" href="?view=cats&do=edit&id=<?php echo $category_id; ?>">
                                        <img border="0" hspace="2" alt="������������� ������" src="images/actions/edit.gif"/>
                                    </a>
                                    <a title="������� ������" onclick="deleteCat('<?php echo $current_cat; ?>', <?php echo $category_id; ?>)" href="#">
                                        <img border="0" hspace="2" alt="������� ������" src="images/actions/delete.gif"/>
                                    </a>
                                <?php } ?>
                            </span>
                        </td>
                    </tr>
                </table>
                <table class="toolmenu" cellpadding="5" border="0" width="100%" style="margin-bottom: 2px;" id="filterpanel">
                    <tr>
                        <td width="130">
                            <select name="orderby" style="width:130px" onchange="$('#filter_form').submit()">
                                <?php if($category_id){ ?>
                                <option value="ordering" <?php if($orderby=='ordering'){ ?>selected="selected"<?php } ?>>�� �������</option>
                                <?php } ?>
                                <option value="title" <?php if($orderby=='title'){ ?>selected="selected"<?php } ?>>�� ��������</option>
                                <option value="pubdate" <?php if($orderby=='pubdate'){ ?>selected="selected"<?php } ?>>�� ����</option>
                            </select>
                        </td>
                        <td width="150">
                            <select name="orderto" style="width:150px" onchange="$('#filter_form').submit()">
                                <option value="asc" <?php if($orderto=='asc'){ ?>selected="selected"<?php } ?>>�� �����������</option>
                                <option value="desc" <?php if($orderto=='desc'){ ?>selected="selected"<?php } ?>>�� ��������</option>
                            </select>
                        </td>
                        <td width="60">��������:</td>
                        <td width="">
                            <input type="text" name="title" value="<?php echo $title_part; ?>" style="width:99%"/>
                        </td>
                        <td width="30">
                            <input type="submit" name="filter" value="&raquo;" style="width:30px"/>
                        </td>
                    </tr>
                </table>
            </form>

            <form name="selform" action="index.php?view=components" method="post">
                <table id="listTable" class="tablesorter" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top:0px">
                    <thead>
                        <tr>
                            <th class="lt_header" align="center" width="20">
                                <a class="lt_header_link" title="������������� ���������" href="javascript:" onclick="javascript:invert()">#</a>
                            </th>
                            <th class="lt_header" width="25">id</th>
                            <th class="lt_header" width="" colspan="2">��������</th>
                            <th class="lt_header" width="80">����</th>
                            <th class="lt_header" width="50">�����</th>
                            <?php if ($category_id && sizeof($items)>1){ ?>
                                <th class="lt_header" width="24">&darr;&uarr;</th>
                            <?php } ?>
                            <th class="lt_header" align="center" width="90">��������</th>
                        </tr>
                    </thead>
                    <?php if ($items){ ?>
                        <tbody>
                            <?php foreach($items as $num=>$item){ ?>
                                <tr id="<?php echo $item['id']; ?>" class="item_tr">
                                    <td><input type="checkbox" name="item[]" value="<?php echo $item['id']; ?>" /></td>
                                    <td><?php echo $item['id']; ?></td>
                                    <td width="16">
                                        <img src="/templates/_default_/images/icons/article.png" border="0"/>
                                    </td>
                                    <td>
                                        <a href="index.php?view=content&do=edit&id=<?php echo $item['id']; ?>">
                                            <?php echo $item['title']; ?>
                                        </a>                                        
                                    </td>
                                    <td><?php echo $item['fpubdate']; ?></td>
                                    <td>
                                        <?php if ($item['published']) { ?>
                                            <a id="publink<?php echo $item['id']; ?>" href="javascript:pub(<?php echo $item['id']; ?>, 'view=content&do=hide&id=<?php echo $item['id']; ?>', 'view=content&do=show&id=<?php echo $item['id']; ?>', 'off', 'on');" title="������">
                                                <img id="pub<?php echo $item['id']; ?>" border="0" src="images/actions/on.gif"/>
                                            </a>
                                        <?php } else { ?>
                                            <a id="publink<?php echo $item['id']; ?>" href="javascript:pub(<?php echo $item['id']; ?>, 'view=content&do=show&id=<?php echo $item['id']; ?>', 'view=content&do=hide&item_=<?php echo $item['id']; ?>', 'on', 'off');" title="��������">
                                                <img id="pub<?php echo $item['id']; ?>" border="0" src="images/actions/off.gif"/>
                                            </a>
                                        <?php } ?>                                        
                                    </td>
                                    <?php if ($category_id && sizeof($items)>1){ ?>
                                        <td>
                                            <?php
                                                $display_move_down  = ($num<sizeof($items)-1) ? 'inline' : 'none';
                                                $display_move_up    = ($num>0) ? 'inline' : 'none';
                                            ?>
                                            <a class="move_item_down" href="javascript:void(0)" onclick="moveItem(<?php echo $item['id']; ?>, 1)" title="��������� ����" style="float:left;display:<?php echo $display_move_down; ?>"><img src="images/actions/down.gif" border="0"/></a>
                                            <a class="move_item_up" href="javascript:void(0)" onclick="moveItem(<?php echo $item['id']; ?>, -1)" title="��������� ����" style="float:right;display:<?php echo $display_move_up; ?>"><img src="images/actions/top.gif" border="0"/></a>
                                        </td>
                                    <?php } ?>
                                    <td align="right">
                                        <div style="padding-right: 8px;">
                                            <a title="���������� �� �����" href="/<?php echo $item['seolink'];?>.html">
                                                <img border="0" hspace="2" alt="���������� �� �����" src="images/actions/search.gif"/>
                                            </a>
                                            <a title="�������������" href="?view=content&do=edit&id=<?php echo $item['id']; ?>">
                                                <img border="0" hspace="2" alt="�������������" src="images/actions/edit.gif"/>
                                            </a>
                                            <a title="� �����" href="?view=content&do=arhive_on&id=<?php echo $item['id']; ?>">
                                                <img border="0" hspace="2" alt="� �����" src="images/actions/arhive_on.gif">
                                            </a>
                                            <a title="�������" onclick="jsmsg('������� <?php echo $item['title']; ?>?', '?view=content&do=delete&id=<?php echo $item['id']; ?>')" href="#">
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
                <?php if ($items){ ?>

                    <div style="margin-top:4px;padding-top:4px;">
                        <table class="" cellpadding="5" border="0" height="40">
                            <tr>
                                <td width="">
                                   <strong style="color:#09C">����������:</strong>
                                </td>
                                <td width="" class="sel_pub">
                                    <input type="button" name="" value="�������������" onclick="sendContentForm('edit');" />
                                    <input type="button" name="" value="���������" onclick="$('.sel_move').toggle();$('.sel_pub').toggle();" />
                                </td>
                                <td class="sel_move" style="display:none">
                                    ��������� � ������
                                </td>
                                <td class="sel_move" style="display:none">
                                    <select id="move_cat_id" style="width:250px">
                                        <?php
                                           echo $inCore->getListItemsNS('cms_category', $category_id);
                                        ?>
                                    </select>
                                </td>
                                <td class="sel_move" style="display:none">
                                    <input type="button" name="" value="��" onclick="sendContentForm('move_to_cat', $('select#move_cat_id').val(), <?php echo $category_id; ?>);" />
                                    <input type="button" name="" value="������" onclick="$('td.sel_move').toggle();$('td.sel_pub').toggle();" />
                                </td>
                                <td class="sel_pub">
                                    <input type="button" name="" value="��������" onclick="sendContentForm('show');" />
                                    <input type="button" name="" value="������" onclick="sendContentForm('hide');" />
                                </td>
                                <td class="sel_pub">
                                    <input type="button" name="" value="�������" onclick="sendContentForm('delete');" />
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php } ?>
                <script type="text/javascript">highlightTableRows("listTable","hoverRow","clickedRow");</script>
            </form>
            
            <?php
                if ($pages>1){
                    echo cmsPage::getPagebar($total, $page, $perpage, $base_uri.'&hide_cats='.$hide_cats.'&title='.$title_part.'&orderby='.$orderby.'&orderto='.$orderto.'&cat_id='.$category_id.'&page=%page%');
                }
            ?>
        </td>
    </tr>
</table>
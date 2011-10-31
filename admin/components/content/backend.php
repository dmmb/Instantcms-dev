<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

	cpAddPathway('������� ������', '?view=components&do=config&id='.(int)$_REQUEST['id']);
	
	echo '<h3>������� ������</h3>';
	
	$opt = $inCore->request('opt', 'str', 'list');
	
	$toolmenu = array();

	$toolmenu[0]['icon'] = 'save.gif';
	$toolmenu[0]['title'] = '���������';
	$toolmenu[0]['link'] = 'javascript:document.optform.submit();';

	$toolmenu[1]['icon'] = 'cancel.gif';
	$toolmenu[1]['title'] = '������';
	$toolmenu[1]['link'] = '?view=components';

	cpToolMenu($toolmenu);

	if($opt=='saveconfig'){	
		$cfg = array();
        $cfg['readdesc']    = $inCore->request('readdesc', 'int', 0);
		$cfg['rating']      = $inCore->request('rating', 'int', 0);
		$cfg['perpage']     = $inCore->request('perpage', 'int', 0);
        $cfg['pt_show']     = $inCore->request('pt_show', 'int', 0);
		$cfg['pt_disp']     = $inCore->request('pt_disp', 'int', 0);
		$cfg['pt_hide']     = $inCore->request('pt_hide', 'int', 0);
		$cfg['autokeys']    = $inCore->request('autokeys', 'int', 0);

        $cfg['img_small_w'] = $inCore->request('img_small_w', 'int', 100);
        $cfg['img_big_w']   = $inCore->request('img_big_w', 'int', 200);
        $cfg['img_sqr']     = $inCore->request('img_sqr', 'int', 1);
        $cfg['img_users']   = $inCore->request('img_users', 'int', 1);
		$cfg['watermark']   = $inCore->request('watermark', 'int', 0);
		$cfg['watermark_only_big']   = $inCore->request('watermark_only_big', 'int', 0);

        $cfg['af_on']           = $inCore->request('af_on', 'int', 0);
        $cfg['af_delete']       = $inCore->request('af_delete', 'int', 1);
        $cfg['af_showlink']     = $inCore->request('af_showlink', 'int', 1);
        $cfg['af_forum_id']     = $inCore->request('af_forum_id', 'int', 0);
        $cfg['af_hidecat_id']   = $inCore->request('af_hidecat_id', 'int', 0);

		$inCore->saveComponentConfig('content', $cfg);
		
		$msg = '��������� ���������.';
	}

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

    $cfg = $inCore->loadComponentConfig('content');

	if(!isset($cfg['rating'])) { $cfg['rating'] = 1; }
	if(!isset($cfg['perpage'])) { $cfg['perpage'] = 20; }	
	if(!isset($cfg['autokeys'])) { $cfg['autokeys'] = 1; }
    if(!isset($cfg['readdesc'])) { $cfg['readdesc'] = 0; }

	if(!isset($cfg['img_small_w'])) { $cfg['img_small_w'] = 100; }
	if(!isset($cfg['img_big_w'])) { $cfg['img_big_w'] = 200; }
    if(!isset($cfg['img_sqr'])) { $cfg['img_sqr'] = 1; }
    if(!isset($cfg['img_users'])) { $cfg['img_users'] = 1; }

    if(!isset($cfg['af_on'])) { $cfg['af_on'] = 0; }
    if(!isset($cfg['af_delete'])) { $cfg['af_delete'] = 1; }
    if(!isset($cfg['af_showlink'])) { $cfg['af_showlink'] = 1; }
    if(!isset($cfg['af_forum_id'])) { $cfg['af_forum_id'] = 0; }
    if(!isset($cfg['af_hidecat_id'])) { $cfg['af_hidecat_id'] = 0; }

    require('../includes/jwtabs.php');
    $GLOBALS['cp_page_head'][] = jwHeader();

?>


<form action="index.php?view=components&do=config&id=<?php echo (int)$_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
    <?php ob_start(); ?>
    {tab=������� ���}
    <table width="550" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td><strong>�������� ������ ��� ��������� ������: </strong></td>
            <td width="110">
                <input name="readdesc" type="radio" value="1" <?php if (@$cfg['readdesc']) { echo 'checked="checked"'; } ?>/> ��
                <input name="readdesc" type="radio" value="0" <?php if (@!$cfg['readdesc']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td><strong>������� ������: </strong></td>
            <td>
                <input name="rating" type="radio" value="1" <?php if (@$cfg['rating']) { echo 'checked="checked"'; } ?>/> ���
                <input name="rating" type="radio" value="0" <?php if (@!$cfg['rating']) { echo 'checked="checked"'; } ?>/> ����
            </td>
        </tr>
        <tr>
            <td>
                <strong>������������� ������������<br />�������� ����� � �������� ��� ������:</strong>
            </td>
            <td valign="top">
                <input name="autokeys" type="radio" value="1" <?php if (@$cfg['autokeys']) { echo 'checked="checked"'; } ?>/> ��
                <input name="autokeys" type="radio" value="0" <?php if (@!$cfg['autokeys']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td><strong>���������� ������ �� ��������: </strong></td>
            <td><input name="perpage" type="text" id="perpage" value="<?php echo @$cfg['perpage'];?>" size="5" /> ��.</td>
        </tr>
    </table>
    <table width="550" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td><strong>���������� ���������� ������: </strong></td>
            <td width="110">
                <input name="pt_show" type="radio" value="1" <?php if (@$cfg['pt_show']) { echo 'checked="checked"'; } ?>/> ��
                <input name="pt_show" type="radio" value="0" <?php if (@!$cfg['pt_show']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td><strong>������������� ����������: </strong></td>
            <td>
                <input name="pt_disp" type="radio" value="1" <?php if (@$cfg['pt_disp']) { echo 'checked="checked"'; } ?>/> ��
                <input name="pt_disp" type="radio" value="0" <?php if (@!$cfg['pt_disp']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td><strong>������ &quot;������ ����������&quot; : </strong></td>
            <td>
                <input name="pt_hide" type="radio" value="1" <?php if (@$cfg['pt_hide']) { echo 'checked="checked"'; } ?>/> ��
                <input name="pt_hide" type="radio" value="0" <?php if (@!$cfg['pt_hide']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
    </table>
    {tab=���� ������}
    <table width="550" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td><strong>������ ��������� �����:</strong></td>
            <td width="120">
                <input name="img_small_w" type="text" id="img_small_w" value="<?php echo @$cfg['img_small_w'];?>" size="5" /> ����.
            </td>
        </tr>
        <tr>
            <td><strong>������ ������� �����:</strong></td>
            <td>
                <input name="img_big_w" type="text" id="img_big_w" value="<?php echo @$cfg['img_big_w'];?>" size="5" /> ����.
            </td>
        </tr>
        <tr>
            <td><strong>����������:</strong></td>
            <td>
                <input name="img_sqr" type="radio" value="1" <?php if (@$cfg['img_sqr']) { echo 'checked="checked"'; } ?>/> ��
                <input name="img_sqr" type="radio" value="0" <?php if (@!$cfg['img_sqr']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td>
                <strong>��������� �������������:</strong><br/>
                <span class="hinttext">������ �� ������������ ��������� ���������� � ����� �������</span>
            </td>
            <td>
                <input name="img_users" type="radio" value="1" <?php if (@$cfg['img_users']) { echo 'checked="checked"'; } ?>/> ��
                <input name="img_users" type="radio" value="0" <?php if (@!$cfg['img_users']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
           <td><strong>�������� ������� ����:</strong>  <br />���� ��������, �� �� ��� �����������
			      ���������� � ������� ����� ��������� ����������� 
			      �� ����� "<a href="/images/watermark.png" target="_blank">/images/watermark.png</a>"</td>
           <td width="260">
               <input name="watermark" type="radio" value="1" <?php if (@$cfg['watermark']) { echo 'checked="checked"'; } ?> /> ��
               <input name="watermark" type="radio" value="0"  <?php if (@!$cfg['watermark']) { echo 'checked="checked"'; } ?> /> ���
           </td>
        </tr>
        <tr>
           <td><strong>�������� ������� ���� ������ �� ������� �����:</strong><br />
           �������� ������ � ���������� ������ "�������� ������� ����"</td>
           <td width="260">
               <input name="watermark_only_big" type="radio" value="1" <?php if (@$cfg['watermark_only_big']) { echo 'checked="checked"'; } ?> /> ��
               <input name="watermark_only_big" type="radio" value="0"  <?php if (@!$cfg['watermark_only_big']) { echo 'checked="checked"'; } ?> /> ���
           </td>
        </tr>
    </table>
    {tab=���������}
    <table width="550" border="0" cellpadding="10" cellspacing="0" class="proptable">
        <tr>
            <td><strong>������������� ��������� ���� �� ������<br/>��� ���������� ������:</strong></td>
            <td width="250">
                <input name="af_on" type="radio" value="1" <?php if (@$cfg['af_on']) { echo 'checked="checked"'; } ?>/> ��
                <input name="af_on" type="radio" value="0" <?php if (@!$cfg['af_on']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td><strong>������� ���� ��� �������� ������:</strong></td>
            <td width="250">
                <input name="af_delete" type="radio" value="1" <?php if (@$cfg['af_delete']) { echo 'checked="checked"'; } ?>/> ��
                <input name="af_delete" type="radio" value="0" <?php if (@!$cfg['af_delete']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td><strong>���������� ������ �� ������ �� ��������� ���� ������:</strong></td>
            <td width="250">
                <input name="af_showlink" type="radio" value="1" <?php if (@$cfg['af_showlink']) { echo 'checked="checked"'; } ?>/> ��
                <input name="af_showlink" type="radio" value="0" <?php if (@!$cfg['af_showlink']) { echo 'checked="checked"'; } ?>/> ���
            </td>
        </tr>
        <tr>
            <td><strong>�������� ���� � �����:</strong></td>
            <td width="250">
                <select name="af_forum_id" style="width:250px">
                    <?php
                        if (isset($cfg['af_forum_id'])){
                            echo $inCore->getListItemsNS('cms_forums', $cfg['af_forum_id']);
                        } else {
                            echo $inCore->getListItemsNS('cms_forums');
                        }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><strong>�� ��������� ���� ��� ������ �� �������:</strong></td>
            <td width="250">
                <select name="af_hidecat_id" style="width:250px">
                    <?php
                        if (isset($cfg['af_hidecat_id'])){
                            echo $inCore->getListItemsNS('cms_category', $cfg['af_hidecat_id']);
                        } else {
                            echo $inCore->getListItemsNS('cms_category');
                        }
                    ?>
                </select>
            </td>
        </tr>
    </table>
    {/tabs}
    <?php echo jwTabs(ob_get_clean()); ?>
    <p>
        <input name="opt" type="hidden" value="saveconfig" />
        <input name="save" type="submit" id="save" value="���������" />
        <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components';"/>
    </p>
</form>
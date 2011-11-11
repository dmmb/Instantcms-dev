<?php
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

if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

function newContent($table, $where=''){
    if ($where) { $where = ' AND '.$where; }
    $new = dbGetField($table, "DATE_FORMAT(pubdate, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y'){$where}", 'COUNT(id)');
    return $new;
}

function applet_main(){

    $inCore = cmsCore::getInstance();

	$GLOBALS['cp_page_title'] = '�������';

	$new['content'] = (int)newContent('cms_content');
	$new['photos'] 	= (int)newContent('cms_photo_files');
	$new['faq'] 	= (int)newContent('cms_faq_quests');
	$new['board'] 	= (int)newContent('cms_board_items');
	$new['catalog'] = (int)newContent('cms_uc_items');
	$new['forum'] 	= (int)newContent('cms_forum_posts');

?>

<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <td width="275" valign="top" style="padding-left:0px;">
	<div class="small_box">
	<div class="small_title">������� �����</div>
	<div style="padding:8px">
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
      <tr>
        <td><a href="index.php?view=tree">������</a> <?php if($new['content']) { ?><span class="new_content">+<?php echo $new['content']?></span><?php } ?></td>
        <td width="20" align="center"><a href="index.php?view=cats&amp;do=add"><img src="/admin/images/mainpage/folder_add.png" alt="������� ������" width="16" height="16" border="0" /></a></td>
        <td width="20" align="center"><a href="index.php?view=content&amp;do=add"><img src="/admin/images/mainpage/page_add.png" alt="������� ������" width="16" height="16" border="0" /></a></td>
        </tr>
      <tr>
	    <?php $cid = dbGetField('cms_components', "link='photos'", 'id'); ?>
        <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>">�����������</a> <?php if($new['photos']) { ?><span class="new_content">+<?php echo $new['photos']?></span><?php } ?></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_album"><img src="/admin/images/mainpage/folder_add.png" alt="������� ������" width="16" height="16" border="0" /></a></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_photo"><img src="/admin/images/mainpage/page_add.png" alt="��������� ����" width="16" height="16" border="0" /></a></td>
      </tr>
      <tr>
 	    <?php $cid = dbGetField('cms_components', "link='faq'", 'id'); ?>
        <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>">������� � ������</a> <?php if($new['faq']) { ?><span class="new_content">+<?php echo $new['faq']?></span><?php } ?></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_cat"><img src="/admin/images/mainpage/folder_add.png" alt="������� ���������" width="16" height="16" border="0" /></a></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_item"><img src="/admin/images/mainpage/page_add.png" alt="������� ������" width="16" height="16" border="0" /></a></td>
      </tr>
      <tr>
	    <?php $cid = dbGetField('cms_components', "link='board'", 'id'); ?>
        <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>">����� ����������</a> <?php if($new['board']) { ?><span class="new_content">+<?php echo $new['board']?></span><?php } ?></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_cat"><img src="/admin/images/mainpage/folder_add.png" alt="������� �������" width="16" height="16" border="0" /></a></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_item"><img src="/admin/images/mainpage/page_add.png" alt="������� ����������" width="16" height="16" border="0" /></a></td>
      </tr>  
      <tr>
	    <?php $cid = dbGetField('cms_components', "link='catalog'", 'id'); ?>
        <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>">������������� �������</a> <?php if($new['catalog']) { ?><span class="new_content">+<?php echo $new['catalog']?></span><?php } ?></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_cat"><img src="/admin/images/mainpage/folder_add.png" alt="������� �������" width="16" height="16" border="0" /></a></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_item"><img src="/admin/images/mainpage/page_add.png" alt="������� ������" width="16" height="16" border="0" /></a></td>
      </tr>
      <tr>
	    <?php $cid = dbGetField('cms_components', "link='forum'", 'id'); ?>
        <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=list_forums">������</a> <?php if($new['forum']) { ?><span class="new_content">+<?php echo $new['forum']?></span><?php } ?></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_cat"><img src="/admin/images/mainpage/folder_add.png" alt="������� ���������" width="16" height="16" border="0" /></a></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_forum"><img src="/admin/images/mainpage/page_add.png" alt="������� �����" width="16" height="16" border="0" /></a></td>
      </tr>
    </table>	 
	</div>  
	</div>
	<div class="small_box">
		<div class="small_title">������������</div>
		<div style="padding:8px">
		  <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
            <tr>
              <td width="16"><img src="images/icons/hmenu/users.png" width="16" height="16" /></td>
              <td><a href="index.php?view=users">�������������</a> &mdash; <?php echo dbRowsCount('cms_users', 'is_deleted=0'); ?></td>
            </tr>
            <tr>
              <td><img src="images/icons/hmenu/users.png" width="16" height="16" /></td>
              <td>����� �� ������� &mdash; <?php echo (int)dbGetField('cms_users', "DATE_FORMAT(regdate, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y') AND is_deleted = 0", 'COUNT(id)'); ?></td>
            </tr>
            <tr>
              <td><img src="images/icons/hmenu/users.png" width="16" height="16" /></td>
              <td>����� �� ������ &mdash; <?php echo (int)dbGetField('cms_users', "regdate >= DATE_SUB(NOW(), INTERVAL 7 DAY)", 'COUNT(id)'); ?></td>
            </tr>
            <tr>
              <td><img src="images/icons/hmenu/users.png" width="16" height="16" /></td>
              <td>����� �� ����� &mdash; <?php echo (int)dbGetField('cms_users', "regdate >= DATE_SUB(NOW(), INTERVAL 1 MONTH)", 'COUNT(id)'); ?></td>
            </tr>
          </table>
		</div>
	</div>	
	<div class="small_box">
		<div class="small_title"><strong>������ �� �����</strong></div>
		<div style="font-size:10px;margin:8px;">
		  <?php echo cpWhoOnline();?>
		</div>
	</div>
	</td>
    <td width="" valign="top" style="">
	<div class="small_box">
		<div class="small_title"><strong>��������� �������</strong></div>
	    <div id="actions_box">
            <div id="actions">
                <?php

                    $inActions = cmsActions::getInstance();

                    $inActions->showTargets(true);

                    $actions = $inActions->getActionsLog();

                    $tpl_file   = 'admin/actions.php';
                    $tpl_dir    = file_exists(TEMPLATE_DIR.$tpl_file) ? TEMPLATE_DIR : DEFAULT_TEMPLATE_DIR;

                    include($tpl_dir.$tpl_file);
                    
                ?>
            </div>
		</div>			
	</div>

    </td>
    <td width="325" valign="top" style=""><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="100" valign="top">
        <?php
			$new_photos 	= dbRowsCount('cms_photo_files', 'published=0');
			$new_quests 	= dbRowsCount('cms_faq_quests', 'published=0');
			$new_content 	= dbRowsCount('cms_content', 'published=0');
			$new_catalog 	= dbRowsCount('cms_uc_items', 'on_moderate=1');
		?>
        <?php if ($new_photos || $new_quests || $new_content || $new_catalog){ ?>
            <div class="small_box">
                <div class="small_title">
                    <span class="attention">
                        <strong>��������� �� ���������</strong>
                    </span>
                </div>
                <div style="padding:10px">
                    <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
                      <?php if ($new_content){ ?>
                      <tr>
                        <td width="16"><img src="images/updates/content.gif" width="16" height="16" /></td>
                        <td><a href="index.php?view=content&sort=published">������</a> (<?php echo $new_content; ?>)</td>
                      </tr>
                      <?php } ?>
                      <?php if ($new_photos){ ?>
                      <tr>
                        <td width="16"><img src="images/updates/photos.gif" width="16" height="16" /></td>
                        <td><a href="index.php?view=components&amp;do=config&amp;link=photos&amp;opt=list_photos">����������</a> (<?php echo $new_photos; ?>)</td>
                      </tr>
                      <?php } ?>
                      <?php if ($new_quests){ ?>
                      <tr>
                        <td width="16"><img src="images/updates/quests.gif" width="16" height="16" /></td>
                        <td><a href="index.php?view=components&amp;do=config&amp;link=faq&amp;opt=list_items">�������</a> (<?php echo $new_quests; ?>)</td>
                      </tr>
                      <?php } ?>
                      <?php if ($new_catalog){ ?>
                      <tr>
                        <td width="16"><img src="images/updates/content.gif" width="16" height="16" /></td>
                        <td><a href="index.php?view=components&amp;do=config&amp;link=catalog&amp;opt=list_items&amp;on_moderate=1">������ ��������</a> (<?php echo $new_catalog; ?>)</td>
                      </tr>
                      <?php } ?>
                    </table>
                </div>
            </div>
        <?php } ?>
        <!--
		<div class="small_box">
		  <div class="small_title">���������� InstantCMS</div>
		  	<ul>
                <li><a href="index.php?view=update"><strong>��������� ������� ����������</strong></a></li>
			</ul>
		</div>
        -->
        <?php if ($inCore->isComponentInstalled('rssfeed')){ ?>
        <div class="small_box">
            <div class="small_title">RSS-����� �����</div>
            <div style="padding:10px;">
            <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
              <tr>
                <td width="16"><img src="/images/markers/rssfeed.png" width="16" height="16" /></td>
                <td><a href="/rss/comments/all/feed.rss" id="rss_link">����� ������������ </a></td>
                <td width="16"><img src="/images/markers/rssfeed.png" width="16" height="16" /></td>
                <td><a href="/rss/blogs/all/feed.rss" id="rss_link">����� ������</a></td>
              </tr>
              <tr>
              <tr>
                <td width="16"><img src="/images/markers/rssfeed.png" width="16" height="16" /></td>
                <td><a href="/rss/forum/all/feed.rss" id="rss_link">����� ������ </a></td>
                <td width="16"><img src="/images/markers/rssfeed.png" width="16" height="16" /></td>
                <td><a href="/rss/catalog/all/feed.rss" id="rss_link">����� ��������</a></td>
              </tr>
              <tr>
                <td><img src="/images/markers/rssfeed.png" width="16" height="16" /></td>
                <td><a href="/rss/content/all/feed.rss" id="rss_link">����� ����������</a> </td>
                <td><img src="/admin/images/icons/config.png" width="16" height="16" /></td>
                <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo dbGetField('cms_components', "link='rssfeed'", 'id'); ?>" id="rss_link">��������� RSS </a></td>
              </tr>
            </table>
            </div>
        </div>
        <?php } ?>

		<div class="small_box">
		  <div class="small_title">���������� InstantCMS</div>
            <ul>
              <li><a href="http://www.instantcms.ru/"><strong>����������� ���� InstantCMS</strong></a></li>
              <li><a href="http://www.instantcms.ru/wiki">������������ �� ������ � ��������</a></li>
              <li><a href="http://www.instantcms.ru/forum">����� InstantCMS </a></li>
            </ul>
		</div>
		<div class="small_box">
            <div class="small_title">�������-����������</div>
            <div class="advert_billing"><a href="http://www.instantcms.ru/billing/about.html"><strong>�������</strong></a> &mdash; ������������� �� ����� �����!</div>
            <div class="advert_inmaps"><a href="http://www.instantmaps.ru/"><strong>InstantMaps</strong></a> &mdash; ������� �������� �� �����</div>
            <div class="advert_inshop"><a href="http://www.instantcms.ru/blogs/InstantSoft/professionalnyi-magazin-dlja-InstantCMS.html"><strong>InstantShop</strong></a> &mdash; ���������������� �������</div>
            <div class="advert_invideo"><a href="http://www.instantvideo.ru/"><strong>InstantVideo</strong></a> &mdash; ������ �����-�������</div>
        </div>
		  </td>
      </tr>
    </table></td>
  </tr>
</table>
<?php
	return true;
}
?>
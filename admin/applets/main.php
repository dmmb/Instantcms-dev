<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function newContent($table, $where=''){
    if ($where) { $where = ' AND '.$where; }
    $new = dbGetField($table, "DATE_FORMAT(pubdate, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y'){$where}", 'COUNT(id)');
    return $new;
}

function applet_main(){

    $inCore = cmsCore::getInstance();

	$GLOBALS['cp_page_title'] = 'Главная';

	$new['content'] = (int)newContent('cms_content');
	$new['photos'] 	= (int)newContent('cms_photo_files');
	$new['faq'] 	= (int)newContent('cms_faq_quests');
	$new['board'] 	= (int)newContent('cms_board_items');
	$new['catalog'] = (int)newContent('cms_uc_items');
	$new['forum'] 	= (int)newContent('cms_forum_posts');

?>

<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
  <tr>
    <td width="33%" valign="top" style="">
	<div class="small_box">
	<div class="small_title">Контент сайта</div>
	<div style="padding:8px">
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
      <tr>
        <td><a href="index.php?view=content">Статьи</a> <?php if($new['content']) { ?><span class="new_content">+<?php echo $new['content']?></span><?php } ?></td>
        <td width="20" align="center"><a href="index.php?view=cats&amp;do=add"><img src="/admin/images/mainpage/folder_add.png" alt="Создать раздел" width="16" height="16" border="0" /></a></td>
        <td width="20" align="center"><a href="index.php?view=content&amp;do=add"><img src="/admin/images/mainpage/page_add.png" alt="Создать статью" width="16" height="16" border="0" /></a></td>
        </tr>
      <tr>
	    <?php $cid = dbGetField('cms_components', "link='photos'", 'id'); ?>
        <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>">Фотогалерея</a> <?php if($new['photos']) { ?><span class="new_content">+<?php echo $new['photos']?></span><?php } ?></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_album"><img src="/admin/images/mainpage/folder_add.png" alt="Создать альбом" width="16" height="16" border="0" /></a></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_photo"><img src="/admin/images/mainpage/page_add.png" alt="Загрузить фото" width="16" height="16" border="0" /></a></td>
      </tr>
      <tr>
 	    <?php $cid = dbGetField('cms_components', "link='faq'", 'id'); ?>
        <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>">Вопросы и ответы</a> <?php if($new['faq']) { ?><span class="new_content">+<?php echo $new['faq']?></span><?php } ?></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_cat"><img src="/admin/images/mainpage/folder_add.png" alt="Создать категорию" width="16" height="16" border="0" /></a></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_item"><img src="/admin/images/mainpage/page_add.png" alt="Создать вопрос" width="16" height="16" border="0" /></a></td>
      </tr>
      <tr>
	    <?php $cid = dbGetField('cms_components', "link='board'", 'id'); ?>
        <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>">Доска объявлений</a> <?php if($new['board']) { ?><span class="new_content">+<?php echo $new['board']?></span><?php } ?></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_cat"><img src="/admin/images/mainpage/folder_add.png" alt="Создать рубрику" width="16" height="16" border="0" /></a></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_item"><img src="/admin/images/mainpage/page_add.png" alt="Создать объявление" width="16" height="16" border="0" /></a></td>
      </tr>  
      <tr>
	    <?php $cid = dbGetField('cms_components', "link='catalog'", 'id'); ?>
        <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>">Универсальный каталог</a> <?php if($new['catalog']) { ?><span class="new_content">+<?php echo $new['catalog']?></span><?php } ?></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_cat"><img src="/admin/images/mainpage/folder_add.png" alt="Создать рубрику" width="16" height="16" border="0" /></a></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_item"><img src="/admin/images/mainpage/page_add.png" alt="Создать запись" width="16" height="16" border="0" /></a></td>
      </tr>
      <tr>
	    <?php $cid = dbGetField('cms_components', "link='forum'", 'id'); ?>
        <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=list_forums">Форумы</a> <?php if($new['forum']) { ?><span class="new_content">+<?php echo $new['forum']?></span><?php } ?></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_cat"><img src="/admin/images/mainpage/folder_add.png" alt="Создать категорию" width="16" height="16" border="0" /></a></td>
        <td align="center"><a href="index.php?view=components&amp;do=config&amp;id=<?php echo $cid?>&amp;opt=add_forum"><img src="/admin/images/mainpage/page_add.png" alt="Создать форум" width="16" height="16" border="0" /></a></td>
      </tr>
    </table>	 
	</div>  
	</div>
	<div class="small_box">
		<div class="small_title">Пользователи</div>
		<div style="padding:8px">
		  <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
            <tr>
              <td width="16"><img src="images/icons/hmenu/users.png" width="16" height="16" /></td>
              <td><a href="index.php?view=users">Пользователей</a> &mdash; <?php echo dbRowsCount('cms_users', 'is_deleted=0'); ?></td>
            </tr>
            <tr>
              <td><img src="images/icons/hmenu/users.png" width="16" height="16" /></td>
              <td>Новые за сегодня &mdash; <?php echo (int)dbGetField('cms_users', "DATE_FORMAT(regdate, '%d-%m-%Y') = DATE_FORMAT(NOW(), '%d-%m-%Y') AND is_deleted = 0", 'COUNT(id)'); ?></td>
            </tr>
            <tr>
              <td><img src="images/icons/hmenu/users.png" width="16" height="16" /></td>
              <td>Новые за неделю &mdash; <?php echo (int)dbGetField('cms_users', "regdate >= DATE_SUB(NOW(), INTERVAL 7 DAY)", 'COUNT(id)'); ?></td>
            </tr>
            <tr>
              <td><img src="images/icons/hmenu/users.png" width="16" height="16" /></td>
              <td>Новые за месяц &mdash; <?php echo (int)dbGetField('cms_users', "regdate >= DATE_SUB(NOW(), INTERVAL 1 MONTH)", 'COUNT(id)'); ?></td>
            </tr>
          </table>
          <div style="margin-top:5px;text-align:right">
            Настроить <a href="index.php?view=components&do=config&link=registration">регистрацию</a> | <a href="index.php?view=components&do=config&link=users">профили</a> | <a href="index.php?view=usergroups">группы</a>
          </div>
		</div>
	</div>	
	<div class="small_box">
		<div class="small_title"><strong>Сейчас на сайте</strong></div>
		<div style="font-size:10px;margin:8px;">
		  <?php echo cpWhoOnline();?>
		</div>
	</div>
	</td>
    <td width="33%" valign="top" style="">
	<div class="small_box">
		<div class="small_title"><strong>Последние комментарии</strong></div>
	    <div style="padding:6px;font-size:10px">
			<?php echo cpUpdates();?>
		</div>			
	</div>
    
	<div class="small_box">
		<?php if ($inCore->isComponentInstalled('rssfeed')){ ?>
		<div class="small_title">RSS-ленты сайта</div>
		<div style="padding:10px;font-size:10px">
		<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
		  <tr>
			<td width="16"><img src="/images/markers/rssfeed.png" width="16" height="16" /></td>
			<td><a href="/rss/comments/all/feed.rss" id="rss_link">Лента комментариев </a></td>
		    <td width="16"><img src="/images/markers/rssfeed.png" width="16" height="16" /></td>
		    <td><a href="/rss/blog/all/feed.rss" id="rss_link">Лента блогов</a></td>
		  </tr>
		  <tr>
			<td><img src="/images/markers/rssfeed.png" width="16" height="16" /></td>
			<td><a href="/rss/content/all/feed.rss" id="rss_link">Лента материалов</a> </td>
		    <td><img src="/admin/images/icons/config.png" width="16" height="16" /></td>
		    <td><a href="index.php?view=components&amp;do=config&amp;id=<?php echo dbGetField('cms_components', "link='rssfeed'", 'id'); ?>" id="rss_link">Настройки RSS </a></td>
		  </tr>
		</table>
		</div>
	</div>
	<?php } ?>	</td>
    <td width="33%" valign="top" style=""><table width="100%" border="0" cellspacing="0" cellpadding="0">
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
                    <span style="padding-left:20px;background:url(/admin/images/icons/attention.gif) no-repeat left center">
                        <strong>Материалы на модерацию</strong>
                    </span>
                </div>
                <div style="padding:10px">
                    <table width="100%" border="0" cellspacing="0" cellpadding="2" align="center">
                      <?php if ($new_content){ ?>
                      <tr>
                        <td width="16"><img src="images/updates/content.gif" width="16" height="16" /></td>
                        <td><a href="index.php?view=content&sort=published">Статьи</a> (<?php echo $new_content; ?>)</td>
                      </tr>
                      <?php } ?>
                      <?php if ($new_photos){ ?>
                      <tr>
                        <td width="16"><img src="images/updates/photos.gif" width="16" height="16" /></td>
                        <td><a href="index.php?view=components&amp;do=config&amp;link=photos&amp;opt=list_photos">Фотографии</a> (<?php echo $new_photos; ?>)</td>
                      </tr>
                      <?php } ?>
                      <?php if ($new_quests){ ?>
                      <tr>
                        <td width="16"><img src="images/updates/quests.gif" width="16" height="16" /></td>
                        <td><a href="index.php?view=components&amp;do=config&amp;link=faq&amp;opt=list_items">Вопросы</a> (<?php echo $new_quests; ?>)</td>
                      </tr>
                      <?php } ?>
                      <?php if ($new_catalog){ ?>
                      <tr>
                        <td width="16"><img src="images/updates/content.gif" width="16" height="16" /></td>
                        <td><a href="index.php?view=components&amp;do=config&amp;link=catalog&amp;opt=list_items&amp;on_moderate=1">Записи каталога</a> (<?php echo $new_catalog; ?>)</td>
                      </tr>
                      <?php } ?>
                    </table>
                </div>
            </div>
        <?php } ?>
        <!--
		<div class="small_box">
		  <div class="small_title">Обновления InstantCMS</div>
		  	<ul>
                <li><a href="index.php?view=update"><strong>Проверить наличие обновлений</strong></a></li>
			</ul>
		</div>
        -->
		<div class="small_box">
		  <div class="small_title">Сообщество InstantCMS</div>
            <ul>
              <li><a href="http://www.instantcms.ru/"><strong>Официальный сайт InstantCMS</strong></a></li>
              <li><a href="http://www.instantcms.ru/wiki">Документация по работе с системой</a></li>
              <li><a href="http://www.instantcms.ru/view-forum/menuid-43">Форум InstantCMS </a></li>
            </ul>
		</div>
		<div class="small_box">
            <div class="small_title">Участвуйте в разработке</div>
            <ul>
                <li><a href="http://trac.instantcms.ru/">Сообщайте об ошибках</a></li>
                <li><a href="http://trac.instantcms.ru/wiki/Team/Join">Вступите в команду</a></li>
                <li><a href="http://instantcms.ru/content/0/read80.html">Поддержите проект</a></li>
            </ul>
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
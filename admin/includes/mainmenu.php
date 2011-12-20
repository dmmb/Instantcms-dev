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

defined('VALID_CMS_ADMIN') or die( 'Доступ запрещен' );

$mainmenu = array();

$mainmenu[0] = array();

#### FIRST ROW
$mainmenu[0][0]['title'] = 'Новая статья';
$mainmenu[0][0]['image'] = 'new-content.gif';
$mainmenu[0][0]['link'] = 'index.php?view=content&do=add';

$mainmenu[0][1]['title'] = 'Новый раздел';
$mainmenu[0][1]['image'] = 'new-category.gif';
$mainmenu[0][1]['link'] = 'index.php?view=cats&do=add';

$mainmenu[0][2]['title'] = 'Новый модуль';
$mainmenu[0][2]['image'] = 'new-module.gif';
$mainmenu[0][2]['link'] = 'index.php?view=modules&do=add';

$mainmenu[0][3]['title'] = 'Новое фото';
$mainmenu[0][3]['image'] = 'new-photo.gif';
$mainmenu[0][3]['link'] = 'index.php?view=components&do=config&id=3&opt=add_photo';

#### SECOND ROW
$mainmenu[1][0]['title'] = 'Пользователи';
$mainmenu[1][0]['image'] = 'users.gif';
$mainmenu[1][0]['link'] = 'index.php?view=users';

$mainmenu[1][1]['title'] = 'Фильтры автозамены';
$mainmenu[1][1]['image'] = 'filters.gif';
$mainmenu[1][1]['link'] = 'index.php?view=filters';

$mainmenu[1][2]['title'] = 'Модули сайта';
$mainmenu[1][2]['image'] = 'modules.gif';
$mainmenu[1][2]['link'] = 'index.php?view=modules';

$mainmenu[1][3]['title'] = 'Компоненты сайта';
$mainmenu[1][3]['image'] = 'components.gif';
$mainmenu[1][3]['link'] = 'index.php?view=components';

#### THIRD ROW

$mainmenu[2][0]['title'] = 'Статистика';
$mainmenu[2][0]['image'] = 'statistics.gif';
$mainmenu[2][0]['link'] = 'index.php?view=components&do=config&id=13';

$mainmenu[2][1]['title'] = 'Настройки';
$mainmenu[2][1]['image'] = 'config.gif';
$mainmenu[2][1]['link'] = 'index.php?view=config';

$mainmenu[2][2]['title'] = 'Открыть сайт в новом окне';
$mainmenu[2][2]['image'] = 'site.gif';
$mainmenu[2][2]['link'] = '/';
$mainmenu[2][2]['target'] = '_blank';

$mainmenu[2][3]['title'] = 'Проверить обновления';
$mainmenu[2][3]['image'] = 'updates.gif';
$mainmenu[2][3]['link'] = 'http://www.instantcms.ru/view-content/do-read/id-31/menuid-66';
$mainmenu[2][3]['target'] = '_blank';


##################################################################################################

foreach($mainmenu as $row=>$items){
	echo '<div class="mainmenu_row">';

		foreach($items as $id=>$item){

			if (isset($item['target'])) { $target=$item['target']; } else { $target = ''; }

			echo '<div class="mainmenu_item">';
			
				echo '<table width="100%" border="0" cellspacing="0" cellpadding="5">';
				echo '<tr>
				            <td valign="middle" align="center" height="32">
								<a href="'.$item['link'].'" target="'.$target.'"><img src="images/mainmenu/'.$item['image'].'" border="0"/></a>
							</td>
					  </tr>';
				echo '<tr>
							<td valign="middle" align="center">
								<a href="'.$item['link'].'" target="'.$target.'">'.$item['title'].'</a>
							</td>
					  </tr>';
				echo '</table>';
			
			echo '</div>';

		}

	echo '</div>';
}

##################################################################################################
?>

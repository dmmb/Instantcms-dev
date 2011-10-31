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
	function getProvidersList() {
		$pdir = @opendir(PATH.'/components/search/providers/');
		if(!$pdir){ return false; }
		$provider_array = array();
		while ($provider = readdir($pdir)){
			if (($provider != '.') && ($provider != '..') && !is_dir(PATH.'/components/search/providers/'.$provider)) {
				$provider = substr($provider, 0, strrpos($provider, '.'));
				$provider_array[] = $provider;
			}
		}
		closedir($pdir);
		return $provider_array;
	}
    $inDB   = cmsDatabase::getInstance();
    $inCore->loadLib('tags');
    $inCore->loadModel('search');
    $model = cms_model_search::initModel();

    $opt = $inCore->request('opt', 'str', '');
	$id  = $inCore->request('id', 'int', 0);

    cpAddPathway('Поиск', '?view=components&do=config&id='.$id);
	
	echo '<h3>Поиск</h3>';
	
	$toolmenu = array();

	$toolmenu[0]['icon'] = 'save.gif';
	$toolmenu[0]['title'] = 'Сохранить';
	$toolmenu[0]['link'] = 'javascript:document.optform.submit();';

	$toolmenu[1]['icon'] = 'cancel.gif';
	$toolmenu[1]['title'] = 'Отмена';
	$toolmenu[1]['link'] = '?view=components';

	cpToolMenu($toolmenu);

	if($opt=='save'){

		$cfg = array();
		$cfg['perpage'] = $inCore->request('perpage', 'int', 15);
		$cfg['comp']    = $inCore->request('comp', 'array_str');
		$cfg['search_engine'] = preg_replace('/[^a-z_]/i', '', $inCore->request('search_engine', 'str', ''));

		if(method_exists($model->config['search_engine'], 'getProviderConfig')){
			foreach($model->getProviderConfig() as $key=>$value){
				$cfg[$model->config['search_engine']][$value] = $inCore->request($value, 'str', '');
			}
		}

		$inCore->saveComponentConfig('search', $cfg);

		$inCore->redirectBack();
	}
	
	if ($opt=='dropcache'){
		$model->truncateResults();
	}

?>
<form action="index.php?view=components&do=config&id=<?php echo $id;?>" name="optform" method="post" target="_self">
        <table border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="215"><strong>Результатов на странице: </strong></td>
            <td width="289"><input name="perpage" type="text" id="perpage" value="<?php echo $model->config['perpage'];?>" size="6" /></td>
          </tr>
          <tr>
            <td valign="top"><strong>Провайдер поиска: </strong></td>
            <td valign="top">
                <select name="search_engine" style="width:245px">
                    <option value="" <?php if (!$model->config['search_engine']){?>selected="selected"<?php } ?>>Нативный</option>
                    <?php $provider_array = getProvidersList();
					if($provider_array){
						foreach($provider_array as $provider){
					?>
                    	<option value="<?php echo $provider; ?>" <?php if ($model->config['search_engine']==$provider){?>selected="selected"<?php } ?>><?php echo $provider; ?></option>
                    <?php
						}
					}
					?>
                </select>
            </td>
          </tr>
          <?php if(method_exists($model->config['search_engine'], 'getProviderConfig')){ 
		  foreach($model->getProviderConfig() as $key=>$value){
		  ?>
              <tr>
                <td width="215"><strong><?php echo $key; ?>: </strong></td>
                <td width="289"><input name="<?php echo $value; ?>" type="text" value="<?php echo $model->config[$model->config['search_engine']][$value]; ?>" style="width:245px" /></td>
              </tr>
		  <?php } } ?>
          <tr>
            <td valign="top"><strong>Поиск по компонентам:</strong> </td>
            <td valign="top">
			<?php
				echo '<table border="0" cellpadding="2" cellspacing="0">';
				foreach($model->components as $component){
					echo '<tr>';
					$checked = '';
					if (in_array($component['link'], $model->config['comp'])){
						$checked = 'checked="checked"';	
					}
					echo '<td><input name="comp[]" id="'.$component['link'].'" type="checkbox" value="'.$component['link'].'" '.$checked.'/></td><td><label for="'.$component['link'].'">'.$component['title'].'</label></td>';
					echo '</tr>';
				}
				echo '</table>';
			?></td>
          </tr>
          <tr>
            <td valign="top"><strong>Записей в поисковом кеше:</strong> </td>
            <td valign="top">
			<?php 
				$records = $inDB->rows_count('cms_search', "1=1");
				echo $records . ' шт.';
				if ($records) {
					echo ' | <a href="?view=components&do=config&id='.$id.'&opt=dropcache">Очистить</a>';
				}
			?></td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
        </p>
</form>
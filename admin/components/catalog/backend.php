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

function cpPriceInput($id){
	$inDB = cmsDatabase::getInstance();
	$sql = "SELECT c.view_type as view_type
			FROM cms_uc_cats c, cms_uc_items i
			WHERE i.id = $id AND i.category_id=c.id";
	$rs = dbQuery($sql) ;
	$show = mysql_fetch_assoc($rs);
	$show = $show['view_type'];
	
	if ($show == 'shop'){
		$price = $inDB->get_field('cms_uc_items', 'id='.$id, 'price');
		$price = number_format($price, 2, '.', '');	
		$html = '<input type="text" name="price['.$id.']" value="'.$price.'" id="priceinput"/>';
	} else {
		$html = '&mdash;';
	}

	return $html;
}

    $inCore->loadLib('tags');
    $inCore->loadModel('catalog');
    $model = new cms_model_catalog();

    $inDB = cmsDatabase::getInstance();

    $cfg = $inCore->loadComponentConfig('catalog');
    $opt = $inCore->request('opt', 'str', 'list_cats');

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }

	cpAddPathway('Универсальный каталог', '?view=components&do=config&id='.$_REQUEST['id']);
    echo '<h3>Универсальный каталог</h3>';

    $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/admin/components/catalog/js/common.js"></script>';

//=================================================================================================//
//=================================================================================================//

	$toolmenu = array();
	
	if ($opt=='list_items' || $opt=='list_cats' || $opt=='list_discount'){

        $toolmenu[0]['icon'] = 'newfolder.gif';
        $toolmenu[0]['title'] = 'Новая рубрика';
        $toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat';

        $toolmenu[1]['icon'] = 'newstuff.gif';
        $toolmenu[1]['title'] = 'Новая запись';
        $toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item';

        $toolmenu[2]['icon'] = 'newdiscount.gif';
        $toolmenu[2]['title'] = 'Новый коэффициент';
        $toolmenu[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_discount';

        $toolmenu[3]['icon'] = 'folders.gif';
        $toolmenu[3]['title'] = 'Все рубрики';
        $toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats';

        $toolmenu[4]['icon'] = 'liststuff.gif';
        $toolmenu[4]['title'] = 'Все записи';
        $toolmenu[4]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items';

        $toolmenu[5]['icon'] = 'listdiscount.gif';
        $toolmenu[5]['title'] = 'Все коэффициенты';
        $toolmenu[5]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_discount';

        $toolmenu[6]['icon'] = 'excel.gif';
        $toolmenu[6]['title'] = 'Импорт записей из MS Excel';
        $toolmenu[6]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=import_xls';

        $toolmenu[7]['icon'] = 'config.gif';
        $toolmenu[7]['title'] = 'Настройки';
        $toolmenu[7]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config';

	}

//=================================================================================================//
//=================================================================================================//

	if($opt == 'list_items'){
		$toolmenu[11]['icon'] = 'edit.gif';
		$toolmenu[11]['title'] = 'Редактировать выбранные';
		$toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=edit_item&multiple=1');";

		$toolmenu[12]['icon'] = 'show.gif';
		$toolmenu[12]['title'] = 'Публиковать выбранные';
		$toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=show_item&multiple=1');";

		$toolmenu[13]['icon'] = 'hide.gif';
		$toolmenu[13]['title'] = 'Скрыть выбранные';
		$toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=hide_item&multiple=1');";

		$toolmenu[16]['icon'] = 'saveprices.gif';
		$toolmenu[16]['title'] = 'Сохранить цены';
		$toolmenu[16]['link'] = "javascript:sendForm('index.php?view=components&do=config&id=".$_REQUEST['id']."&opt=saveprices');";	
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt=='list_items' || $opt=='list_cats' || $opt=='list_discount'){
	} else {
	
		$toolmenu[20]['icon'] = 'save.gif';
		$toolmenu[20]['title'] = 'Сохранить';
		$toolmenu[20]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[21]['icon'] = 'cancel.gif';
		$toolmenu[21]['title'] = 'Отмена';
		$toolmenu[21]['link'] = '?view=components&do=config&id='.(int)$_REQUEST['id'];
	
	}

	cpToolMenu($toolmenu);

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'go_import_xls'){
		//get variables
		$cat_id = $inCore->request('cat_id', 'int');
        $rows   = $inCore->request('xlsrows', 'int');
        $sheet   = $inCore->request('xlslist', 'int', 1);
        $cells  = $_REQUEST['cells'];
        $charset = $inCore->request('charset', 'str', 'cp1251');
        //get items config
        $published      = $inCore->request('published', 'int', 0);
        $imgfile        = '';
        $is_comments    = $inCore->request('is_comments', 'int', 0);
        $tags           = $inCore->request('tags', 'str', '');
        $meta_desc      = '';
        $meta_keys      = $tags;
        $canmany        = $inCore->request('canmany', 'int', 0);
        $price          = 0;

        //upload image
        if (isset($_FILES["imgfile"]["name"]) && @$_FILES["imgfile"]["name"]!=''){
            require(PATH.'/includes/graphic.inc.php');
			//generate image file
			$tmp_name = $_FILES["imgfile"]["tmp_name"];
			$imgfile = $_FILES["imgfile"]["name"];
			$path_parts = pathinfo($imgfile);
			$ext = $path_parts['extension'];
			$imgfile = md5($imgfile.time()).'.'.$ext;
			//upload image and insert record in db
			if (@move_uploaded_file($tmp_name, PATH."/images/catalog/$imgfile")){
				@img_resize(PATH."/images/catalog/$imgfile", PATH."/images/catalog/small/$imgfile.jpg", 100, 100);
				@img_resize(PATH."/images/catalog/$imgfile", PATH."/images/catalog/medium/$imgfile.jpg", 250, 250);
			} else { $msg .= 'Ошибка загрузки изображения!'; }
		} else { $imgfile = ''; }

        //process import
		if (isset($_FILES["xlsfile"]["name"]) && @$_FILES["xlsfile"]["name"]!=''){
			//upload xls file
			$tmp_name = $_FILES["xlsfile"]["tmp_name"];
			$file = $_FILES["xlsfile"]["name"];
			$file = PATH."/upload/". md5($file . time()). '.xls';
			if (@move_uploaded_file($tmp_name, $file)){
                $inCore->includeFile('includes/excel/excel_reader2.php');
                $data = new Spreadsheet_Excel_Reader($file, true, $charset);
                //read rows in xls
                for($r=0; $r<$rows; $r++){
                    $fields = array();
                    $title = '';
                    $price = '';

                    //get each cell in row by user coordinates
                    foreach($cells as $cell_id=>$pos){                        
                        if (isset($pos['ignore'])){
                            $celldata = $pos['other'];
                        } else {
                            $celldata = $data->val($r+$pos['row'],$pos['col'],$sheet-1);
                        }

                        if ($cell_id === 'title'){
                            $title = $celldata;
                        } elseif ($cell_id === 'price'){
                            $price = $celldata;
                        } else {
                            $fields[] = $celldata;
                        }
                    }                    

                    $fields = serialize($fields);

                    if ($title && $fields){
                        $sql = "INSERT INTO cms_uc_items (category_id, title, pubdate, published, imageurl, fieldsdata, is_comments, tags, rating, meta_desc, meta_keys, price, canmany)
                                VALUES ($cat_id, '$title', NOW(), '$published', '$imgfile', '$fields', $is_comments, '$tags', 0, '$meta_desc', '$meta_keys', '$price', $canmany)";
                        dbQuery($sql) or die('Ошибка импорта. Проверьте правильность настроек. <a href="javascript:window.history.go(-2)">Назад</a>');

                        if ($tags){
                            $lastid = dbLastId('cms_uc_items');
                            cmsInsertTags($tags, 'catalog', $lastid);
                        }
                    }
                }

                if ($file) @unlink($file);

			} else { $msg .= 'Ошибка загрузки файла Excel!'; }
		} else { $file = ''; }

		header('location:?view=components&do=config&opt=list_items&id='.$_REQUEST['id']);
	}

//=================================================================================================//
//=================================================================================================//

	if($opt=='saveprices'){
		$prices = $_REQUEST['price'];		
		if (is_array($prices)){		
			foreach($prices as $id=>$price){
				$price = str_replace(',', '.', $price);
				$price = number_format($price, 2, '.', '');
				$sql = "UPDATE cms_uc_items SET price='$price' WHERE id = $id";
				dbQuery($sql);
			}		
		}
		header('location:'.$_SERVER['HTTP_REFERER']);
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'show_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ 
                dbShow('cms_uc_items', $_REQUEST['item_id']);
                dbQuery('UPDATE cms_uc_items SET on_moderate = 0 WHERE id='.$_REQUEST['item_id']);
            }
			echo '1'; exit;
		} else {
			dbShowList('cms_uc_items', $_REQUEST['item']);
            foreach($_REQUEST['item'] as $k=>$id){
                dbQuery('UPDATE cms_uc_items SET on_moderate = 0 WHERE id='.$id);
            }
			header('location:'.$_SERVER['HTTP_REFERER']);
		}			
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'hide_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_uc_items', $_REQUEST['item_id']);  }
			echo '1'; exit;
		} else {
			dbHideList('cms_uc_items', $_REQUEST['item']);				
			header('location:'.$_SERVER['HTTP_REFERER']);			
		}			
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'submit_item'){	
        $inCore->includeGraphics();
        
        $item = array();

		//get variables
		$item['cat_id']         = $inCore->request('cat_id', 'int', 0);
		$item['title']          = $inCore->request('title', 'str');
		$item['published']      = $inCore->request('published', 'int', 0);
		
        $item['fdata']          = $_REQUEST['fdata'];
		foreach($item['fdata'] as $key=>$value) { $item['fdata'][$key] = trim($value); }
		
        $item['is_comments']    = $inCore->request('is_comments', 'int', 0);
		$item['meta_desc']      = $inCore->request('meta_desc', 'str');
		$item['meta_keys']      = $inCore->request('meta_keys', 'str');
		$item['tags']           = $inCore->request('tags', 'str');

		$pubdate                = $inCore->request('pubdate', 'str');
		$date                   = explode('.', $pubdate);
		$item['pubdate']        = $date[2] . '-' . $date[1] . '-' . $date[0] . ' '.date('H:i');

        $item['price']          = 0;
        $item['canmany']        = 1;

		if ($inCore->inRequest('price')) {
            $canmany            = $inCore->request('canmany', 'int', 0);
			$price              = $inCore->request('price', 'str', '0');
			$price              = str_replace(',', '.', $price);
			$price              = round($price, 2);
            $item['price']      = $price;
            $item['canmany']    = $canmany;
		}
				
		//get fields data
		$item['fields']     = serialize($item['fdata']);
		$item['fields']     = mysql_escape_string($item['fields']);

        $item['file']   = '';

		if (isset($_FILES["imgfile"]["name"]) && @$_FILES["imgfile"]["name"]!=''){			
			//generate image file			
			$tmp_name = $_FILES["imgfile"]["tmp_name"];
			$file = $_FILES["imgfile"]["name"];			
			$path_parts = pathinfo($file);
			$ext = $path_parts['extension'];
			if(strstr($ext, 'php')) { die(); }
			$file = md5($file.time()).'.'.$ext;
            $item['file'] = $file;
			//upload image and insert record in db		
			if (@move_uploaded_file($tmp_name, PATH."/images/catalog/$file")){
				@img_resize(PATH."/images/catalog/$file", PATH."/images/catalog/small/$file.jpg", 100, 100);
				@img_resize(PATH."/images/catalog/$file", PATH."/images/catalog/medium/$file.jpg", 250, 250);
                @chmod(PATH."/images/catalog/$file", 0644);
				@chmod(PATH."/images/catalog/small/$file.jpg", 0644);
				@chmod(PATH."/images/catalog/medium/$file.jpg", 0644);
			}
		}
		
        $model->addItem($item);
        $inCore->redirect('?view=components&do=config&opt=list_items&id='.$_REQUEST['id']);
	}

//=================================================================================================//
//=================================================================================================//
	
	if ($opt == 'renew_item'){
		if($inCore->inRequest('item_id')) {
			$id = $inCore->request('item_id', 'int');
			$model->renewItem($id);
		}
		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
	}

//=================================================================================================//
//=================================================================================================//
	
	if ($opt == 'update_item'){
		if($inCore->inRequest('item_id')) {
			$id = $inCore->request('item_id', 'int');
			
			$item['cat_id']     = $inCore->request('cat_id', 'int');
			$item['title']      = $inCore->request('title', 'str');
			$item['published']  = $inCore->request('published', 'int');

			$fdata              = $_REQUEST['fdata'];
			foreach($fdata as $key=>$value) { $fdata[$key] = trim($value); }
			$fields             = serialize($fdata);
			$item['fields']     = mysql_escape_string($fields);

			$item['is_comments'] = $inCore->request('is_comments', 'int', 0);


			$item['meta_desc']  = $inCore->request('meta_desc', 'str');
			$item['meta_keys']  = $inCore->request('meta_keys', 'str');
			$item['tags']       = $inCore->request('tags', 'str');

			$pubdate            = $inCore->request('pubdate', 'str');
			$date               = explode('.', $pubdate);
			$pubdate            = $date[2] . '-' . $date[1] . '-' . $date[0] . ' ' .date('H:i');
            $item['pubdate']    = $pubdate;

			$item['price']      = 0;
            $item['canmany']    = 1;

            $item['imageurl']   = $inDB->get_field('cms_uc_items', "id={$id}", 'imageurl');

            if ($inCore->inRequest('price')) {
                $canmany        = $inCore->request('canmany', 'int', 0);
                $price          = $inCore->request('price', 'str', '0');
                $price          = str_replace(',', '.', $price);
                $price          = round($price, 2);
                $item['price']  = $price;
                $item['canmany']= $canmany;
            }

			if (isset($_FILES["imgfile"]["name"]) && @$_FILES["imgfile"]["name"]!=''){
                
                $inCore->includeGraphics();
				$tmp_name   = $_FILES["imgfile"]["tmp_name"];
                $imageurl   = $model->getItemImageUrl($id);

				if($imageurl){
					@chmod(PATH."/images/catalog/$file", 0777);
					@chmod(PATH."/images/catalog/small/$file.jpg", 0777);
					@chmod(PATH."/images/catalog/medium/$file.jpg", 0777);
					@unlink(PATH."/images/catalog/$imageurl");
					@unlink(PATH."/images/catalog/small/$imageurl.jpg");
					@unlink(PATH."/images/catalog/medium/$imageurl.jpg");
				}

                $file               = $_FILES["imgfile"]["name"];
                $path_parts         = pathinfo($file);
                $ext                = $path_parts['extension'];
				if(strstr($ext, 'php')) { die(); }
                $file               = md5($file.time()).'.'.$ext;
                
                $item['imageurl']   = $file;

				if (@move_uploaded_file($tmp_name, PATH."/images/catalog/$file")){
                    //create image thumbnails
					if ( $cfg['watermark'] ) { @img_add_watermark(PATH."/images/catalog/$file"); }
                    @img_resize(PATH."/images/catalog/$file", PATH."/images/catalog/small/$file.jpg", 100, 100);
                    @img_resize(PATH."/images/catalog/$file", PATH."/images/catalog/medium/$file.jpg", 250, 250);
                    @chmod(PATH."/images/catalog/$file", 0644);
                    @chmod(PATH."/images/catalog/small/$file.jpg", 0644);
                    @chmod(PATH."/images/catalog/medium/$file.jpg", 0644);
				} else { $msg = 'Ошибка загрузки изображения!'; }				
			}					

			$model->updateItem($id, $item);
		}
		if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
			$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
		} else {
			$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item');
		}
	}

//=================================================================================================//
//=================================================================================================//

	if($opt == 'delete_item'){
		if ($inCore->inRequest('item_id')){
			$id = $inCore->request('item_id', 'int');			
			$model->deleteItem($id);
		}
		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
	}

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'submit_discount'){
        $item['title']      = $inCore->request('title', 'str');
        $item['cat_id']     = $inCore->request('cat_id', 'int');
        $item['sign']       = $inCore->request('sign', 'str');
        $item['value']      = $inCore->request('value', 'str');
        $item['unit']       = $inCore->request('unit', 'str');
        $item['if_limit']   = $inCore->request('if_limit', 'int', 0);

		$model->addDiscount($item);

		$inCore->redirect('?view=components&do=config&opt=list_discount&id='.$_REQUEST['id']);
	}

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'update_discount'){
		if($inCore->inRequest('item_id')) {
			$id = $inCore->request('item_id', 'int');
            $item['title']  = $inCore->request('title', 'str');
            $item['cat_id'] = $inCore->request('cat_id', 'int');
            $item['sign']   = $inCore->request('sign', 'str');
            $item['value']  = $inCore->request('value', 'str');
            $item['unit']   = $inCore->request('unit', 'str');
            $item['if_limit']   = $inCore->request('if_limit', 'int', 0);
            
            $model->updateDiscount($id, $item);

            $inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_discount');
		}
	}

//=================================================================================================//
//=================================================================================================//

    if($opt == 'delete_discount'){
		if($inCore->inRequest('item_id')) {
			$id = $inCore->request('item_id', 'int');
            $model->deleteDiscount($id);
		}
		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_discount');
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'show_cat'){
		if($inCore->inRequest('item_id')) {
			$id = $inCore->request('item_id', 'int');
			$sql = "UPDATE cms_uc_cats SET published = 1 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'hide_cat'){
		if($inCore->inRequest('item_id')) {
			$id = $inCore->request('item_id', 'int');
			$sql = "UPDATE cms_uc_cats SET published = 0 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'submit_cat'){
		$cat['parent_id']      = $inCore->request('parent_id', 'int');
		$cat['title']          = $inCore->request('title', 'str');
		$cat['description']    = $inCore->request('description', 'html');
        $cat['description']    = $inDB->escape_string($cat['description']);
		$cat['published']      = $inCore->request('published', 'int');
		$cat['view_type']      = $inCore->request('view_type', 'str');
		$cat['fields_show']    = $inCore->request('fieldsshow', 'int');
		$cat['showmore']       = $inCore->request('showmore', 'int');
		$cat['perpage']        = $inCore->request('perpage', 'int');
		$cat['showtags']       = $inCore->request('showtags', 'int');
        $cat['showabc']        = $inCore->request('showabc', 'int');
		$cat['showsort']       = $inCore->request('showsort', 'int');
		$cat['is_ratings']     = $inCore->request('is_ratings', 'int');
        $cat['filters']        = $inCore->request('filters', 'int');
		$cat['orderby']        = $inCore->request('orderby', 'str');
		$cat['ordetto']        = $inCore->request('ordetto', 'str');

        $cat['shownew']        = $inCore->request('shownew', 'int');
        $cat['newint']         = $inCore->request('int_1', 'int') . ' ' . $inCore->request('int_2', 'str');

        if ($inCore->request('copy_parent_struct')){
            $cat['fields'] = $inDB->get_field('cms_uc_cats', "id={$cat['parent_id']}", 'fieldsstruct');
        } else {
            $fstruct = $_REQUEST['fstruct'];
            foreach ($fstruct as $key=>$value) {
				$value = trim($value);
    			if ($value=='') { unset($fstruct[$key]); }
        		else {
					if ($_REQUEST['fformat'][$key]=='html') { $fstruct[$key] .= '/~h~/'; }
					if ($_REQUEST['fformat'][$key]=='link') { $fstruct[$key] .= '/~l~/'; }
					if ($_REQUEST['flink'][$key]) { $fstruct[$key] .= '/~m~/'; }
				 }
            }
            $cat['fields'] = serialize($fstruct);
        }

		$cat['fields'] = $inDB->escape_string($cat['fields']);

        $cat['is_public']   = $inCore->request('is_public', 'int', 0);
        $cat['can_edit']    = $inCore->request('can_edit', 'int', 0);

        $cat['cost']        = $inCore->request('cost', 'str', '');
        if (!is_numeric($cat['cost'])) { $cat['cost'] = ''; }

        $cat['id'] = $model->addCategory($cat);

        if ($cat['is_public']){
			$showfor = $_REQUEST['showfor'];
			if (sizeof($showfor)>0){
                $model->setCategoryAccess($cat['id'], $showfor);
            }
		}
        
		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
    }

//=================================================================================================//
//=================================================================================================//
	
	if($opt == 'delete_cat'){
		if($inCore->inRequest('item_id')) {
			$id = $inCore->request('item_id', 'int');
            $model->deleteCategory($id);
		}
		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'update_cat'){
		if($inCore->inRequest('item_id')) {
			$id = $inCore->request('item_id', 'int');
			
			$cat['parent_id']      = $inCore->request('parent_id', 'int');
            $cat['title']          = $inCore->request('title', 'str');
            $cat['description']    = $inCore->request('description', 'html');
            $cat['description']    = $inDB->escape_string($cat['description']);
            $cat['published']      = $inCore->request('published', 'int');
            $cat['view_type']      = $inCore->request('view_type', 'str');
            $cat['fields_show']    = $inCore->request('fieldsshow', 'int');
            $cat['showmore']       = $inCore->request('showmore', 'int');
            $cat['perpage']        = $inCore->request('perpage', 'int');
            $cat['showtags']       = $inCore->request('showtags', 'int');
            $cat['showabc']        = $inCore->request('showabc', 'int');
            $cat['showsort']       = $inCore->request('showsort', 'int');
            $cat['is_ratings']     = $inCore->request('is_ratings', 'int');
            $cat['filters']        = $inCore->request('filters', 'int');
            $cat['orderby']        = $inCore->request('orderby', 'str');
            $cat['orderto']        = $inCore->request('orderto', 'str');

            $cat['shownew']        = $inCore->request('shownew', 'int');
            $cat['newint']         = $inCore->request('int_1', 'int') . ' ' . $inCore->request('int_2', 'str');

            if ($inCore->request('copy_parent_struct')){
                $cat['fields'] = $inDB->get_field('cms_uc_cats', "id={$cat['parent_id']}", 'fieldsstruct');
            } else {
                $fstruct = $_REQUEST['fstruct'];
                foreach ($fstruct as $key=>$value) {
					$value = trim($value);
                    if ($value=='') { unset($fstruct[$key]); }
                    else {
                        if ($_REQUEST['fformat'][$key]=='html') { $fstruct[$key] .= '/~h~/'; }
                        if ($_REQUEST['fformat'][$key]=='link') { $fstruct[$key] .= '/~l~/'; }
                        if ($_REQUEST['flink'][$key]) { $fstruct[$key] .= '/~m~/'; }
                     }
                }
                $cat['fields'] = serialize($fstruct);
            }

			$cat['fields'] = $inDB->escape_string($cat['fields']);

            $cat['is_public'] = $inCore->request('is_public', 'int', 0);
            $cat['can_edit']  = $inCore->request('can_edit', 'int', 0);

            $cat['cost']      = $inCore->request('cost', 'str', '');
            if (!is_numeric($cat['cost'])) { $cat['cost'] = ''; }

            if ($cat['is_public']){
                $showfor = $_REQUEST['showfor'];
                if (sizeof($showfor)>0){
                    $model->setCategoryAccess($id, $showfor);
                }
            } else {
                $model->clearCategoryAccess($id);
            }

            $model->updateCategory($id, $cat);
							
			$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		}
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'list_cats'){

        cpAddPathway('Рубрики каталога', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		echo '<h3>Рубрики каталога</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$fields[3]['title'] = 'Родитель';	$fields[3]['field'] = 'parent_id';	$fields[3]['width'] = '200';
		$fields[3]['prc'] = 'cpCatalogCatById';

		$fields[4]['title'] = 'Показ';		$fields[4]['field'] = 'published';	$fields[4]['width'] = '100';
		$fields[4]['do'] = 'opt'; $fields[4]['do_suffix'] = '_cat';

		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Просмотр содержимого';
		$actions[0]['icon']  = 'explore.gif';
		$actions[0]['link']  = 'javascript:openCat(%id%)';

		$actions[1]['title'] = 'Редактировать';
		$actions[1]['icon']  = 'edit.gif';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$actions[2]['title'] = 'Копировать';
		$actions[2]['icon']  = 'copy.gif';
		$actions[2]['link']  = "javascript:copyCat(".$_REQUEST['id'].", %id%);";

		$actions[3]['title'] = 'Удалить';
		$actions[3]['icon']  = 'delete.gif';
		$actions[3]['confirm'] = 'Удалить рубрику из каталога?';
		$actions[3]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_cat&item_id=%id%';
				
		//Print table
		echo '<script type="text/javascript">function openCat(id){ $("#catform input").val(id); $("#catform").submit(); } </script>';
		echo '<form id="catform" method="post" action="index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items"><input type="hidden" id="filter[category_id]" name="filter[category_id]" value=""></form>';
		
        cpListTable('cms_uc_cats', $fields, $actions, 'parent_id>0', 'NSLeft');

	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'list_items'){
		$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/admin/components/catalog/js/common.js"></script>';
	
		cpAddPathway('Записи', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');

        if ($inCore->inRequest('on_moderate')){
            echo '<h3>Записи на модерацию</h3>';
        } else {
            echo '<h3>Записи</h3>';
        }
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';

        if ($inCore->inRequest('on_moderate')){
            $fields[1]['link'] = '/catalog/item%id%.html';
        } else {
            $fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';
        }

		$fields[1]['filter'] = 15;
		
		$fields[2]['title'] = 'Показ';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';
		$fields[2]['do'] = 'opt'; $fields[2]['do_suffix'] = '_item';
		
		$fields[3]['title'] = 'Рубрика';	$fields[3]['field'] = 'category_id';$fields[3]['width'] = '200';
		$fields[3]['prc'] = 'cpCatalogCatById';  $fields[3]['filter'] = 1;  $fields[3]['filterlist'] = cpGetList('cms_uc_cats');

		$fields[4]['title'] = 'Цена';		$fields[4]['field'] = 'id';	$fields[4]['width'] = '150';
		$fields[4]['prc'] = 'cpPriceInput';
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Обновить дату';
		$actions[0]['icon']  = 'date.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=renew_item&item_id=%id%';

		$actions[1]['title'] = 'Редактировать';
		$actions[1]['icon']  = 'edit.gif';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

		$actions[2]['title'] = 'Копировать';
		$actions[2]['icon']  = 'copy.gif';
		$actions[2]['link']  = "javascript:copyItem(".$_REQUEST['id'].", %id%);";

		$actions[3]['title'] = 'Удалить';
		$actions[3]['icon']  = 'delete.gif';
		$actions[3]['confirm'] = 'Удалить запись из каталога?';
		$actions[3]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_item&item_id=%id%';

        if ($inCore->inRequest('on_moderate')){ $where = 'on_moderate=1'; } else { $where = ''; }

		//Print table
		cpListTable('cms_uc_items', $fields, $actions, $where);
	}

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'list_discount'){

        cpAddPathway('Коэффициенты', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_discount');
		echo '<h3>Коэффициенты</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_discount&item_id=%id%';

		$fields[3]['title'] = 'Категория';	$fields[3]['field'] = 'cat_id';     $fields[3]['width'] = '200';
		$fields[3]['prc'] = 'cpCatalogCatById';

		$fields[4]['title'] = 'Тип';       $fields[4]['field'] = 'sign';		$fields[4]['width'] = '40';

        $fields[5]['title'] = 'Размер';     $fields[5]['field'] = 'value';      $fields[5]['width'] = '80';

        $fields[6]['title'] = 'Единицы';        $fields[6]['field'] = 'unit';       $fields[6]['width'] = '80';

        $fields[7]['title'] = 'Лимит';     $fields[7]['field'] = 'if_limit';      $fields[7]['width'] = '80';

		//ACTIONS
		$actions = array();
		$actions[1]['title'] = 'Редактировать';
		$actions[1]['icon']  = 'edit.gif';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_discount&item_id=%id%';

        $actions[3]['title'] = 'Удалить';
		$actions[3]['icon']  = 'delete.gif';
		$actions[3]['confirm'] = 'Удалить коэффициент?';
		$actions[3]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_discount&item_id=%id%';

		//Print table
		cpListTable('cms_uc_discount', $fields, $actions);
	}

//=================================================================================================//
//=================================================================================================//
	
	if ($opt == 'copy_item'){
		$item_id    = $inCore->request('item_id', 'int');
		$copies     = $inCore->request('copies', 'int');
		if ($copies){
			$model->copyItem($item_id, $copies);
		}
		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'copy_cat'){
		$item_id    = $inCore->request('item_id', 'int');
		$copies     = $inCore->request('copies', 'int');
		if ($copies){
            $model->copyCategory($item_id, $copies);
		}
		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'add_item' || $opt == 'edit_item'){
	   	$inCore->includeFile('includes/jwtabs.php');
		$GLOBALS['cp_page_head'][] = jwHeader();
		if ($opt=='add_item'){
            echo '<h3>Добавить запись</h3>';
            cpAddPathway('Добавить запись', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item');
		} else {
                     if(isset($_REQUEST['multiple'])){
                        if (isset($_REQUEST['item'])){
                            $_SESSION['editlist'] = $_REQUEST['item'];
                        } else {
                            echo '<p class="error">Нет выбранных объектов!</p>';
                            return;
                        }
                     }

                     $ostatok = '';

                     if (isset($_SESSION['editlist'])){
                        $id = array_shift($_SESSION['editlist']);
                        if (sizeof($_SESSION['editlist'])==0) { unset($_SESSION['editlist']); } else
                        { $ostatok = '(На очереди: '.sizeof($_SESSION['editlist']).')'; }
                     } else { $id = $_REQUEST['item_id']; }


                     $sql = "SELECT i.*, c.view_type as viewtype, DATE_FORMAT(i.pubdate, '%d.%m.%Y') as pubdate
                             FROM cms_uc_items i, cms_uc_cats c
                             WHERE i.id = $id AND i.category_id = c.id
                             LIMIT 1";
                     $result = dbQuery($sql) ;
                     if (mysql_num_rows($result)){
                        $mod = mysql_fetch_assoc($result);
                        $fdata = unserialize($mod['fieldsdata']);
                     }


                     echo '<h3>'.$mod['title'].' '.$ostatok.'</h3>';
                     cpAddPathway('Записи', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
                     cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id='.$id);
			}

//=================================================================================================//
//=================================================================================================//

		if ($opt == 'edit_item' || isset($_REQUEST['cat_id'])) {		
			if ($opt=='edit_item') { 
				$cat_id  = $mod['category_id'];
				$is_shop = ($mod['viewtype']=='shop');
			} else { 
				$cat_id  = $_REQUEST['cat_id'];
				$is_shop = ($inDB->get_field('cms_uc_cats', 'id='.$cat_id, 'view_type')=='shop');
			}

            $sql = "SELECT title, fieldsstruct FROM cms_uc_cats WHERE id = $cat_id"; $result = dbQuery($sql);
            $cat = mysql_fetch_assoc($result);
            $fstruct = unserialize($cat['fieldsstruct']);

            if (isset($mod['price'])){
                $mod['price'] = number_format($mod['price'], 2, '.', '');
            } else {
                $mod['price'] = '0.00';
            }

		?>

        <?php cpCheckWritable('/images/catalog', 'folder'); ?>
        <?php cpCheckWritable('/images/catalog/medium', 'folder'); ?>
        <?php cpCheckWritable('/images/catalog/small', 'folder'); ?>

        <form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
            <table class="proptable" width="100%" cellpadding="15" cellspacing="2">
                <tr>

                    <!-- главная ячейка -->
                    <td valign="top">

                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td valign="top">
                                    <div><strong>Название записи</strong></div>
                                    <div>
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td><input name="title" type="text" id="title" style="width:98%" value="<?php echo htmlspecialchars($mod['title']);?>" /></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td width="135" valign="top">
                                    <div><strong>Дата публикации</strong></div>
                                    <div>
                                        <input name="pubdate" type="text" id="pubdate" style="width:100px" <?php if(@!$mod['pubdate']) { echo 'value="'.date('Y-m-d').'"'; } else { echo 'value="'.$mod['pubdate'].'"'; } ?>/>
                                        <?php
                                            //include javascript
                                            $GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/jquery.js"></script>';
                                            $GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/datepicker/date_ru_win1251.js"></script>';
                                            $GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/datepicker/datepicker.js"></script>';
                                            $GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/datepicker/datepicker.css" rel="stylesheet" type="text/css" />';
                                            if (@!$mod['pubdate']){
                                                $GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#pubdate\').datePicker({startDate:\'01/01/1996\'}).val(new Date().asString()).trigger(\'change\');});</script>';
                                            } else {
                                                $GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#pubdate\').datePicker({startDate:\'01/01/1996\'}).val(\''.$mod['pubdate'].'\').trigger(\'change\');});</script>';
                                            }
                                        ?>
                                        <input type="hidden" name="olddate" value="<?php echo @$mod['pubdate']?>"/>
                                    </div>
                                </td>
                                <?php if ($is_shop){ ?>
                                <td width="130" valign="top">
                                    <div><strong>Цена</strong></div>
                                    <div>
                                        <input name="price" type="text" id="price" style="width:125px" value="<?php echo @$mod['price'];?>"/>
                                    </div>
                                </td>
                                <?php } ?>
                            </tr>
                        </table>

                        <?php

                        foreach($fstruct as $key=>$value) {
                            if (strstr($value, '/~h~/')) { $ftype = 'html'; $value=str_replace('/~h~/', '', $value); }
                            elseif (strstr($value, '/~l~/')) { $ftype = 'link'; $value=str_replace('/~l~/', '', $value); } else { $ftype='text'; }

                            if (strstr($value, '/~m~/')) { $makelink = true; $value=str_replace('/~m~/', '', $value); }
                            else { $makelink = false; }

                        ?>
                        <div>
                            <strong><?php echo stripslashes($value); ?></strong>
                            <?php if ($makelink) { echo ' <span class="hinttext" style="float:right">Через запятую, если несколько</span>'; } ?>
                        </div>
                        <div>
                            <?php if ($ftype=='link' || $ftype == 'text') { ?>
                                <input style="width:99%" name="fdata[<?php echo $key?>]" type="text" id="fdata[]" <?php if (@$fdata) { echo 'value="'.htmlspecialchars(strip_tags(stripslashes($fdata[$key]))).'"';} ?>/>
                            <?php } else { ?>
                                    <?php
                                        if (@$fdata[$key]) { $fdata[$key] = stripslashes($fdata[$key]); }
                                        $inCore->insertEditor('fdata['.$key.']', stripslashes($fdata[$key]), '220', '100%');
                                    ?>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <div><strong>Теги записи</strong></div>
                        <div><input name="tags" type="text" id="tags" style="width:99%" value="<?php if (isset($mod['id'])) { echo cmsTagLine('catalog', $mod['id'], false); } ?>" /></div>

                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                            <?php if ($is_shop) { ?>
                            <tr>
                                <td width="20"><input type="checkbox" name="canmany" id="canmany" value="1" <?php if (@$mod['canmany']) { echo 'checked="checked"'; } ?>/> </td>
                                <td><label for="canmany"><strong>Разрешить выбор количества при заказе этого товара</strong></label></td>
                            </tr>
                            <?php } ?>
                        </table>

                    </td>

                    <!-- боковая ячейка -->
                    <td width="300" valign="top" style="background:#ECECEC;">

                        <?php ob_start(); ?>

                        {tab=Публикация}

                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                            <tr>
                                <td width="20"><input type="checkbox" name="published" id="published" value="1" <?php if ($mod['published'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                                <td><label for="published"><strong>Публиковать запись</strong></label></td>
                            </tr>
                        </table>

                        <div style="margin-top:15px">
                            <strong>Рубрика</strong>
                        </div>
                        <div>
                            <input type="text" disabled="disabled" value="<?php echo $cat['title']; ?>" style="width:100%" />
                            <input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>" />
                        </div>

                        <div style="margin-top:15px"><strong>Фотография</strong></div>
                        <div style="margin-bottom:10px">
                            <?php
                                if ($opt=='edit_item'){
                                    if (file_exists(PATH.'/images/catalog/small/'.$mod['imageurl'].'.jpg')){
                            ?>
                            <div style="margin-top:3px;margin-bottom:3px;padding:10px;border:solid 1px gray;text-align:center">
                                <img src="/images/catalog/small/<?php echo $mod['imageurl']; ?>.jpg" border="0" />
                            </div>
                            <?php
                                    }
                                }
                            ?>
                            <input type="file" name="imgfile" style="width:100%" />
                        </div>

                        <div style="margin-top:25px"><strong>Параметры публикации</strong></div>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                            <tr>
                                <td width="20"><input type="checkbox" name="is_comments" id="is_comments" value="1" <?php if ($mod['is_comments'] || $opt=='add_item') { echo 'checked="checked"'; } ?>/></td>
                                <td><label for="is_comments">Разрешить комментарии</label></td>
                            </tr>
                        </table>

                        {tab=SEO}

                        <div style="margin-top:5px">
                            <strong>Ключевые слова</strong><br/>
                            <span class="hinttext">Через запятую, 10-15 слов</span>
                        </div>
                        <div>
                             <textarea name="meta_keys" style="width:97%" rows="2" id="meta_keys"><?php echo @$mod['meta_keys'];?></textarea>
                        </div>

                        <div style="margin-top:20px">
                            <strong>Описание</strong><br/>
                            <span class="hinttext">Не более 250 символов</span>
                        </div>
                        <div>
                             <textarea name="meta_desc" style="width:97%" rows="4" id="meta_desc"><?php echo @$mod['meta_desc'];?></textarea>
                        </div>

                        {/tabs}

                        <?php echo jwTabs(ob_get_clean()); ?>

                    </td>

                </tr>
            </table>
            <p>
                <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_item') { echo 'value="Добавить запись"'; } else { echo 'value="Сохранить запись"'; } ?> />
                <input name="back2" type="button" id="back2" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
                <input name="opt" type="hidden" id="do" <?php if ($opt=='add_item') { echo 'value="submit_item"'; } else { echo 'value="update_item"'; } ?> />
                <?php
                    if ($opt=='edit_item'){
                        echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
                    }
                ?>
            </p>
        </form>

            <?php
		} else {
					echo '<h4>Выберите рубрику:</h4>';

					$sql = "SELECT id, title, NSLeft, NSLevel, parent_id
                            FROM cms_uc_cats
                            WHERE parent_id > 0
                            ORDER BY NSLeft";
					$result = dbQuery($sql);
					
					if (mysql_num_rows($result)>0){
                        echo '<div style="padding:10px">';
                            while ($cat = mysql_fetch_assoc($result)){
                                echo '<div style="padding:2px;padding-left:18px;margin-left:'.(($cat['NSLevel']-1)*15).'px;background:url(/admin/images/icons/hmenu/cats.png) no-repeat">
                                          <a href="?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item&cat_id='.$cat['id'].'">'.$cat['title'].'</a>
                                      </div>';
                            }
                        echo '</div>';
					}
					
		}
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'add_cat' || $opt == 'edit_cat'){

	   	require('../includes/jwtabs.php');
		$GLOBALS['cp_page_head'][] = jwHeader();

		if ($opt=='add_cat'){
			 echo '<h3>Добавить рубрику</h3>';
			 cpAddPathway('Добавить рубрику', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat');	 
			} else {
				 if(isset($_REQUEST['item_id'])){
					 $id = $_REQUEST['item_id'];
					 $sql = "SELECT * FROM cms_uc_cats WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
						$fstruct = unserialize($mod['fieldsstruct']);
					 }
				 }
				
				 echo '<h3>Рубрика: '.$mod['title'].'</h3>';
	 	 		 cpAddPathway('Рубрики каталога', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
				 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$_REQUEST['item_id']);	 
			}
			?>

            <form id="addform" name="addform" method="post" action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" enctype="multipart/form-data">
                <table class="proptable" width="100%" cellpadding="15" cellspacing="2">
                    <tr>

                        <!-- главная ячейка -->
                        <td valign="top">

                            <div><strong>Название рубрики</strong></div>
                            <div><input name="title" type="text" id="title" style="width:99%" value="<?php echo htmlspecialchars($mod['title']);?>" /></div>

                            <div style="margin-top:10px"><strong>Характеристики записей</strong></div>

                            <div style="margin-top:2px;margin-bottom:12px">
                                <div><span class="hinttext">
                                    Введите названия полей, которые нужно будет заполнять при добавлении записей в эту рубрику каталога.
                                </span></div>
                                <div><span class="hinttext">
                                    Автопоиск разбивает текст характеристики на отдельные слова и каждое слово делает ссылкой.
                                    Это позволяет пользователям искать записи с одинаковыми характеристиками одним кликом.
                                </span></div>
                            </div>

                            <div>
                                <script type="text/javascript">
                                    function toggleFields(){
                                        var copy = $('#copy_parent_struct').attr('checked');

                                        if (copy){
                                            $('.field').attr('disabled', 'disabled');
                                            $('.fformat').attr('disabled', 'disabled');
                                            $('.flink').attr('disabled', 'disabled');
                                        } else {
                                            $('.field').attr('disabled', '');
                                            $('.fformat').attr('disabled', '');
                                            $('.flink').attr('disabled', '');
                                        }
                                    }
                                </script>
                                <table cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td width="16"><input type="checkbox" id="copy_parent_struct" name="copy_parent_struct" onchange="toggleFields()" value="1" /></td>
                                        <td>
                                            <label for="copy_parent_struct">Скопировать характеристики родительской рубрики</label>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <?php for($f=0; $f<10; $f++) { ?>
                                <?php
                                    if(@$fstruct[$f]) {
                                        if (strstr($fstruct[$f], '/~h~/')) {
                                            $ftype = 'html';  $fstruct[$f] = str_replace('/~h~/', '', $fstruct[$f]);
                                        } elseif(strstr($fstruct[$f], '/~l~/')) { $ftype = 'link';  $fstruct[$f] = str_replace('/~l~/', '', $fstruct[$f]);} else { $ftype = 'text'; }

                                        if (strstr($fstruct[$f], '/~m~/')) {
                                            $makelink = true;  $fstruct[$f] = str_replace('/~m~/', '', $fstruct[$f]);
                                        } else { $makelink = false; }
                                    }
                                ?>
                                <tr>
                                    <td width="105" style="padding-bottom:4px">
                                        <select name="fformat[]" class="fformat" style="width:100px">
                                            <option value="text" <?php if(@$fstruct[$f]) { if ($ftype=='text') { echo 'selected'; } } ?>>Текст</option>
                                            <option value="html" <?php if(@$fstruct[$f]) { if ($ftype=='html') { echo 'selected'; } } ?>>HTML</option>
                                            <option value="link" <?php if(@$fstruct[$f]) { if ($ftype=='link') { echo 'selected'; } } ?>>Ссылка</option>
                                        </select>
                                    </td>
                                    <td style="padding-bottom:4px">
                                        <input name="fstruct[]" class="field" type="text" id="fstruct[]" style="width:99%" <?php if (@$fstruct[$f]) { echo 'value="'.htmlspecialchars(stripslashes($fstruct[$f])).'"'; }?> />
                                    </td>
                                    <td width="80" align="right" style="padding-bottom:2px">
                                        <strong>Автопоиск:</strong>
                                    </td>
                                    <td width="20" align="right"><input name="flink[<?php echo $f;?>]" class="flink" type="radio" value="1" <?php if(@$fstruct[$f]) { if ($makelink) { echo 'checked="checked"'; } } ?>/></td>
                                    <td width="20" align="right">Вкл</td>
                                    <td width="20" align="right"><input name="flink[<?php echo $f;?>]" class="flink" type="radio" value="0" <?php if(@$fstruct[$f]) { if (!$makelink) { echo 'checked="checked"'; } } else { echo 'checked="checked"';} ?>/></td>
                                    <td width="20" align="right">Выкл</td>
                                </tr>
                            <?php } ?>
                            </table>

                            <div style="margin-top:10px"><strong>Обрабатывать HTML-поля <a href="index.php?view=filters" target="_blank">фильтрами</a>?</strong></div>
                            <div>
                                <select name="filters" id="filters" style="width:100%">
                                    <option value="0" <?php if (!$mod['filters']) { echo 'selected="selected"'; } ?>>Нет</option>
                                    <option value="1" <?php if ($mod['filters']) { echo 'selected="selected"'; } ?>>Да</option>
                                </select>
                            </div>

                            <div style="margin-top:12px"><strong>Описание рубрики</strong></div>
                            <div><?php $inCore->insertEditor('description', $mod['description'], '200', '100%'); ?></div>

                        </td>

                        <!-- боковая ячейка -->
                        <td width="300" valign="top" style="background:#ECECEC;">

                            <?php ob_start(); ?>

                            {tab=Публикация}

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                                <tr>
                                    <td width="20"><input type="checkbox" name="published" id="published" value="1" <?php if ($mod['published'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                                    <td><label for="published"><strong>Публиковать рубрику</strong></label></td>
                                </tr>
                            </table>

                            <div style="margin-top:7px">
                                <select name="parent_id" size="8" id="parent_id" style="width:99%;height:200px">
                                    <?php $rootid = $inDB->get_field('cms_uc_cats', 'parent_id=0', 'id'); ?>
                                    <option value="<?php echo $rootid; ?>" <?php if (@$mod['parent_id']==$rootid || !isset($mod['parent_id'])) { echo 'selected'; }?>>-- Корень каталога --</option>
                                    <?php
                                        if (isset($mod['parent_id'])){
                                            echo $inCore->getListItemsNS('cms_uc_cats', $mod['parent_id']);
                                        } else {
                                            echo $inCore->getListItemsNS('cms_uc_cats');
                                        }
                                    ?>
                                </select>
                            </div>

                            <div style="margin-bottom:15px;margin-top:4px" onchange="toggleAdvert()">
                                <select name="view_type" id="view_type" style="width:99%">
                                    <option value="list" <?php if (@$mod['view_type']=='list') {echo 'selected';} ?>>Список (таблица)</option>
                                    <option value="thumb" <?php if (@$mod['view_type']=='thumb') {echo 'selected';} ?>>Галерея (блоки)</option>
                                    <option value="shop" <?php if (@$mod['view_type']=='shop') {echo 'selected';} ?>>Магазин</option>
                                </select>
                            </div>

                            <div class="advert" id="catalog_advert" style="line-height:16px;<?php if ($mod['view_type']!='shop') {?>display:none<?php } ?>">
                                См. также: <a href="http://www.instantcms.ru/blogs/InstantSoft/professionalnyi-magazin-dlja-InstantCMS.html" target="_blank">InstantShop &mdash; профессиональный магазин для InstantCMS</a>
                            </div>

                            <script type="text/javascript">toggleAdvert();</script>

                            <div style="margin-top:12px"><strong>Вид рубрики</strong></div>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                                <tr>
                                    <td width="20"><input type="checkbox" name="showmore" id="showmore" value="1" <?php if ($mod['showmore']) { echo 'checked="checked"'; } ?>/></td>
                                    <td><label for="showmore">Ссылка &quot;Подробнее&quot; у каждой записи</label></td>
                                </tr>
                                <tr>
                                    <td width="20"><input type="checkbox" name="is_ratings" id="is_ratings" value="1" <?php if ($mod['is_ratings']) { echo 'checked="checked"'; } ?>/></td>
                                    <td><label for="is_ratings">Рейтинги записей</label></td>
                                </tr>
                                <tr>
                                    <td width="20"><input type="checkbox" name="showtags" id="showtags" value="1" <?php if ($mod['showtags']) { echo 'checked="checked"'; } ?>/></td>
                                    <td><label for="showtags">Показывать теги</label></td>
                                </tr>
                                <tr>
                                    <td width="20"><input type="checkbox" name="showsort" id="showsort" value="1" <?php if ($mod['showsort']) { echo 'checked="checked"'; } ?>/></td>
                                    <td><label for="showsort">Показывать выбор сортировки</label></td>
                                </tr>
                                <tr>
                                    <td width="20"><input type="checkbox" name="showabc" id="showabc" value="1" <?php if ($mod['showabc']) { echo 'checked="checked"'; } ?>/></td>
                                    <td><label for="showabc">Алфавитный указатель</label></td>
                                </tr>
                            </table>
                            
                            {tab=Записи}

                            <div style="margin-top:5px;">
                                <strong>Количество полей</strong><br/>
                                <span class="hinttext">Сколько полей (характеристик) показывать для каждой записи при просмотре рубрики</span>
                            </div>
                            <div>
                                <input name="fieldsshow" type="text" id="fieldsshow" style="width:100%" value="<?php if ($opt=='edit_cat') { echo $mod['fields_show']; } else { echo '10'; } ?>"/>
                            </div>

                            <div style="margin-top:10px;">
                                <strong>Сортировка записей</strong>
                            </div>
                            <div>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:2px;">
                                    <tr>
                                        <td valign="top"  width="50%">
                                            <select name="orderby" id="orderby" style="width:100%">
                                                <option value="title" <?php if(@$mod['orderby']=='title') { echo 'selected'; } ?>>По алфавиту</option>
                                                <option value="pubdate" <?php if(@$mod['orderby']=='pubdate') { echo 'selected'; } ?>>По дате</option>
                                                <option value="rating" <?php if(@$mod['orderby']=='rating') { echo 'selected'; } ?>>По рейтингу</option>
                                                <option value="hits" <?php if(@$mod['orderby']=='hits') { echo 'selected'; } ?>>По просмотрам</option>
                                            </select>
                                        </td>
                                        <td valign="top" style="padding-left:5px">
                                            <select name="orderto" id="orderto" style="width:100%">
                                                <option value="desc" <?php if(@$mod['orderto']=='desc') { echo 'selected'; } ?>>по убыванию</option>
                                                <option value="asc" <?php if(@$mod['orderto']=='asc') { echo 'selected'; } ?>>по возрастанию</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div style="margin-top:10px;">
                                <strong>Записей на странице</strong>
                            </div>
                            <div>
                                <input name="perpage" type="text" id="perpage" style="width:100%" value="<?php if ($opt=='edit_cat') { echo $mod['perpage']; } else { echo '20'; } ?>"/>
                            </div>

                            <div style="margin-top:10px;">
                                <strong>Подсветка новинок</strong>
                            </div>
                            <div>
                                <select name="shownew" id="shownew" style="width:100%">
                                    <option value="1" <?php if ($mod['shownew']) { echo 'selected="selected"'; } ?>>Да</option>
                                    <option value="0" <?php if (!$mod['shownew']) { echo 'selected="selected"'; } ?>>Нет</option>
                                </select>
                            </div>

                            <div style="margin-top:10px;">
                                <strong>Срок статуса новинки</strong>
                            </div>
                            <div>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:2px;">
                                    <tr>
                                        <td valign="top" width="100">
                                            <input name="int_1" type="text" id="int_1" style="width:95px" value="<?php echo @(int)$mod['newint']?>"/>
                                        </td>
                                        <td valign="top">
                                            <select name="int_2" id="int_2" style="width:100%">
                                                <option value="HOUR"  <?php if(@strstr($mod['newint'], 'HOUR')) { echo 'selected'; } ?>>часов</option>
                                                <option value="DAY" <?php if(@strstr($mod['newint'], 'DAY')) { echo 'selected'; } ?>>дней</option>
                                                <option value="MONTH" <?php if(@strstr($mod['newint'], 'MONTH')) { echo 'selected'; } ?>>месяцев</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            {tab=Доступ}

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist" style="margin-top:5px">
                                <tr>
                                    <td width="20">
                                        <?php
                                            if ($opt == 'edit_cat'){

                                                $sql2 = "SELECT * FROM cms_uc_cats_access WHERE cat_id = ".$mod['id'];
                                                $result2 = dbQuery($sql2);
                                                $ord = array();

                                                if (mysql_num_rows($result2)){
                                                    while ($r = mysql_fetch_assoc($result2)){
                                                        $ord[] = $r['group_id'];
                                                    }
                                                }
                                            }
                                        ?>
                                        <input name="is_public" type="checkbox" id="is_public" onclick="checkGroupList()" value="1" <?php if(@$mod['is_public']){ echo 'checked="checked"'; } ?> />
                                    </td>
                                    <td><label for="is_public"><strong>Разрешить пользователям добавлять записи</strong></label></td>
                                </tr>
                            </table>
                            <div style="padding:5px">
                                <span class="hinttext">
                                    Если отмечено, пользователи из выбранных групп будут видеть ссылку "Добавить запись" в этой рубрике каталога.
                                </span>
                            </div>

                            <div style="margin-top:10px;padding:5px;padding-right:0px;" id="grp">
                                <div>
                                    <strong>Разрешить группам:</strong><br />
                                    <span class="hinttext">
                                        Можно выбрать несколько, удерживая CTRL.
                                    </span>
                                </div>
                                <div>
                                    <?php
                                        echo '<select style="width: 99%" name="showfor[]" id="showin" size="6" multiple="multiple" '.(@$mod['is_public']?'':'disabled="disabled"').'>';

                                        $sql    = "SELECT * FROM cms_user_groups";
                                        $result = dbQuery($sql) ;

                                        if (mysql_num_rows($result)){
                                            while ($item=mysql_fetch_assoc($result)){
												if($item['alias'] != 'guest'){
													echo '<option value="'.$item['id'].'"';
													if ($opt=='edit_cat'){
														if (inArray($ord, $item['id'])){
															echo 'selected';
														}
													}
	
													echo '>';
													echo $item['title'].'</option>';
												}
                                            }
                                        }

                                        echo '</select>';
                                    ?>
                                </div>
                            </div>

                            <?php if (IS_BILLING){ ?>
                                <div style="margin:5px">
                                    <strong>Стоимость добавления записи</strong><br/>
                                    <div style="color:gray">Если не указана здесь, то используется цена по-умолчанию, из настроек биллинга</div>
                                    <input type="text" name="cost" value="<?php echo $mod['cost']; ?>" style="width:50px"/> баллов
                                </div>
                            <?php } ?>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist" style="margin-top:5px">
                                <tr>
                                    <td width="20">
                                        <input name="can_edit" type="checkbox" id="can_edit" onclick="" value="1" <?php if(@$mod['can_edit']){ echo 'checked="checked"'; } ?> />
                                    </td>
                                    <td><label for="can_edit"><strong>Разрешить редактирование</strong></label></td>
                                </tr>
                            </table>
                            <div style="padding:5px">
                                <span class="hinttext">
                                    Если отмечено, пользователи смогут изменять свои записи
                                </span>
                            </div>

                            {/tabs}

                            <?php echo jwTabs(ob_get_clean()); ?>

                        </td>

                    </tr>
                </table>
                <p>
                    <input name="add_mod" type="submit" id="add_mod" <?php if ($do=='add_cat') { echo 'value="Создать рубрику"'; } else { echo 'value="Сохранить рубрику"'; } ?> />
                    <input name="back" type="button" id="back" value="Отмена" onclick="window.history.back();"/>
                    <input name="opt" type="hidden" id="opt" <?php if ($opt=='add_cat') { echo 'value="submit_cat"'; } else { echo 'value="update_cat"'; } ?> />
                    <?php
                        if ($opt=='edit_cat'){
                            echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
                        }
                    ?>
                </p>
            </form>

		 <?php	
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'add_discount' || $opt == 'edit_discount'){
		if ($opt=='add_discount'){
			 echo '<h3>Добавить коэффициент</h3>';
			 cpAddPathway('Добавить коэффициент', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_discount');
			} else {
				 if(isset($_REQUEST['item_id'])){
					 $id = $_REQUEST['item_id'];
					 $sql = "SELECT * FROM cms_uc_discount WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);					
					 }
				 }

				 echo '<h3>'.$mod['title'].'</h3>';
	 	 		 cpAddPathway('Коэффициенты', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_discount');
				 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_discount&item_id='.$_REQUEST['item_id']);
			}
			?>
            <form id="addform" name="addform" method="post" action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>">
                <table width="584" border="0" cellspacing="5" class="proptable">
                    <tr>
                        <td width="250"><strong>Название: </strong></td>
                        <td width="315" valign="top"><input name="title" type="text" id="title" style="width:250px" value="<?php echo htmlspecialchars($mod['title']);?>"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Рубрика:</strong></td>
                        <td valign="top">
                            <select name="cat_id" id="cat_id" style="width:250px">
                                <?php $rootid = 0; ?>
                                <option value="<?php echo $rootid; ?>" <?php if (@$mod['cat_id']==$rootid || !isset($mod['cat_id'])) { echo 'selected'; }?>>Все рубрики</option>
                                <?php
                                    if (isset($mod['cat_id'])){
                                        echo $inCore->getListItems('cms_uc_cats', $mod['cat_id']);
                                    } else {
                                        echo $inCore->getListItems('cms_uc_cats', 0);
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Тип (действие): </strong></td>
                        <td valign="top"><label>
                                <select name="sign" id="sign" style="width:200px" onchange="toggleDiscountLimit()">
                                    <option value="-1" <?php if (@$mod['sign']==-1) {echo 'selected';} ?>>Скидка на товар (-1)</option>
                                    <option value="1" <?php if (@$mod['sign']==1) {echo 'selected';} ?>>Надбавка на товар (1)</option>
                                    <option value="2" <?php if (@$mod['sign']==2) {echo 'selected';} ?>>Надбавка на заказ (2)</option>
                                    <option value="3" <?php if (@$mod['sign']==3) {echo 'selected';} ?>>Скидка на заказ (3)</option>
                                </select>
                        </label></td>
                    </tr>
                    <tr class="if_limit" <?php if($mod['sign']!=3){ echo 'style="display:none"'; } ?>>
                        <td>
                            <strong>Действует при заказе на сумму от </strong>
                        </td>
                        <td valign="top">
                            <input name="if_limit" type="text" id="value" size="5" value="<?php if ($opt=='edit_discount') { echo $mod['if_limit']; } else { echo '0'; }?>"/> руб.
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Единицы: </strong></td>
                        <td valign="top"><label>
                                <select name="unit" id="unit" style="width:200px">
                                    <option value="%" <?php if (@$mod['unit']=='%') {echo 'selected';} ?>>Проценты</option>
                                    <option value="руб." <?php if (@$mod['unit']=='руб.') {echo 'selected';} ?>>Рубли</option>
                                </select>
                        </label></td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Значение: </strong>
                        </td>
                        <td valign="top">
                            <input name="value" type="text" id="value" size="5" value="<?php if ($opt=='edit_discount') { echo $mod['value']; } ?>"/>
                        </td>
                    </tr>
                </table>
                <p>
                    <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_discount') { echo 'value="Создать"'; } else { echo 'value="Сохранить изменения"'; } ?> />
                    <input name="back3" type="button" id="back3" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
                    <input name="opt" type="hidden" id="do" <?php if ($opt=='add_discount') { echo 'value="submit_discount"'; } else { echo 'value="update_discount"'; } ?> />
                    <?php
                    if ($opt=='edit_discount'){
                        echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
                    }
                    ?>
                </p>
            </form>
		 <?php
	}

//=================================================================================================//
//=================================================================================================//

	if($opt=='saveconfig'){	
		$cfg = array();
		$cfg['email']       = $inCore->request('email', 'str', '');
		$cfg['delivery']    = $inCore->request('delivery', 'str', '');
        $cfg['notice']      = $inCore->request('notice', 'int', 0);
        $cfg['premod']      = $inCore->request('premod', 'int', 1);
        $cfg['premod_msg']  = $inCore->request('premod_msg', 'int', 1);
        $cfg['is_comments'] = $inCore->request('is_comments', 'int', 0);
        $cfg['is_rss']      = $inCore->request('is_rss', 'int', 1);
		$cfg['watermark']   = $inCore->request('watermark', 'int', 1);
		
        $inCore->saveComponentConfig('catalog', $cfg);

		header('location:index.php?view=components&do=config&id='.$_REQUEST['id']);
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'config') {

		if (!isset($cfg['email'])) { $cfg['email'] = 'shop@site.ru'; }
		if (!isset($cfg['delivery'])) { $cfg['delivery'] = 'Сведения о доставке'; }
        if (!isset($cfg['notice'])) { $cfg['notice'] = 0; }
        if (!isset($cfg['premod'])) { $cfg['premod'] = 1; }        
        if (!isset($cfg['premod_msg'])) { $cfg['premod_msg'] = 1; }
        if (!isset($cfg['is_comments'])) { $cfg['is_comments'] = 0; }
        if (!isset($cfg['is_rss'])) { $cfg['is_rss'] = 1; }
		
		cpAddPathway('Настройки', $_SERVER['REQUEST_URI']);
			
         ?>
         <form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
             <table width="600" border="0" cellpadding="10" cellspacing="0" class="proptable">
                 <tr>
                     <td width=""><strong>E-mail продавца:</strong></td>
                     <td width="260"><input name="email" type="text" id="email" style="width:250px" value="<?php echo @$cfg['email'];?>"/></td>
                 </tr>
                 <tr>
                     <td><strong>Отправлять уведомление покупателю: </strong></td>
                     <td>
                         <input name="notice" type="radio" value="1" <?php if (@$cfg['notice']) { echo 'checked="checked"'; } ?> /> Да
                         <input name="notice" type="radio" value="0"  <?php if (@!$cfg['notice']) { echo 'checked="checked"'; } ?> /> Нет
                     </td>
                 </tr>
             </table>
             <table width="600" border="0" cellpadding="10" cellspacing="0" class="proptable">
                 <tr>
                     <td><strong>Премодерация записей пользователей: </strong></td>
                     <td width="260">
                         <input name="premod" type="radio" value="1" <?php if (@$cfg['premod']) { echo 'checked="checked"'; } ?> /> Да
                         <input name="premod" type="radio" value="0"  <?php if (@!$cfg['premod']) { echo 'checked="checked"'; } ?> /> Нет
                     </td>
                 </tr>
                 <tr>
                     <td><strong>Сообщать администратору о новых записях: </strong></td>
                     <td width="260">
                         <input name="premod_msg" type="radio" value="1" <?php if (@$cfg['premod_msg']) { echo 'checked="checked"'; } ?> /> Да
                         <input name="premod_msg" type="radio" value="0"  <?php if (@!$cfg['premod_msg']) { echo 'checked="checked"'; } ?> /> Нет
                     </td>
                 </tr>
                 <tr>
                     <td><strong>Автоматически включать комментарии для пользовательских записей: </strong></td>
                     <td width="260">
                         <input name="is_comments" type="radio" value="1" <?php if (@$cfg['is_comments']) { echo 'checked="checked"'; } ?> /> Да
                         <input name="is_comments" type="radio" value="0"  <?php if (@!$cfg['is_comments']) { echo 'checked="checked"'; } ?> /> Нет
                     </td>
                 </tr>
                 <tr>
                     <td><strong>Наносить водяной знак:</strong>  <br />Если включено, то на все загружаемые
			      фотографии к записям каталога будет наносится изображение 
			      из файла "<a href="/images/watermark.png" target="_blank">/images/watermark.png</a>"</td>
                     <td width="260">
                         <input name="watermark" type="radio" value="1" <?php if (@$cfg['watermark']) { echo 'checked="checked"'; } ?> /> Да
                         <input name="watermark" type="radio" value="0"  <?php if (@!$cfg['watermark']) { echo 'checked="checked"'; } ?> /> Нет
                     </td>
                 </tr>
             </table>
             <table width="600" border="0" cellpadding="10" cellspacing="0" class="proptable">
                 <tr>
                     <td><strong>Показывать иконку RSS в рубриках: </strong></td>
                     <td width="260">
                         <input name="is_rss" type="radio" value="1" <?php if (@$cfg['is_rss']) { echo 'checked="checked"'; } ?> /> Да
                         <input name="is_rss" type="radio" value="0"  <?php if (@!$cfg['is_rss']) { echo 'checked="checked"'; } ?> /> Нет
                     </td>
                 </tr>
             </table>
             <table width="600" border="0" cellpadding="10" cellspacing="0" class="proptable">
                 <tr>
                     <td><p><strong>Информация о доставке:</strong></p>
                         <p>
                             <label>
                                 <textarea name="delivery" style="width:568px;height:150px;border:solid 1px gray"><?php echo @$cfg['delivery'];?></textarea>
                             </label>
                     </p></td>
                 </tr>
             </table>
             <p>
                 <input name="opt" type="hidden" id="opt" value="saveconfig" />
                 <input name="save" type="submit" id="save" value="Сохранить" />
                 <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
             </p>
         </form>
        <?php
    }

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'import_xls'){

        cpAddPathway('Импорт из MS Excel', $_SERVER['REQUEST_URI']);
        echo '<h3>Импорт из MS Excel</h3>';

        if ($inCore->inRequest('cat_id')){
            //load category fields structure
            $cat = dbGetFields('cms_uc_cats', 'id='.$_REQUEST['cat_id'], 'title, fieldsstruct, view_type');
            $fstruct = unserialize($cat['fieldsstruct']);

            ?>
            <form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>" method="POST" enctype="multipart/form-data" name="addform">
            <p><strong>Рубрика:</strong> <a href="index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>&opt=import_xls"><?php echo $cat['title']; ?></a></p>
            <p>Выберите файл Excel, в котором находится таблица с характеристиками записей</p>
            <table width="650" border="0" cellspacing="5" class="proptable">
                <tr>
                    <td width="300">
                        <strong>Файл таблицы Excel:</strong><br/>
                        <span class="hinttext">В формате *.XLS</span>
                    </td>
                    <td><input type="file" name="xlsfile" /></td>
                </tr>
                <tr>
                    <td width="300">
                        <strong>Кодировка файла:</strong><br/>
                        <span class="hinttext">Зависит от пакета, в котором создавалась таблица</span>
                    </td>
                    <td>
                        <select name="charset" style="width:300px">
                            <option value="cp1251" selected>windows-1251 (MS Office)</option>
                            <option value="UTF-8">utf-8 (OpenOffice)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Количество записей (строк) для импорта:</strong><br/>
                        <span class="hinttext">Большие файлы рекомендуется импортировать по частям</span>
                    </td>
                    <td><input type="text" name="xlsrows" style="width:40px" /> шт.</td>
                </tr>
                <tr>
                    <td><strong>Номер листа с таблицей в файле:</strong></td>
                    <td><input type="text" name="xlslist" style="width:40px" value="1" /></td>
                </tr>
            </table>
            <p>
                Укажите числовые координаты первых ячеек с данными для каждого столбца.<br/>
                Если какую-либо характеристику нужно брать не из таблицы Excel, а сделать одинаковой для всех записей,<br/>
                то отметьте для нее галочку "Текст" и введите значение вручную.
            </p>
            <table width="650" border="0" cellspacing="5" class="proptable">
                <tr id="row_title">
                    <td width=""><strong>Название:</strong></td>
                    <td>Столбец:</td>
                    <td><input type="text" onkeyup="xlsEditCol()" id="title_col" name="cells[title][col]" style="width:40px" /></td>
                    <td>Строка:</td>
                    <td><input type="text" onkeyup="xlsEditRow()" id="title_row" name="cells[title][row]" style="width:40px" /></td>
                    <td width="90"><input type="checkbox" id="ignore_title" name="cells[title][ignore]" onclick="ignoreRow('title')" value="1"/> Текст:</td>
                    <td><input type="text" class="other" name="cells[title][other]" style="width:200px" disabled /></td>
                </tr>
            <?php
            $current = 0;
            foreach($fstruct as $key=>$value) {
                //strip special markups
                if (strstr($value, '/~h~/')) { $value=str_replace('/~h~/', '', $value); }
                elseif (strstr($value, '/~l~/')) { $value=str_replace('/~l~/', '', $value); } else { $ftype='text'; }
                if (strstr($value, '/~m~/')) { $value=str_replace('/~m~/', '', $value); }
                //show field inputs
                ?>                
                    <tr id="row_<?php echo $current; ?>">
                        <td width="150"><strong><?php echo stripslashes($value); ?>:</strong></td>
                        <td>Столбец:</td>
                        <td><input type="text" class="col" id="<?php echo $current; ?>" name="cells[<?php echo $current; ?>][col]" style="width:40px" /></td>
                        <td>Строка:</td>
                        <td><input type="text" class="row" name="cells[<?php echo $current; ?>][row]" style="width:40px" /></td>
                        <td><input type="checkbox" id="ignore_<?php echo $current; ?>" name="cells[<?php echo $current; ?>][ignore]" onclick="ignoreRow('<?php echo $current; ?>')" value="1" /> Текст:</td>
                        <td><input type="text" class="other" name="cells[<?php echo $current; ?>][other]" style="width:200px" disabled /></td>
                    </tr>
                <?php
                $current++;
            }

            if ($cat['view_type']=='shop'){
                ?>
                    <tr id="row_price">
                        <td width="250"><strong>Цена:</strong></td>
                        <td>Столбец:</td>
                        <td><input type="text" class="col" name="cells[price][col]" style="width:40px" /></td>
                        <td>Строка:</td>
                        <td><input type="text" class="row" name="cells[price][row]" style="width:40px" /></td>
                        <td><input type="checkbox" id="ignore_price" name="cells[price][ignore]" onclick="ignoreRow('price')" value="1"/> Текст: </td>
                        <td><input type="text" class="other" name="cells[price][other]" style="width:200px" disabled /></td>
                    </tr>
                <?php
            }
            ?>
            </table>

            <p>Задайте остальные параметры записей:</p>
            <table width="650" border="0" cellspacing="5" class="proptable">
                <tr>
                    <td width="300">
                        <strong>Публиковать записи:</strong><br/>
                        <span class="hinttext">Если включено, записи сразу появятся на сайте</span>
                    </td>
                    <td>
                        <input name="published" type="radio" value="1" checked="checked" /> Да
                        <input name="published" type="radio" value="0" /> Нет
                    </td>
                </tr>
                <tr>
                    <td><strong>Разрешить комментарии:</strong></td>
                    <td>
                        <input name="is_comments" type="radio" value="1" checked="checked" /> Да
                        <input name="is_comments" type="radio" value="0" /> Нет
                    </td>
                </tr>
                <?php if ($cat['view_type']=='shop'){ ?>
                <tr>
                    <td>
                        <strong>Разрешить выбор количества:</strong><br/>
                        <span class="hinttext">При заказе товара</span>
                    </td>
                    <td>
                        <input name="canmany" type="radio" value="1" checked="checked" /> Да
                        <input name="canmany" type="radio" value="0" /> Нет
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td>
                        <strong>Тэги записей:</strong><br/>
                        <span class="hinttext">Не обязательно</span>
                    </td>
                    <td>
                        <input type="text" name="tags" style="width:300px" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Изображение:</strong><br/>
                        <span class="hinttext">Не обязательно</span>
                    </td>
                    <td>
                        <input type="file" name="imgfile" />
                    </td>
                </tr>                
            </table>

            <p>
                <input name="cat_id" type="hidden" id="cat_id" value="<?php echo (int)$_REQUEST['cat_id']; ?>" />
                 <input name="opt" type="hidden" id="opt" value="go_import_xls" />
                 <input name="save" type="submit" id="save" value="Импортировать" />
                 <input name="back" type="button" id="back" value="Отмена" onclick="window.history.go(-1);"/>
            </p>

            </form><?php

        } else {


            echo '<h4>Выберите рубрику для импорта записей:</h4>';

            $sql = "SELECT id, title, NSLeft, NSLevel, parent_id
                    FROM cms_uc_cats
                    WHERE parent_id > 0
                    ORDER BY NSLeft";
            $result = dbQuery($sql);

            if (mysql_num_rows($result)>0){
                echo '<div style="padding:10px">';
                    while ($cat = mysql_fetch_assoc($result)){
                        echo '<div style="padding:2px;padding-left:18px;margin-left:'.(($cat['NSLevel']-1)*15).'px;background:url(/admin/images/icons/hmenu/cats.png) no-repeat">
                                  <a href="?view=components&do=config&id='.$_REQUEST['id'].'&opt=import_xls&cat_id='.$cat['id'].'">'.$cat['title'].'</a>
                              </div>';
                    }
                echo '</div>';
            }

//            $sql = "SELECT id, title FROM cms_uc_cats ORDER BY title";
//            $result = dbQuery($sql);
//
//            if (mysql_num_rows($result)>0){
//                echo '<p><strong>Выберите рубрику для импорта записей:</strong></p>';
//                echo '<ul>';
//                while ($cat = mysql_fetch_assoc($result)){
//                    echo '<li><a href="?view=components&do=config&id='.$_REQUEST['id'].'&opt=import_xls&cat_id='.$cat['id'].'">'.$cat['title'].'</a></li>';
//                }
//                echo '</ul>';
//            }
        }

    }

//=================================================================================================//
//=================================================================================================//
			
?>
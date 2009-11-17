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

function cpPriceInput($id){
	$sql = "SELECT c.view_type as view_type
			FROM cms_uc_cats c, cms_uc_items i
			WHERE i.id = $id AND i.category_id=c.id";
	$rs = dbQuery($sql) ;
	$show = mysql_fetch_assoc($rs);
	$show = $show['view_type'];
	
	if ($show == 'shop'){
		$price = dbGetField('cms_uc_items', 'id='.$id, 'price');
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

    $cfg = $inCore->loadComponentConfig('catalog');
    $opt = $inCore->request('opt', 'str', 'list_cats');

	cpAddPathway('������������� �������', '?view=components&do=config&id='.$_REQUEST['id']);
    echo '<h3>������������� �������</h3>';

    $GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/admin/components/catalog/js/common.js"></script>';

//=================================================================================================//
//=================================================================================================//

	$toolmenu = array();
	
	if ($opt=='list_items' || $opt=='list_cats' || $opt=='list_discount'){

        $toolmenu[0]['icon'] = 'newfolder.gif';
        $toolmenu[0]['title'] = '����� �������';
        $toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat';

        $toolmenu[1]['icon'] = 'newstuff.gif';
        $toolmenu[1]['title'] = '����� ������';
        $toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item';

        $toolmenu[2]['icon'] = 'newdiscount.gif';
        $toolmenu[2]['title'] = '����� �����������';
        $toolmenu[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_discount';

        $toolmenu[3]['icon'] = 'folders.gif';
        $toolmenu[3]['title'] = '��� �������';
        $toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats';

        $toolmenu[4]['icon'] = 'liststuff.gif';
        $toolmenu[4]['title'] = '��� ������';
        $toolmenu[4]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items';

        $toolmenu[5]['icon'] = 'listdiscount.gif';
        $toolmenu[5]['title'] = '��� ������������';
        $toolmenu[5]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_discount';

        $toolmenu[6]['icon'] = 'excel.gif';
        $toolmenu[6]['title'] = '������ ������� �� MS Excel';
        $toolmenu[6]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=import_xls';

        $toolmenu[7]['icon'] = 'config.gif';
        $toolmenu[7]['title'] = '���������';
        $toolmenu[7]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config';

	}

//=================================================================================================//
//=================================================================================================//

	if($opt == 'list_items'){
		$toolmenu[11]['icon'] = 'edit.gif';
		$toolmenu[11]['title'] = '������������� ���������';
		$toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=edit_item&multiple=1');";

		$toolmenu[12]['icon'] = 'show.gif';
		$toolmenu[12]['title'] = '����������� ���������';
		$toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=show_item&multiple=1');";

		$toolmenu[13]['icon'] = 'hide.gif';
		$toolmenu[13]['title'] = '������ ���������';
		$toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=hide_item&multiple=1');";

		$toolmenu[16]['icon'] = 'saveprices.gif';
		$toolmenu[16]['title'] = '��������� ����';
		$toolmenu[16]['link'] = "javascript:sendForm('index.php?view=components&do=config&id=".$_REQUEST['id']."&opt=saveprices');";	
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt=='list_items' || $opt=='list_cats' || $opt=='list_discount'){

		$toolmenu[15]['icon'] = 'cancel.gif';
		$toolmenu[15]['title'] = '������';
		$toolmenu[15]['link'] = '?view=components';
	
	} else {
	
		$toolmenu[20]['icon'] = 'save.gif';
		$toolmenu[20]['title'] = '���������';
		$toolmenu[20]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[21]['icon'] = 'cancel.gif';
		$toolmenu[21]['title'] = '������';
		$toolmenu[21]['link'] = 'javascript:history.go(-1);';
	
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
            require($_SERVER['DOCUMENT_ROOT'].'/includes/graphic.inc.php');
			//generate image file
			$tmp_name = $_FILES["imgfile"]["tmp_name"];
			$imgfile = $_FILES["imgfile"]["name"];
			$path_parts = pathinfo($imgfile);
			$ext = $path_parts['extension'];
			$imgfile = md5($imgfile.time()).'.'.$ext;
			//upload image and insert record in db
			if (@move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT']."/images/catalog/$imgfile")){
				@img_resize($_SERVER['DOCUMENT_ROOT']."/images/catalog/$imgfile", $_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$imgfile.jpg", 100, 100);
				@img_resize($_SERVER['DOCUMENT_ROOT']."/images/catalog/$imgfile", $_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$imgfile.jpg", 250, 250);
			} else { $msg .= '������ �������� �����������!'; }
		} else { $imgfile = ''; }

        //process import
		if (isset($_FILES["xlsfile"]["name"]) && @$_FILES["xlsfile"]["name"]!=''){
			//upload xls file
			$tmp_name = $_FILES["xlsfile"]["tmp_name"];
			$file = $_FILES["xlsfile"]["name"];
			$file = $_SERVER['DOCUMENT_ROOT']."/upload/". md5($file . time()). '.xls';
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
                        dbQuery($sql) or die('������ �������. ��������� ������������ ��������. <a href="javascript:window.history.go(-2)">�����</a>');

                        if ($tags){
                            $lastid = dbLastId('cms_uc_items');
                            cmsInsertTags($tags, 'catalog', $lastid);
                        }
                    }
                }

                if ($file) @unlink($file);

			} else { $msg .= '������ �������� ����� Excel!'; }
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
		} else {
			dbShowList('cms_uc_items', $_REQUEST['item']);
            foreach($_REQUEST['item'] as $k=>$id){
                dbQuery('UPDATE cms_uc_items SET on_moderate = 0 WHERE id='.$id);
            }
		}			
		echo '1'; exit;
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'hide_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_uc_items', $_REQUEST['item_id']);  }
		} else {
			dbHideList('cms_uc_items', $_REQUEST['item']);				
		}			
		echo '1'; exit;
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
            $canmany        = $inCore->request('canmany', 'int');
			$price          = $inCore->request('price', 'str');
			$price          = str_replace(',', '.', $price);
			$price          = round($price, 2);
            $item['price']  = $price;
            $item['canmany']= $canmany;
		}
				
		//get fields data
		$item['fields'] = serialize($item['fdata']);
		$item['fields'] = mysql_escape_string($item['fields']);

        $item['file']   = '';

		if (isset($_FILES["imgfile"]["name"]) && @$_FILES["imgfile"]["name"]!=''){			
			//generate image file			
			$tmp_name = $_FILES["imgfile"]["tmp_name"];
			$file = $_FILES["imgfile"]["name"];			
			$path_parts = pathinfo($file);
			$ext = $path_parts['extension'];	
			$file = md5($file.time()).'.'.$ext;
            $item['file'] = $file;
			//upload image and insert record in db		
			if (@move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT']."/images/catalog/$file")){
				@img_resize($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", $_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$file.jpg", 100, 100);
				@img_resize($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", $_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$file.jpg", 250, 250);
                @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", 0644);
				@chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$file.jpg", 0644);
				@chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$file.jpg", 0644);
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

            $item['imageurl']   = dbGetField('cms_uc_items', "id={$id}", 'imageurl');

            if ($inCore->inRequest('price')) {
                $canmany        = $inCore->request('canmany', 'int', 0);
                $price          = $inCore->request('price', 'str');
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
					@chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", 0777);
					@chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$file.jpg", 0777);
					@chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$file.jpg", 0777);
					@unlink($_SERVER['DOCUMENT_ROOT']."/images/catalog/$imageurl");
					@unlink($_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$imageurl.jpg");
					@unlink($_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$imageurl.jpg");
				}

                $file               = $_FILES["imgfile"]["name"];
                $path_parts         = pathinfo($file);
                $ext                = $path_parts['extension'];
                $file               = md5($file.time()).'.'.$ext;
                
                $item['imageurl']   = $file;

				if (@move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT']."/images/catalog/$file")){
                    //create image thumbnails
                    @img_resize($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", $_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$file.jpg", 100, 100);
                    @img_resize($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", $_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$file.jpg", 250, 250);
                    @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", 0644);
                    @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$file.jpg", 0644);
                    @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$file.jpg", 0644);
				} else { $msg = '������ �������� �����������!'; }				
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

        $fstruct               = $_REQUEST['fstruct'];

		foreach ($fstruct as $key=>$value) { 
			if ($value=='') { unset($fstruct[$key]); } 
			else { 
					if ($_REQUEST['fformat'][$key]=='html') { $fstruct[$key] .= '/~h~/'; } 
					if ($_REQUEST['fformat'][$key]=='link') { $fstruct[$key] .= '/~l~/'; } 
					if ($_REQUEST['flink'][$key]) { $fstruct[$key] .= '/~m~/'; } 
				 }
		}

		$cat['fields'] = serialize($fstruct);

        $cat['is_public'] = $inCore->request('is_public', 'int', 0);

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

            $fstruct               = $_REQUEST['fstruct'];

            foreach ($fstruct as $key=>$value) {
                if ($value=='') { unset($fstruct[$key]); }
                else {
                        if ($_REQUEST['fformat'][$key]=='html') { $fstruct[$key] .= '/~h~/'; }
                        if ($_REQUEST['fformat'][$key]=='link') { $fstruct[$key] .= '/~l~/'; }
                        if ($_REQUEST['flink'][$key]) { $fstruct[$key] .= '/~m~/'; }
                     }
            }

            $cat['fields'] = serialize($fstruct);

            $cat['is_public'] = $inCore->request('is_public', 'int', 0);

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

        cpAddPathway('������� ��������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		echo '<h3>������� ��������</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = '��������';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$fields[3]['title'] = '��������';	$fields[3]['field'] = 'parent_id';	$fields[3]['width'] = '200';
		$fields[3]['prc'] = 'cpCatalogCatById';

		$fields[4]['title'] = '�����';		$fields[4]['field'] = 'published';	$fields[4]['width'] = '100';
		$fields[4]['do'] = 'opt'; $fields[4]['do_suffix'] = '_cat';

		//ACTIONS
		$actions = array();
		$actions[0]['title'] = '�������� �����������';
		$actions[0]['icon']  = 'explore.gif';
		$actions[0]['link']  = 'javascript:openCat(%id%)';

		$actions[1]['title'] = '�������������';
		$actions[1]['icon']  = 'edit.gif';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$actions[2]['title'] = '����������';
		$actions[2]['icon']  = 'copy.gif';
		$actions[2]['link']  = "javascript:copyCat(".$_REQUEST['id'].", %id%);";

		$actions[3]['title'] = '�������';
		$actions[3]['icon']  = 'delete.gif';
		$actions[3]['confirm'] = '������� ������� �� ��������?';
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
	
		cpAddPathway('������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');

        if ($inCore->inRequest('on_moderate')){
            echo '<h3>������ �� ���������</h3>';
        } else {
            echo '<h3>������</h3>';
        }
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = '��������';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';

        if ($inCore->inRequest('on_moderate')){
            $fields[1]['link'] = '/catalog/0/item%id%.html';
        } else {
            $fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';
        }

		$fields[1]['filter'] = 15;
		
		$fields[2]['title'] = '�����';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';
		$fields[2]['do'] = 'opt'; $fields[2]['do_suffix'] = '_item';
		
		$fields[3]['title'] = '�������';	$fields[3]['field'] = 'category_id';$fields[3]['width'] = '200';
		$fields[3]['prc'] = 'cpCatalogCatById';  $fields[3]['filter'] = 1;  $fields[3]['filterlist'] = cpGetList('cms_uc_cats');

		$fields[4]['title'] = '����';		$fields[4]['field'] = 'id';	$fields[4]['width'] = '150';
		$fields[4]['prc'] = 'cpPriceInput';
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = '�������� ����';
		$actions[0]['icon']  = 'date.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=renew_item&item_id=%id%';

		$actions[1]['title'] = '�������������';
		$actions[1]['icon']  = 'edit.gif';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

		$actions[2]['title'] = '����������';
		$actions[2]['icon']  = 'copy.gif';
		$actions[2]['link']  = "javascript:copyItem(".$_REQUEST['id'].", %id%);";

		$actions[3]['title'] = '�������';
		$actions[3]['icon']  = 'delete.gif';
		$actions[3]['confirm'] = '������� ������ �� ��������?';
		$actions[3]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_item&item_id=%id%';

        if ($inCore->inRequest('on_moderate')){ $where = 'on_moderate=1'; } else { $where = ''; }

		//Print table
		cpListTable('cms_uc_items', $fields, $actions, $where);
	}

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'list_discount'){

        cpAddPathway('������������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_discount');
		echo '<h3>������������</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = '��������';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_discount&item_id=%id%';

		$fields[3]['title'] = '���������';	$fields[3]['field'] = 'cat_id';     $fields[3]['width'] = '200';
		$fields[3]['prc'] = 'cpCatalogCatById';

		$fields[4]['title'] = '���';       $fields[4]['field'] = 'sign';		$fields[4]['width'] = '40';

        $fields[5]['title'] = '������';     $fields[5]['field'] = 'value';      $fields[5]['width'] = '80';

        $fields[6]['title'] = '�������';        $fields[6]['field'] = 'unit';       $fields[6]['width'] = '80';

        $fields[7]['title'] = '�����';     $fields[7]['field'] = 'if_limit';      $fields[7]['width'] = '80';

		//ACTIONS
		$actions = array();
		$actions[1]['title'] = '�������������';
		$actions[1]['icon']  = 'edit.gif';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_discount&item_id=%id%';

        $actions[3]['title'] = '�������';
		$actions[3]['icon']  = 'delete.gif';
		$actions[3]['confirm'] = '������� �����������?';
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
            echo '<h3>�������� ������</h3>';
            cpAddPathway('�������� ������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item');
		} else {
                     if(isset($_REQUEST['multiple'])){
                        if (isset($_REQUEST['item'])){
                            $_SESSION['editlist'] = $_REQUEST['item'];
                        } else {
                            echo '<p class="error">��� ��������� ��������!</p>';
                            return;
                        }
                     }

                     $ostatok = '';

                     if (isset($_SESSION['editlist'])){
                        $id = array_shift($_SESSION['editlist']);
                        if (sizeof($_SESSION['editlist'])==0) { unset($_SESSION['editlist']); } else
                        { $ostatok = '(�� �������: '.sizeof($_SESSION['editlist']).')'; }
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
                     cpAddPathway('������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
                     cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id='.$id);
			}

//=================================================================================================//
//=================================================================================================//

		if ($opt == 'edit_item' || isset($_REQUEST['cat_id'])) {		
			if ($opt=='edit_item') { 
				$cat_id = $mod['category_id']; 
				$is_shop = ($mod['viewtype']=='shop');
			} else { 
				$cat_id = $_REQUEST['cat_id']; 
				$is_shop = (dbGetField('cms_uc_cats', 'id='.$cat_id, 'view_type')=='shop');
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

                    <!-- ������� ������ -->
                    <td valign="top">

                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td valign="top">
                                    <div><strong>�������� ������</strong></div>
                                    <div>
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td><input name="title" type="text" id="title" style="width:98%" value="<?php echo @$mod['title'];?>" /></td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td width="135" valign="top">
                                    <div><strong>���� ����������</strong></div>
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
                                    <div><strong>����</strong></div>
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
                            <strong><?php echo $value; ?></strong>
                            <?php if ($makelink) { echo ' <span class="hinttext" style="float:right">����� �������, ���� ���������</span>'; } ?>
                        </div>
                        <div>
                            <?php if ($ftype=='link' || $ftype == 'text') { ?>
                                <input style="width:99%" name="fdata[<?php echo $key?>]" type="text" id="fdata[]" <?php if (@$fdata) { echo 'value="'.strip_tags($fdata[$key]).'"';} ?>/>
                            <?php } else { ?>
                                    <?php
                                        if (@$fdata[$key]) { $fdata[$key] = str_replace('\"', '"', $fdata[$key]); }
                                        $inCore->insertEditor('fdata['.$key.']', $fdata[$key], '220', '100%');
                                    ?>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <div><strong>���� ������</strong></div>
                        <div><input name="tags" type="text" id="tags" style="width:99%" value="<?php if (isset($mod['id'])) { echo cmsTagLine('catalog', $mod['id'], false); } ?>" /></div>

                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                            <?php if ($is_shop) { ?>
                            <tr>
                                <td width="20"><input type="checkbox" name="canmany" id="canmany" value="1" <?php if (@$mod['canmany']) { echo 'checked="checked"'; } ?>/> </td>
                                <td><label for="canmany"><strong>��������� ����� ���������� ��� ������ ����� ������</strong></label></td>
                            </tr>
                            <?php } ?>
                        </table>

                    </td>

                    <!-- ������� ������ -->
                    <td width="300" valign="top" style="background:#ECECEC;">

                        <?php ob_start(); ?>

                        {tab=����������}

                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                            <tr>
                                <td width="20"><input type="checkbox" name="published" id="published" value="1" <?php if ($mod['published'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                                <td><label for="published"><strong>����������� ������</strong></label></td>
                            </tr>
                        </table>

                        <div style="margin-top:15px">
                            <strong>�������</strong>
                        </div>
                        <div>
                            <input type="text" disabled="disabled" value="<?php echo $cat['title']; ?>" style="width:100%" />
                            <input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>" />
                        </div>

                        <div style="margin-top:15px"><strong>����������</strong></div>
                        <div style="margin-bottom:10px">
                            <?php
                                if ($opt=='edit_item'){
                                    if (file_exists($_SERVER['DOCUMENT_ROOT'].'/images/catalog/small/'.$mod['imageurl'].'.jpg')){
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

                        <div style="margin-top:25px"><strong>��������� ����������</strong></div>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                            <tr>
                                <td width="20"><input type="checkbox" name="is_comments" id="is_comments" value="1" <?php if ($mod['is_comments'] || $opt=='add_item') { echo 'checked="checked"'; } ?>/></td>
                                <td><label for="is_comments">��������� �����������</label></td>
                            </tr>
                        </table>

                        {tab=SEO}

                        <div style="margin-top:5px">
                            <strong>�������� �����</strong><br/>
                            <span class="hinttext">����� �������, 10-15 ����</span>
                        </div>
                        <div>
                             <textarea name="meta_keys" style="width:97%" rows="2" id="meta_keys"><?php echo @$mod['meta_keys'];?></textarea>
                        </div>

                        <div style="margin-top:20px">
                            <strong>��������</strong><br/>
                            <span class="hinttext">�� ����� 250 ��������</span>
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
                <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_item') { echo 'value="�������� ������"'; } else { echo 'value="��������� ������"'; } ?> />
                <input name="back2" type="button" id="back2" value="������" onclick="window.location.href='index.php?view=components';"/>
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
					echo '<h4>�������� �������:</h4>';

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
			 echo '<h3>�������� �������</h3>';
			 cpAddPathway('�������� �������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat');	 
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
				
				 echo '<h3>�������: '.$mod['title'].'</h3>';
	 	 		 cpAddPathway('������� ��������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
				 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$_REQUEST['item_id']);	 
			}
			?>

            <form id="addform" name="addform" method="post" action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" enctype="multipart/form-data">
                <table class="proptable" width="100%" cellpadding="15" cellspacing="2">
                    <tr>

                        <!-- ������� ������ -->
                        <td valign="top">

                            <div><strong>�������� �������</strong></div>
                            <div><input name="title" type="text" id="title" style="width:99%" value="<?php echo @$mod['title'];?>" /></div>

                            <div style="margin-top:10px"><strong>�������������� �������</strong></div>

                            <div style="margin-top:2px;margin-bottom:12px">
                                <div><span class="hinttext">
                                    ������� �������� �����, ������� ����� ����� ��������� ��� ���������� ������� � ��� ������� ��������.
                                </span></div>
                                <div><span class="hinttext">
                                    ��������� ��������� ����� �������������� �� ��������� ����� � ������ ����� ������ �������.
                                    ��� ��������� ������������� ������ ������ � ����������� ���������������� ����� ������.
                                </span></div>
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
                                        <select name="fformat[]" style="width:100px">
                                            <option value="text" <?php if(@$fstruct[$f]) { if ($ftype=='text') { echo 'selected'; } } ?>>�����</option>
                                            <option value="html" <?php if(@$fstruct[$f]) { if ($ftype=='html') { echo 'selected'; } } ?>>HTML</option>
                                            <option value="link" <?php if(@$fstruct[$f]) { if ($ftype=='link') { echo 'selected'; } } ?>>������</option>
                                        </select>
                                    </td>
                                    <td style="padding-bottom:4px">
                                        <input name="fstruct[]" type="text" id="fstruct[]" style="width:99%" <?php if (@$fstruct[$f]) { echo 'value="'.$fstruct[$f].'"'; }?> />
                                    </td>
                                    <td width="80" align="right" style="padding-bottom:2px">
                                        <strong>���������:</strong>
                                    </td>
                                    <td width="20" align="right"><input name="flink[<?php echo $f;?>]" type="radio" value="1" <?php if(@$fstruct[$f]) { if ($makelink) { echo 'checked="checked"'; } } ?>/></td>
                                    <td width="20" align="right">���</td>
                                    <td width="20" align="right"><input name="flink[<?php echo $f;?>]" type="radio" value="0" <?php if(@$fstruct[$f]) { if (!$makelink) { echo 'checked="checked"'; } } else { echo 'checked="checked"';} ?>/></td>
                                    <td width="20" align="right">����</td>
                                </tr>
                            <?php } ?>
                            </table>

                            <div style="margin-top:10px"><strong>������������ HTML-���� <a href="index.php?view=filters" target="_blank">���������</a>?</strong></div>
                            <div>
                                <select name="filters" id="filters" style="width:100%">
                                    <option value="0" <?php if (!$mod['filters']) { echo 'selected="selected"'; } ?>>���</option>
                                    <option value="1" <?php if ($mod['filters']) { echo 'selected="selected"'; } ?>>��</option>
                                </select>
                            </div>

                            <div style="margin-top:12px"><strong>�������� �������</strong></div>
                            <div><?php $inCore->insertEditor('description', $mod['description'], '200', '100%'); ?></div>

                        </td>

                        <!-- ������� ������ -->
                        <td width="300" valign="top" style="background:#ECECEC;">

                            <?php ob_start(); ?>

                            {tab=����������}

                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                                <tr>
                                    <td width="20"><input type="checkbox" name="published" id="published" value="1" <?php if ($mod['published'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                                    <td><label for="published"><strong>����������� �������</strong></label></td>
                                </tr>
                            </table>

                            <div style="margin-top:7px">
                                <select name="parent_id" size="8" id="parent_id" style="width:99%;height:200px">
                                    <?php $rootid = dbGetField('cms_uc_cats', 'parent_id=0', 'id'); ?>
                                    <option value="<?php echo $rootid; ?>" <?php if (@$mod['parent_id']==$rootid || !isset($mod['parent_id'])) { echo 'selected'; }?>>-- ������ �������� --</option>
                                    <?php
                                        if (isset($mod['parent_id'])){
                                            echo $inCore->getListItemsNS('cms_uc_cats', $mod['parent_id']);
                                        } else {
                                            echo $inCore->getListItemsNS('cms_uc_cats');
                                        }
                                    ?>
                                </select>
                            </div>

                            <div style="margin-bottom:15px;margin-top:4px">
                                <select name="view_type" id="view_type" style="width:99%">
                                    <option value="list" <?php if (@$mod['view_type']=='list') {echo 'selected';} ?>>������ (�������)</option>
                                    <option value="thumb" <?php if (@$mod['view_type']=='thumb') {echo 'selected';} ?>>������� (�����)</option>
                                    <option value="shop" <?php if (@$mod['view_type']=='shop') {echo 'selected';} ?>>�������</option>
                                </select>
                            </div>

                            <div style="margin-top:12px"><strong>��� �������</strong></div>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                                <tr>
                                    <td width="20"><input type="checkbox" name="showmore" id="showmore" value="1" <?php if ($mod['showmore']) { echo 'checked="checked"'; } ?>/></td>
                                    <td><label for="showmore">������ &quot;���������&quot; � ������ ������</label></td>
                                </tr>
                                <tr>
                                    <td width="20"><input type="checkbox" name="showtags" id="showtags" value="1" <?php if ($mod['showtags']) { echo 'checked="checked"'; } ?>/></td>
                                    <td><label for="showtags">���������� ����</label></td>
                                </tr>
                                <tr>
                                    <td width="20"><input type="checkbox" name="showsort" id="showsort" value="1" <?php if ($mod['showsort']) { echo 'checked="checked"'; } ?>/></td>
                                    <td><label for="showsort">���������� ����� ����������</label></td>
                                </tr>
                                <tr>
                                    <td width="20"><input type="checkbox" name="showabc" id="showabc" value="1" <?php if ($mod['showabc']) { echo 'checked="checked"'; } ?>/></td>
                                    <td><label for="showabc">���������� ���������</label></td>
                                </tr>
                            </table>
                            
                            {tab=������}

                            <div style="margin-top:5px;">
                                <strong>���������� �����</strong><br/>
                                <span class="hinttext">������� ����� (�������������) ���������� ��� ������ ������ ��� ��������� �������</span>
                            </div>
                            <div>
                                <input name="fieldsshow" type="text" id="fieldsshow" style="width:100%" value="<?php if ($opt=='edit_cat') { echo $mod['fields_show']; } else { echo '10'; } ?>"/>
                            </div>

                            <div style="margin-top:10px;">
                                <strong>���������� �������</strong>
                            </div>
                            <div>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:2px;">
                                    <tr>
                                        <td valign="top"  width="50%">
                                            <select name="orderby" id="orderby" style="width:100%">
                                                <option value="title" <?php if(@$mod['orderby']=='title') { echo 'selected'; } ?>>�� ��������</option>
                                                <option value="pubdate" <?php if(@$mod['orderby']=='pubdate') { echo 'selected'; } ?>>�� ����</option>
                                                <option value="rating" <?php if(@$mod['orderby']=='rating') { echo 'selected'; } ?>>�� ��������</option>
                                                <option value="hits" <?php if(@$mod['orderby']=='hits') { echo 'selected'; } ?>>�� ����������</option>
                                            </select>
                                        </td>
                                        <td valign="top" style="padding-left:5px">
                                            <select name="orderto" id="orderto" style="width:100%">
                                                <option value="desc" <?php if(@$mod['orderto']=='desc') { echo 'selected'; } ?>>�� ��������</option>
                                                <option value="asc" <?php if(@$mod['orderto']=='asc') { echo 'selected'; } ?>>�� �����������</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div style="margin-top:10px;">
                                <strong>������� �� ��������</strong>
                            </div>
                            <div>
                                <input name="perpage" type="text" id="perpage" style="width:100%" value="<?php if ($opt=='edit_cat') { echo $mod['perpage']; } else { echo '20'; } ?>"/>
                            </div>

                            <div style="margin-top:10px;">
                                <strong>��������� �������</strong>
                            </div>
                            <div>
                                <select name="shownew" id="shownew" style="width:100%">
                                    <option value="1" <?php if ($mod['shownew']) { echo 'selected="selected"'; } ?>>��</option>
                                    <option value="0" <?php if (!$mod['shownew']) { echo 'selected="selected"'; } ?>>���</option>
                                </select>
                            </div>

                            <div style="margin-top:10px;">
                                <strong>���� ������� �������</strong>
                            </div>
                            <div>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:2px;">
                                    <tr>
                                        <td valign="top" width="100">
                                            <input name="int_1" type="text" id="int_1" style="width:95px" value="<?php echo @(int)$mod['newint']?>"/>
                                        </td>
                                        <td valign="top">
                                            <select name="int_2" id="int_2" style="width:100%">
                                                <option value="HOUR"  <?php if(@strstr($mod['newint'], 'HOUR')) { echo 'selected'; } ?>>�����</option>
                                                <option value="DAY" <?php if(@strstr($mod['newint'], 'DAY')) { echo 'selected'; } ?>>����</option>
                                                <option value="MONTH" <?php if(@strstr($mod['newint'], 'MONTH')) { echo 'selected'; } ?>>�������</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            {tab=������}

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
                                    <td><label for="is_public"><strong>��������� ������������� ��������� ������</strong></label></td>
                                </tr>
                            </table>
                            <div style="padding:5px">
                                <span class="hinttext">
                                    ���� ��������, ������������ �� ��������� ����� ����� ������ ������ "�������� ������" � ���� ������� ��������.
                                </span>
                            </div>

                            <div style="margin-top:10px;padding:5px;padding-right:0px;" id="grp">
                                <div>
                                    <strong>��������� �������:</strong><br />
                                    <span class="hinttext">
                                        ����� ������� ���������, ��������� CTRL.
                                    </span>
                                </div>
                                <div>
                                    <?php
                                        echo '<select style="width: 99%" name="showfor[]" id="showin" size="6" multiple="multiple" '.(@$mod['is_public']?'':'disabled="disabled"').'>';

                                        $sql    = "SELECT * FROM cms_user_groups";
                                        $result = dbQuery($sql) ;

                                        if (mysql_num_rows($result)){
                                            while ($item=mysql_fetch_assoc($result)){
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

                                        echo '</select>';
                                    ?>
                                </div>
                            </div>

                            {/tabs}

                            <?php echo jwTabs(ob_get_clean()); ?>

                        </td>

                    </tr>
                </table>
                <p>
                    <input name="add_mod" type="submit" id="add_mod" <?php if ($do=='add_cat') { echo 'value="������� �������"'; } else { echo 'value="��������� �������"'; } ?> />
                    <input name="back" type="button" id="back" value="������" onclick="window.history.back();"/>
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
			 echo '<h3>�������� �����������</h3>';
			 cpAddPathway('�������� �����������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_discount');
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
	 	 		 cpAddPathway('������������', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_discount');
				 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_discount&item_id='.$_REQUEST['item_id']);
			}
			?>
            <form id="addform" name="addform" method="post" action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>">
                <table width="584" border="0" cellspacing="5" class="proptable">
                    <tr>
                        <td width="250"><strong>��������: </strong></td>
                        <td width="315" valign="top"><input name="title" type="text" id="title" style="width:250px" value="<?php echo @$mod['title'];?>"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>�������:</strong></td>
                        <td valign="top">
                            <select name="cat_id" id="cat_id" style="width:250px">
                                <?php $rootid = 0; ?>
                                <option value="<?php echo $rootid; ?>" <?php if (@$mod['cat_id']==$rootid || !isset($mod['cat_id'])) { echo 'selected'; }?>>��� �������</option>
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
                        <td><strong>��� (��������): </strong></td>
                        <td valign="top"><label>
                                <select name="sign" id="sign" style="width:200px" onchange="toggleDiscountLimit()">
                                    <option value="-1" <?php if (@$mod['sign']==-1) {echo 'selected';} ?>>������ �� ����� (-1)</option>
                                    <option value="1" <?php if (@$mod['sign']==1) {echo 'selected';} ?>>�������� �� ����� (1)</option>
                                    <option value="2" <?php if (@$mod['sign']==2) {echo 'selected';} ?>>�������� �� ����� (2)</option>
                                    <option value="3" <?php if (@$mod['sign']==3) {echo 'selected';} ?>>������ �� ����� (3)</option>
                                </select>
                        </label></td>
                    </tr>
                    <tr class="if_limit" <?php if($mod['sign']!=3){ echo 'style="display:none"'; } ?>>
                        <td>
                            <strong>��������� ��� ������ �� ����� �� </strong>
                        </td>
                        <td valign="top">
                            <input name="if_limit" type="text" id="value" size="5" value="<?php if ($opt=='edit_discount') { echo $mod['if_limit']; } else { echo '0'; }?>"/> ���.
                        </td>
                    </tr>
                    <tr>
                        <td><strong>�������: </strong></td>
                        <td valign="top"><label>
                                <select name="unit" id="unit" style="width:200px">
                                    <option value="%" <?php if (@$mod['unit']=='%') {echo 'selected';} ?>>��������</option>
                                    <option value="���." <?php if (@$mod['unit']=='���.') {echo 'selected';} ?>>�����</option>
                                </select>
                        </label></td>
                    </tr>
                    <tr>
                        <td>
                            <strong>��������: </strong>
                        </td>
                        <td valign="top">
                            <input name="value" type="text" id="value" size="5" value="<?php if ($opt=='edit_discount') { echo $mod['value']; } ?>"/>
                        </td>
                    </tr>
                </table>
                <p>
                    <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_discount') { echo 'value="�������"'; } else { echo 'value="��������� ���������"'; } ?> />
                    <input name="back3" type="button" id="back3" value="������" onclick="window.location.href='index.php?view=components';"/>
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
		$cfg['delivery']    = $inCore->request('delivery', 'html', '');
        $cfg['notice']      = $inCore->request('notice', 'int', 0);
        $cfg['premod']      = $inCore->request('premod', 'int', 1);
        $cfg['premod_msg']  = $inCore->request('premod_msg', 'int', 1);
		$cfg['delivery']    = str_replace('\"', '&quot;', $cfg['delivery']);
		$cfg['delivery']    = str_replace('"', '&quot;', $cfg['delivery']);
		
        $inCore->saveComponentConfig('catalog', $cfg);

		header('location:index.php?view=components&do=config&id='.$_REQUEST['id']);
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'config') {
		if (!isset($cfg['email'])) { $cfg['email'] = 'shop@site.ru'; }
		if (!isset($cfg['delivery'])) { $cfg['delivery'] = '�������� � ��������'; }
        if (!isset($cfg['notice'])) { $cfg['notice'] = 0; }
        if (!isset($cfg['premod'])) { $cfg['premod'] = 1; }
        if (!isset($cfg['premod_msg'])) { $cfg['premod_msg'] = 1; }
		
		cpAddPathway('���������', $_SERVER['REQUEST_URI']);
		
		$cfg['delivery'] = str_replace('&quot;', '"', $cfg['delivery']);
			
         ?>
         <form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
             <table width="600" border="0" cellpadding="10" cellspacing="0" class="proptable">
                 <tr>
                     <td width=""><strong>E-mail ��������:</strong></td>
                     <td width="260"><input name="email" type="text" id="email" style="width:250px" value="<?php echo @$cfg['email'];?>"/></td>
                 </tr>
                 <tr>
                     <td><strong>���������� ����������� ����������: </strong></td>
                     <td>
                         <input name="notice" type="radio" value="1" <?php if (@$cfg['notice']) { echo 'checked="checked"'; } ?> /> ��
                         <input name="notice" type="radio" value="0"  <?php if (@!$cfg['notice']) { echo 'checked="checked"'; } ?> /> ���
                     </td>
                 </tr>
             </table>
             <table width="600" border="0" cellpadding="10" cellspacing="0" class="proptable">
                 <tr>
                     <td><strong>������������ ������� �������������: </strong></td>
                     <td width="260">
                         <input name="premod" type="radio" value="1" <?php if (@$cfg['premod']) { echo 'checked="checked"'; } ?> /> ��
                         <input name="premod" type="radio" value="0"  <?php if (@!$cfg['premod']) { echo 'checked="checked"'; } ?> /> ���
                     </td>
                 </tr>
                 <tr>
                     <td><strong>�������� �������������� � ����� �������: </strong></td>
                     <td width="260">
                         <input name="premod_msg" type="radio" value="1" <?php if (@$cfg['premod_msg']) { echo 'checked="checked"'; } ?> /> ��
                         <input name="premod_msg" type="radio" value="0"  <?php if (@!$cfg['premod_msg']) { echo 'checked="checked"'; } ?> /> ���
                     </td>
                 </tr>
             </table>
             <table width="600" border="0" cellpadding="10" cellspacing="0" class="proptable">
                 <tr>
                     <td><p><strong>���������� � ��������:</strong></p>
                         <p>
                             <label>
                                 <textarea name="delivery" style="width:568px;height:150px;border:solid 1px gray"><?php echo @$cfg['delivery'];?></textarea>
                             </label>
                     </p></td>
                 </tr>
             </table>
             <p>
                 <input name="opt" type="hidden" id="opt" value="saveconfig" />
                 <input name="save" type="submit" id="save" value="���������" />
                 <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=components';"/>
             </p>
         </form>
        <?php
    }

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'import_xls'){

        cpAddPathway('������ �� MS Excel', $_SERVER['REQUEST_URI']);
        echo '<h3>������ �� MS Excel</h3>';

        if ($inCore->inRequest('cat_id')){
            //load category fields structure
            $cat = dbGetFields('cms_uc_cats', 'id='.$_REQUEST['cat_id'], 'title, fieldsstruct, view_type');
            $fstruct = unserialize($cat['fieldsstruct']);

            ?>
            <form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>" method="POST" enctype="multipart/form-data" name="addform">
            <p><strong>�������:</strong> <a href="index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>&opt=import_xls"><?php echo $cat['title']; ?></a></p>
            <p>�������� ���� Excel, � ������� ��������� ������� � ���������������� �������</p>
            <table width="650" border="0" cellspacing="5" class="proptable">
                <tr>
                    <td width="300">
                        <strong>���� ������� Excel:</strong><br/>
                        <span class="hinttext">� ������� *.XLS</span>
                    </td>
                    <td><input type="file" name="xlsfile" /></td>
                </tr>
                <tr>
                    <td width="300">
                        <strong>��������� �����:</strong><br/>
                        <span class="hinttext">������� �� ������, � ������� ����������� �������</span>
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
                        <strong>���������� ������� (�����) ��� �������:</strong><br/>
                        <span class="hinttext">������� ����� ������������� ������������� �� ������</span>
                    </td>
                    <td><input type="text" name="xlsrows" style="width:40px" /> ��.</td>
                </tr>
                <tr>
                    <td><strong>����� ����� � �������� � �����:</strong></td>
                    <td><input type="text" name="xlslist" style="width:40px" value="1" /></td>
                </tr>
            </table>
            <p>
                ������� �������� ���������� ������ ����� � ������� ��� ������� �������.<br/>
                ���� �����-���� �������������� ����� ����� �� �� ������� Excel, � ������� ���������� ��� ���� �������,<br/>
                �� �������� ��� ��� ������� "�����" � ������� �������� �������.
            </p>
            <table width="650" border="0" cellspacing="5" class="proptable">
                <tr id="row_title">
                    <td width=""><strong>��������:</strong></td>
                    <td>�������:</td>
                    <td><input type="text" onkeyup="xlsEditCol()" id="title_col" name="cells[title][col]" style="width:40px" /></td>
                    <td>������:</td>
                    <td><input type="text" onkeyup="xlsEditRow()" id="title_row" name="cells[title][row]" style="width:40px" /></td>
                    <td width="90"><input type="checkbox" id="ignore_title" name="cells[title][ignore]" onclick="ignoreRow('title')" value="1"/> �����:</td>
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
                        <td width="150"><strong><?php echo $value; ?>:</strong></td>
                        <td>�������:</td>
                        <td><input type="text" class="col" id="<?php echo $current; ?>" name="cells[<?php echo $current; ?>][col]" style="width:40px" /></td>
                        <td>������:</td>
                        <td><input type="text" class="row" name="cells[<?php echo $current; ?>][row]" style="width:40px" /></td>
                        <td><input type="checkbox" id="ignore_<?php echo $current; ?>" name="cells[<?php echo $current; ?>][ignore]" onclick="ignoreRow('<?php echo $current; ?>')" value="1" /> �����:</td>
                        <td><input type="text" class="other" name="cells[<?php echo $current; ?>][other]" style="width:200px" disabled /></td>
                    </tr>
                <?php
                $current++;
            }

            if ($cat['view_type']=='shop'){
                ?>
                    <tr id="row_price">
                        <td width="250"><strong>����:</strong></td>
                        <td>�������:</td>
                        <td><input type="text" class="col" name="cells[price][col]" style="width:40px" /></td>
                        <td>������:</td>
                        <td><input type="text" class="row" name="cells[price][row]" style="width:40px" /></td>
                        <td><input type="checkbox" id="ignore_price" name="cells[price][ignore]" onclick="ignoreRow('price')" value="1"/> �����: </td>
                        <td><input type="text" class="other" name="cells[price][other]" style="width:200px" disabled /></td>
                    </tr>
                <?php
            }
            ?>
            </table>

            <p>������� ��������� ��������� �������:</p>
            <table width="650" border="0" cellspacing="5" class="proptable">
                <tr>
                    <td width="300">
                        <strong>����������� ������:</strong><br/>
                        <span class="hinttext">���� ��������, ������ ����� �������� �� �����</span>
                    </td>
                    <td>
                        <input name="published" type="radio" value="1" checked="checked" /> ��
                        <input name="published" type="radio" value="0" /> ���
                    </td>
                </tr>
                <tr>
                    <td><strong>��������� �����������:</strong></td>
                    <td>
                        <input name="is_comments" type="radio" value="1" checked="checked" /> ��
                        <input name="is_comments" type="radio" value="0" /> ���
                    </td>
                </tr>
                <?php if ($cat['view_type']=='shop'){ ?>
                <tr>
                    <td>
                        <strong>��������� ����� ����������:</strong><br/>
                        <span class="hinttext">��� ������ ������</span>
                    </td>
                    <td>
                        <input name="canmany" type="radio" value="1" checked="checked" /> ��
                        <input name="canmany" type="radio" value="0" /> ���
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td>
                        <strong>���� �������:</strong><br/>
                        <span class="hinttext">�� �����������</span>
                    </td>
                    <td>
                        <input type="text" name="tags" style="width:300px" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>�����������:</strong><br/>
                        <span class="hinttext">�� �����������</span>
                    </td>
                    <td>
                        <input type="file" name="imgfile" />
                    </td>
                </tr>                
            </table>

            <p>
                <input name="cat_id" type="hidden" id="cat_id" value="<?php echo (int)$_REQUEST['cat_id']; ?>" />
                 <input name="opt" type="hidden" id="opt" value="go_import_xls" />
                 <input name="save" type="submit" id="save" value="�������������" />
                 <input name="back" type="button" id="back" value="������" onclick="window.history.go(-1);"/>
            </p>

            </form><?php

        } else {
            $sql = "SELECT id, title FROM cms_uc_cats ORDER BY title";
            $result = dbQuery($sql);

            if (mysql_num_rows($result)>0){
                echo '<p><strong>�������� ������� ��� ������� �������:</strong></p>';
                echo '<ul>';
                while ($cat = mysql_fetch_assoc($result)){
                    echo '<li><a href="?view=components&do=config&id='.$_REQUEST['id'].'&opt=import_xls&cat_id='.$cat['id'].'">'.$cat['title'].'</a></li>';
                }
                echo '</ul>';
            }
        }

    }

//=================================================================================================//
//=================================================================================================//
			
?>
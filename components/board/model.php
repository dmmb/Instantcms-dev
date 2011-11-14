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

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_board{

    private $where    = '';
    private $group_by = '';
    private $order_by = '';
    private $limit    = '100';
	public $root_cat  = array();
	public $config    = array();

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function __construct(){
        $this->inDB        = cmsDatabase::getInstance();
		$this->inCore      = cmsCore::getInstance();
		$this->config      = self::getConfig();
		$this->root_cat    = $this->inDB->get_fields('cms_board_cats', 'parent_id=0', '*');
		$this->category_id = $this->inCore->request('category_id', 'int', $this->root_cat['id']);
		$this->item_id     = $this->inCore->request('id', 'int', 0);
		$this->page        = $this->inCore->request('page', 'int', 1);
		$this->city        = $this->detectCriteria('city');
		$this->obtype      = $this->detectCriteria('obtype');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCommentTarget($target, $target_id) {

        $result = array();

        switch($target){

            case 'boarditem': $item               = $this->inDB->get_fields('cms_board_items', "id='{$target_id}'", 'title');
                              if (!$item) { return false; }
                              $result['link']     = '/board/read'.$target_id.'.html';
                              $result['title']    = $item['title'];
                              break;

        }

        return ($result ? $result : false);

    }

/* ========================================================================== */
/* ========================================================================== */

    public static function getDefaultConfig() {

        $cfg = array(
                     'showlat'=>1,
                     'photos'=>1,
                     'maxcols'=>1,
                     'public'=>1,
                     'srok'=>1,
                     'pubdays'=>14,
                     'watermark'=>0,
                     'comments'=>1,
                     'aftertime'=>'',
                     'extend'=>0,
                     'vip_enabled'=>0,
                     'vip_prolong'=>0,
                     'vip_max_days'=>30,
                     'vip_day_cost'=>5
               );

        return $cfg;

    }

/* ========================================================================== */
/* ========================================================================== */

    public static function getConfig() {

        $inCore = cmsCore::getInstance();

        $default_cfg = self::getDefaultConfig();
        $cfg         = $inCore->loadComponentConfig('board');
        $cfg         = array_merge($default_cfg, $cfg);

        return $cfg;

    }

// ============================================================================ //
// ============================================================================ //

    private function resetConditions(){

        $this->where        = '';
        $this->group_by     = '';
        $this->order_by     = '';
        $this->limit        = '';

    }
// ============================================================================ //
// ============================================================================ //
    public function where($condition){
        $this->where .= ' AND ('.$condition.')' . "\n";
    }

    public function whereCatIs($cat_id){
        $this->where("i.category_id = '$cat_id'");
        return;
    }

    public function whereCityIs($city) {
        $this->where("i.city = '$city'");
    }

    public function whereTypeIs($type) {
        $this->where("i.obtype = '$type'");
    }

    public function whereUserIs($user_id) {
        $this->where("i.user_id = '$user_id'");
    }

    public function groupBy($field){
        $this->group_by = 'GROUP BY '.$field;
    }

    public function orderBy($field, $direction='ASC'){
        $this->order_by = 'ORDER BY is_vip DESC, '.$field.' '.$direction;
    }

    public function limit($howmany) {
        $this->limitIs(0, $howmany);
    }

    public function limitIs($from, $howmany='') {
        $this->limit = (int)$from;
        if ($howmany){
            $this->limit .= ', '.$howmany;
        }
    }

    public function limitPage($page, $perpage) {
        $this->limitIs(($page-1)*$perpage, $perpage);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategory() {

		if($this->category_id == $this->root_cat['id']){
			$category = $this->root_cat;
		} else {
	        $category = $this->inDB->get_fields('cms_board_cats', "id = '{$this->category_id}'", '*');
		}
		if(!$category) { return false; }

        $category = cmsCore::callEvent('GET_BOARD_CAT', $category);

		$category['perpage'] = $category['perpage'] ? $category['perpage'] : 20;
		if ($category['public'] == -1) { $category['public'] = $this->config['public']; }

        if (!$category['obtypes']){
            $category['obtypes'] = $this->inDB->get_field('cms_board_cats', "NSLeft <= {$category['NSLeft']} AND NSRight >= {$category['NSRight']} AND obtypes <> ''", 'obtypes');
			if(!$category['obtypes']) { $category['obtypes'] = $this->config['obtypes']; }
        }

		$category['cat_city'] = $this->getCatCity($category['id']);

		$category['ob_links'] = $this->getTypesLinks($category['id'], $category['obtypes']);

        return $category;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategoryPath($left_key, $right_key) {

        $path = array();

        $sql = "SELECT id, title, NSLevel
                FROM cms_board_cats
                WHERE NSLeft <= $left_key AND NSRight >= $right_key AND parent_id > 0
                ORDER BY NSLeft";

		$result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        while($pcat = $this->inDB->fetch_assoc($result)){
            $path[] = $pcat;
        }

        return $path;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getSubCats($category_id) {
        $cats = array();

        $sql = "SELECT c.*, IFNULL(COUNT(i.id), 0) as content_count
                FROM cms_board_cats c
                LEFT JOIN cms_board_items i ON i.category_id = c.id AND i.published = 1
                WHERE c.published = 1 AND c.parent_id = '$category_id'
                GROUP BY c.id
                ORDER BY title ASC";
        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        while($cat = $this->inDB->fetch_assoc($result)){
            if (!$cat['obtypes']){
                $cat['obtypes'] = $this->inDB->get_field('cms_board_cats', "NSLeft <= {$cat['NSLeft']} AND NSRight >= {$cat['NSRight']} AND obtypes <> ''", 'obtypes');
            }
			$cat['ob_links'] = $this->getTypesLinks($cat['id'], $cat['obtypes']);
            $cats[] = $cat;
        }

        $cats = cmsCore::callEvent('GET_BOARD_SUBCATS', $cats);
            
        return $cats;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
	public function getAdverts($show_all = false, $is_users = false, $is_coments = false, $is_cats = false){

        $this->deleteOldRecords();
        $this->clearOldVips();

        //���������� �������
        $pub_where = ($show_all ? '1=1' : 'i.published = 1');
        $r_join    = $is_users ? "LEFT JOIN cms_users u ON u.id = i.user_id" : '';
		$r_join   .= $is_cats ? "INNER JOIN cms_board_cats cat ON cat.id = i.category_id" : '';

		$r_select  = $is_users ? ', u.login, u.nickname' : '';
		$r_select .= $is_cats ? ', cat.title, cat.obtypes' : '';

        $sql = "SELECT i.*{$r_select}

                FROM cms_board_items i
				{$r_join}
                WHERE {$pub_where}
                      {$this->where}

                {$this->group_by}                      

                {$this->order_by}\n";

        if ($this->limit){
            $sql .= "LIMIT {$this->limit}";
        }

		$result = $this->inDB->query($sql);

		if(!$this->inDB->num_rows($result)){ return false; }

		$records = array();

		while ($item = $this->inDB->fetch_assoc($result)){

			if($is_coments){
				$item['comments'] = $this->inCore->getCommentsCount('boarditem', $item['id']);
			}
            $item['content']  = nl2br($item['content']);
			$item['content']  = $this->config['auto_link'] ? $this->inCore->parseSmiles($item['content']) : $item['content'];
			$item['title']    = $item['obtype'].' '.$item['title'];
			$item['fpubdate'] = $this->inCore->dateformat($item['pubdate']);
			$item['enc_city'] = urlencode($item['city']);
            if (!$item['file'] || !file_exists(PATH.'/images/board/small/'.$item['file'])){
				$item['file'] = 'nopic.jpg';
			}
            // ����� �������
            $item['moderator'] = $this->checkAccess($item['user_id']);
			$timedifference    = strtotime("now") - strtotime($item['pubdate']);
			$item['is_overdue'] = round($timedifference / 86400) > $item['pubdays'] && $item['pubdays'] > 0;

			$records[] = $item;

		}

		$this->resetConditions();

		$records = cmsCore::callEvent('GET_BOARD_RECORDS', $records);

		return $records;

	}

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getAdvertsCount($show_all = false){

        //���������� �������
        $pub_where = ($show_all ? '1=1' : 'i.published = 1');

        $sql = "SELECT 1

                FROM cms_board_items i

                WHERE {$pub_where}
                      {$this->where}

                {$this->group_by}\n";

		$result = $this->inDB->query($sql);

		return $this->inDB->num_rows($result);

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getRecord($item_id) {

        $this->deleteOldRecords();
        $this->clearOldVips();

        $sql = "SELECT i.*, 
                       a.id as cat_id,
                       a.NSLeft as NSLeft,
                       a.NSRight as NSRight,
                       a.title as cat_title,
                       a.title as category,
                       a.public as public,
                       a.thumb1 as thumb1,
                       a.thumb2 as thumb2,
                       a.thumbsqr as thumbsqr,
                       u.nickname as user,
                       u.login as user_login
                FROM cms_board_items i
				INNER JOIN cms_board_cats a ON a.id = i.category_id
				LEFT JOIN cms_users u ON u.id = i.user_id
                WHERE i.id = '$item_id'";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        $record = $this->inDB->fetch_assoc($result);

		$timedifference 	  = strtotime("now") - strtotime($record['pubdate']);
		$record['is_overdue'] = round($timedifference / 86400) > $record['pubdays'] && $record['pubdays'] > 0;
		$record['fpubdate']   = $record['pubdate'];
		$record['pubdate'] 	  = cmsCore::dateFormat($record['pubdate']);
		$record['vipdate'] 	  = cmsCore::dateFormat($record['vipdate']);
		$record['enc_city']   = urlencode($record['city']);
		$record['title']      = $record['obtype'].' '.$record['title'];
		$record['content']    = nl2br($record['content']);
		$record['content']    = $this->config['auto_link'] ? $this->inCore->parseSmiles($record['content']) : $record['content'];
		$record['moderator']  = $this->checkAccess($record['user_id']);
		if (!$record['file'] || !file_exists(PATH.'/images/board/small/'.$record['file'])){
			$record['file'] = '';
		}

        $record = cmsCore::callEvent('GET_BOARD_RECORD', $record);

        return $record;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function increaseHits($item_id) {
        $this->inDB->query("UPDATE cms_board_items SET hits = hits + 1 WHERE id = '$item_id'");
        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function addRecord($item){

        $item = cmsCore::callEvent('ADD_BOARD_RECORD', $item);

        $sql = "INSERT INTO cms_board_items (category_id, user_id, obtype, title , content, city, pubdate, pubdays, published, file, hits) 
                VALUES ({$item['category_id']}, {$item['user_id']}, '{$item['obtype']}', '{$item['title']}', '{$item['content']}',
                        '{$item['city']}', NOW(), {$item['pubdays']}, {$item['published']}, '{$item['file']}', 0)";

        $this->inDB->query($sql);
		
		$item_id = $this->inDB->get_last_id('cms_board_items');

        return $item_id ? $item_id : false;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function updateRecord($id, $item) {
        $item = cmsCore::callEvent('UPDATE_BOARD_RECORD', $item);

        $sql = "UPDATE cms_board_items
                SET category_id = {$item['category_id']},
                    obtype = '{$item['obtype']}',
                    title = '{$item['title']}',
                    content = '{$item['content']}',
                    city = '{$item['city']}',
					pubdate = '{$item['pubdate']}',
					pubdays = '{$item['pubdays']}',
                    published = '{$item['published']}',
                    file = '{$item['file']}'
                WHERE id = '$id'";

        $this->inDB->query($sql);
        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteRecord($item_id) {

		$inCore = cmsCore::getInstance();

        cmsCore::callEvent('DELETE_BOARD_RECORD', $item_id);

        $item = $this->getRecord($item_id);
        @unlink(PATH.'/images/board/'.$item['file']);
        @unlink(PATH.'/images/board/small/'.$item['file']);
        @unlink(PATH.'/images/board/medium/'.$item['file']);
        $sql = "DELETE FROM cms_board_items WHERE id = '$item_id'";
        $this->inDB->query($sql);

		$inCore->deleteComments('boarditem', $item_id);

		cmsActions::removeObjectLog('add_board', $item_id);
        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteOldRecords() {

        if ($this->config['aftertime']){            
            $time_sql = '';
            switch ($this->config['aftertime']){
                case 'delete':  $time_sql = "DELETE FROM cms_board_items WHERE DATEDIFF(NOW(), pubdate) > pubdays AND pubdays > 0"; break;
                case 'hide':    $time_sql = "UPDATE cms_board_items SET published = 0 WHERE DATEDIFF(NOW(), pubdate) > pubdays AND pubdays > 0"; break;
            }          
            if ($time_sql){
                $this->inDB->query($time_sql);
            }
        }

        return true;

    }

    public function clearOldVips() {

        $this->inDB->query("UPDATE cms_board_items SET is_vip=0 WHERE DATE(vipdate) <= CURRENT_DATE");

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function setVip($id, $days){

        // ���������� ������ VIP � ���� ��������� ������ �� �������,
        // ���� �� ����� ������� VIP �� ����
        $sql = "UPDATE cms_board_items
                SET is_vip = 1, vipdate = DATE_ADD(NOW(), INTERVAL {$days} DAY)
                WHERE id='{$id}' AND is_vip=0
                LIMIT 1";

        $this->inDB->query($sql);

        // �������� ��������� ���� VIP, ���� VIP-������ ��� ���
        $sql = "UPDATE cms_board_items
                SET vipdate = DATE_ADD(vipdate, INTERVAL {$days} DAY)
                WHERE id='{$id}' AND is_vip=1
                LIMIT 1";
                
        $this->inDB->query($sql);

        return true;

    }
/* ==================================================================================================== */
/* ==================================================================================================== */
	
	public function uploadPhoto($old_file = '', $cfg, $cat) {

		// ��������� ����� �������� ����
		$this->inCore->loadClass('upload_photo');
		$inUploadPhoto = cmsUploadPhoto::getInstance();
		// ���������� ���������������� ���������
		$inUploadPhoto->upload_dir    = PATH.'/images/board/';
		$inUploadPhoto->small_size_w  = $cat['thumb1'];
		$inUploadPhoto->medium_size_w = $cat['thumb2'];
		$inUploadPhoto->thumbsqr      = $cat['thumbsqr'];
		$inUploadPhoto->is_watermark  = $cfg['watermark'];
		// ������� �������� ����
		$file = $inUploadPhoto->uploadPhoto($old_file);
		
		return $file;

	}

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getOrder($order='', $default='') {

		if ($this->inCore->inRequest($order)) { 
			$orders = $this->inCore->request($order, 'str');
			cmsUser::sessionPut('ad_'.$order, $orders);
		} elseif(cmsUser::sessionGet('ad_'.$order)) { 
			$orders = cmsUser::sessionGet('ad_'.$order);
		} else {
			$orders = $default; 
		}
		
		return $orders;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function detectCriteria($search = '') {

		$value = urldecode($this->inCore->request($search, 'str'));
		if ($value) {
			if($value == 'all'){
				cmsUser::sessionDel('board_'.$search);
				$value = '';
			} else {
				cmsUser::sessionPut('board_'.$search, $value);
			}
		} elseif(cmsUser::sessionGet('board_'.$search)) { 
			$value = cmsUser::sessionGet('board_'.$search);
		} else {
			$value = ''; 
		}
		
		return $value;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCatCity($cat_id = 0){

		$cat_city = array();

		$cat_id = ($cat_id == $this->root_cat['id']) ? 0 : $cat_id;
		$cat_sql = $cat_id ? "category_id = '$cat_id'" : '1=1';

        $sql = "SELECT city FROM cms_board_items WHERE published = 1 AND {$cat_sql} GROUP BY city";
        $result = $this->inDB->query($sql);

		if ($this->inDB->num_rows($result)){
			while($c = $this->inDB->fetch_assoc($result)){
				$cat_city[] = $c['city'];
			}
		}

        return $cat_city;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getBoardCities($selected='', $cat){

		global $_LANG;

        $html = '<select name="city" onchange="$(\'form#obform\').submit();">';
        $html .= '<option value="all">'.$_LANG['ALL_CITY'].'</option>';
		if ($cat['cat_city']){
			foreach($cat['cat_city'] as $cat_city){
				if (strtolower($selected)==strtolower($cat_city)){
					$s = 'selected="selected"';
				} else {
					$s = '';
				}
				$pretty = htmlspecialchars(ucfirst(strtolower($cat_city)));
				$html .= '<option value="'.$pretty.'" '.$s.'>'.$pretty.'</option>';
			}
		}
        $html .= '</select>';
        return $html;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
	public function getTypesLinks($cat_id, $types){
	
		$html  = '';
		$types = explode("\n", $types);
		foreach($types as $id=>$type){
			$type = trim($type);
			$html .= '<a class="board_cats_a" href="/board/'.$cat_id.'/type/'.urlencode(ucfirst($type)).'">'.ucfirst($type).'</a>, ';
		}
		$html = rtrim($html, ', ');
		return $html;
	
	}
/* ==================================================================================================== */
/* ==================================================================================================== */
	public function getTypesOptions($types='', $selected=''){

		$html  = '';

        if (!$types){
            $types = explode("\n", $this->config['obtypes']);
        } else {
            $types = explode("\n", $types);
        }

		foreach($types as $id=>$type){
			$type = ucfirst(htmlspecialchars(trim($type)));
			if (strtolower($selected) == strtolower($type)){ $sel = 'selected="selected"'; } else { $sel = ''; }
			$html .= '<option value="'.$type.'" '.$sel.'>'.$type.'</option>';
		}
		return $html;
	}
/* ==================================================================================================== */
/* ==================================================================================================== */
	public function orderForm($orderby, $orderto, $category){

		$smarty = $this->inCore->initSmarty('components', 'com_board_order_form.tpl');				
		$smarty->assign('btype', $this->obtype);
		$smarty->assign('btypes', $this->getTypesOptions($category['obtypes'], $this->obtype));
		$smarty->assign('bcity', $this->city);
		$smarty->assign('bcities', $this->getBoardCities($this->city, $category));
		$smarty->assign('orderby', $orderby);
		$smarty->assign('orderto', $orderto);		
		$smarty->assign('action_url', '/board/'.$category['id']);
		ob_start();
		$smarty->display('com_board_order_form.tpl');
		return ob_get_clean();
	}
/* ==================================================================================================== */
/* ==================================================================================================== */
	public function loadedByUser24h($user_id, $album_id){
		return $this->inDB->rows_count('cms_board_items', "user_id = '$user_id' AND category_id = '$album_id' AND pubdate >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
	}
/* ==================================================================================================== */
/* ==================================================================================================== */

	public function checkAccess($user_id){
	
		$inUser = cmsUser::getInstance();

		if ($inUser->id){	
			$access = ($inUser->is_admin || $this->inCore->isUserCan('board/moderate') || $user_id == $inUser->id);
		} else {
			$access = false;
		}
		return $access;

	}

/* ==================================================================================================== */
/* ==================================================================================================== */

}
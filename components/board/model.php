<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_board{

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCommentTarget($target, $target_id) {

        $result = array();

        switch($target){

            case 'boarditem': $item               = $this->inDB->get_fields('cms_board_items', "id={$target_id}", 'title');
                              if (!$item) { return false; }
                              $result['link']     = '/board/read'.$target_id.'.html';
                              $result['title']    = $item['title'];
                              break;

        }

        return ($result ? $result : false);

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getRootCategory() {
       return $this->inDB->get_fields('cms_board_cats', 'parent_id=0', 'id, title');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategory($category_id) {

        $this->deleteOldRecords();

        $category   = $this->inDB->get_fields('cms_board_cats', 'id='.$category_id, '*');
		if (!$category['id']) { cmsCore::error404(); }
        $category   = cmsCore::callEvent('GET_BOARD_CAT', $category);

        if (!$category['obtypes']){
            $category['obtypes'] = $this->inDB->get_field('cms_board_cats', "NSLeft <= {$category['NSLeft']} AND NSRight >= {$category['NSRight']} AND obtypes <> ''", 'obtypes');
        }

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
                WHERE c.published = 1 AND c.parent_id = $category_id
                GROUP BY c.id
                ORDER BY title ASC";
        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        while($cat = $this->inDB->fetch_assoc($result)){
            if (!$cat['obtypes']){
                $cat['obtypes'] = $this->inDB->get_field('cms_board_cats', "NSLeft <= {$cat['NSLeft']} AND NSRight >= {$cat['NSRight']} AND obtypes <> ''", 'obtypes');
            }
            $cats[] = $cat;
        }

        $cats = cmsCore::callEvent('GET_BOARD_SUBCATS', $cats);
            
        return $cats;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getSubCatsCount($category_id) {
        return $this->inDB->rows_count('cms_board_cats', 'parent_id='.$category_id);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getRecords($category_id, $page=1, $perpage=1000, $orderby='pubdate', $orderto='DESC') {
        
        $records = array();
		$inCore = cmsCore::getInstance();
        $this->deleteOldRecords();

        $city_filter = isset($_SESSION['board_city']) ? "AND city = '".$_SESSION['board_city']."'" : '';
        $type_filter = isset($_SESSION['board_type']) ? "AND obtype = '".$_SESSION['board_type']."'" : '';

        $rootcat = $this->inDB->get_fields('cms_board_cats', 'id='.$category_id, 'NSLeft, NSRight');
        $catsql  = "AND (i.category_id = cat.id AND (i.category_id=$category_id OR (cat.NSLeft >= {$rootcat['NSLeft']} AND cat.NSRight <= {$rootcat['NSRight']})))";

        $sql = "SELECT i.*, i.pubdate as fpubdate, u.nickname as user, u.login as user_login
                FROM cms_board_items i, cms_users u, cms_board_cats cat
                WHERE i.user_id = u.id AND i.published = 1 $city_filter $type_filter $catsql
                GROUP BY i.id
                ORDER BY $orderby $orderto
                LIMIT ".($page-1)*$perpage.", $perpage";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        while($item = $this->inDB->fetch_assoc($result)){
            $item['content']    = nl2br($item['content']);
			$item['fpubdate']   = $inCore->dateformat($item['fpubdate']);
            $records[]          = $item;
        }

        $records = cmsCore::callEvent('GET_BOARD_RECORDS', $records);

        return $records;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getRecord($item_id) {

        $this->deleteOldRecords();

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
				LEFT JOIN cms_board_cats a ON a.id = i.category_id
				LEFT JOIN cms_users u ON u.id = i.user_id
                WHERE i.id = '$item_id'";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ cmsCore::error404(); }

        $record = $this->inDB->fetch_assoc($result);

		$timedifference 	  = strtotime("now") - strtotime($record['pubdate']);
		$record['is_overdue'] = bcdiv($timedifference,86400) > $record['pubdays'] && $record['pubdays'] > 0;
		$record['fpubdate']   = $record['pubdate'];
		$record['pubdate'] 	  = cmsCore::dateFormat($record['pubdate']);

        $record = cmsCore::callEvent('GET_BOARD_RECORD', $record);

        return $record;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function increaseHits($item_id) {
        $this->inDB->query("UPDATE cms_board_items SET hits = hits + 1 WHERE id = $item_id");
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
                WHERE id = $id";
        $this->inDB->query($sql);
        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteRecord($item_id) {
        cmsCore::callEvent('DELETE_BOARD_RECORD', $item_id);

        $item = $this->getRecord($item_id);
        @unlink(PATH.'/images/board/'.$item['file']);
        @unlink(PATH.'/images/board/small/'.$item['file']);
        @unlink(PATH.'/images/board/medium/'.$item['file']);
        $sql = "DELETE FROM cms_board_items WHERE id = $item_id";
        $this->inDB->query($sql);
        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteOldRecords() {

        $inCore = cmsCore::getInstance();

        $cfg = $inCore->loadComponentConfig('board');
        if (!isset($cfg['aftertime'])) { $cfg['aftertime'] = ''; }

        if ($cfg['aftertime']){            
            $time_sql = '';
            switch ($cfg['aftertime']){
                case 'delete':  $time_sql = "DELETE FROM cms_board_items WHERE DATEDIFF(NOW(), pubdate) > pubdays AND pubdays > 0"; break;
                case 'hide':    $time_sql = "UPDATE cms_board_items SET published = 0 WHERE DATEDIFF(NOW(), pubdate) > pubdays AND pubdays > 0"; break;
            }          
            if ($time_sql){
                $this->inDB->query($time_sql) or die(mysql_error());
            }
        }

        return true;

    }


/* ==================================================================================================== */
/* ==================================================================================================== */

}
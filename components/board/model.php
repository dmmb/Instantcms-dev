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

    public function getRootCategory() {
       return $this->inDB->get_fields('cms_board_cats', 'parent_id=0', 'id, title');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategory($category_id) {
        $category   = $this->inDB->get_fields('cms_board_cats', 'id='.$category_id, '*');
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

        $city_filter = isset($_SESSION['board_city']) ? "AND city = '".$_SESSION['board_city']."'" : '';
        $type_filter = isset($_SESSION['board_type']) ? "AND obtype = '".$_SESSION['board_type']."'" : '';

        $rootcat = $this->inDB->get_fields('cms_board_cats', 'id='.$category_id, 'NSLeft, NSRight');
        $catsql  = "AND (i.category_id = cat.id AND (i.category_id=$category_id OR (cat.NSLeft >= {$rootcat['NSLeft']} AND cat.NSRight <= {$rootcat['NSRight']})))";

        $sql = "SELECT i.*,
                       IF(DATE_FORMAT(i.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(i.pubdate, '<strong>{$_LANG['TODAY']}</strong>'), DATE_FORMAT(i.pubdate, '%d-%m-%Y'))  as fpubdate,
                       u.nickname as user
                FROM cms_board_items i, cms_users u, cms_board_cats cat
                WHERE i.user_id = u.id AND i.published = 1 $city_filter $type_filter $catsql
                GROUP BY i.id
                ORDER BY $orderby $orderto
                LIMIT ".($page-1)*$perpage.", $perpage";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        while($item = $this->inDB->fetch_assoc($result)){
            $records[] = $item;
        }

        $records = cmsCore::callEvent('GET_BOARD_RECORDS', $records);

        return $records;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getRecord($item_id) {
        $sql = "SELECT i.*, 
                       DATE_FORMAT(i.pubdate, '%d-%m-%Y') as pubdate,
                       a.id as cat_id,
                       a.NSLeft as NSLeft,
                       a.NSRight as NSRight,
                       a.title as cat_title,
                       a.title as category,
                       a.public as public,
                       a.thumb1 as thumb1,
                       a.thumb2 as thumb2,
                       a.thumbsqr as thumbsqr,
                       u.nickname as user
                FROM cms_board_items i, cms_board_cats a, cms_users u
                WHERE i.id = $item_id AND i.category_id = a.id AND i.user_id = u.id";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)){ return false; }

        $record = $this->inDB->fetch_assoc($result);

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
        return true;
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

}
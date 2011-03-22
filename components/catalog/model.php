<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_catalog{

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCommentTarget($target, $target_id) {

        $result = array();

        switch($target){

            case 'catalog': $item            = $this->inDB->get_fields('cms_uc_items', "id={$target_id}", 'title');
                            if (!$item) { return false; }
                            $result['link']  = '/catalog/item'.$target_id.'.html';
                            $result['title'] = $item['title'];
                            break;

        }

        return ($result ? $result : false);

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function deleteItem($id){
        $inCore = cmsCore::getInstance();
        $imageurl = $this->getItemImageUrl($id);

        @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", 0777);
        @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$file.jpg", 0777);
        @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$file.jpg", 0777);

        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/catalog/'.$imageurl);
        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/catalog/small/'.$imageurl.'.jpg');
        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/catalog/medium/'.$imageurl.'.jpg');
        
        $this->inDB->query("DELETE FROM cms_uc_items WHERE id={$id}");
        $this->inDB->query("DELETE FROM cms_tags WHERE target='catalog' AND item_id = {$id}");
        $this->inDB->query("DELETE FROM cms_uc_ratings WHERE item_id = {$id}");
		
		cmsActions::removeObjectLog('add_catalog', $id);
		
        $inCore->deleteComments('catalog', $id);

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function updateItem($id, $item){

        $item = cmsCore::callEvent('UPDATE_CATALOG_ITEM', $item);

        if ($item['file']){
            $item['imageurl'] = $item['file'];
        }

        if (!$item['is_moderate']){ $item['is_moderate']=0; }

        $sql = "UPDATE cms_uc_items
                SET title='{$item['title']}',
				    category_id = '{$item['cat_id']}',
                    pubdate='{$item['pubdate']}',
                    published='{$item['published']}',
                    imageurl='{$item['imageurl']}',
                    fieldsdata='{$item['fields']}',
                    is_comments='{$item['is_comments']}',
                    tags='{$item['tags']}',
                    meta_desc='{$item['meta_desc']}',
                    meta_keys='{$item['meta_keys']}',
                    price='{$item['price']}',
                    canmany='{$item['canmany']}',
                    on_moderate='{$item['on_moderate']}'
                WHERE id = $id
                LIMIT 1";
        
        $this->inDB->query($sql) ;
        
        cmsInsertTags($item['tags'], 'catalog', $id);
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function renewItem($id){        
        cmsCore::callEvent('RENEW_CATALOG_ITEM', $id);
        $sql = "UPDATE cms_uc_items SET pubdate = NOW() WHERE id = $id";
		$this->inDB->query($sql);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function getItemImageUrl($id){
        $imageurl   = $this->inDB->get_field('cms_uc_items', "id={$id}", 'imageurl');
        $imageurl   = cmsCore::callEvent('GET_CATALOG_ITEM_IMAGE', $imageurl);
        return $imageurl;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function addItem($item){
        $inUser = cmsUser::getInstance();

        if (!isset($item['on_moderate'])) { $item['on_moderate'] = 0; }

        $item = cmsCore::callEvent('ADD_CATALOG_ITEM', $item);

		$sql = "INSERT INTO cms_uc_items (category_id, title, pubdate, published, imageurl, fieldsdata, is_comments, tags, rating, meta_desc, meta_keys, price, canmany, user_id, on_moderate)
				VALUES ({$item['cat_id']}, '{$item['title']}', '{$item['pubdate']}', '{$item['published']}',
                        '{$item['file']}', '{$item['fields']}', {$item['is_comments']}, '{$item['tags']}', 0,
                        '{$item['meta_desc']}', '{$item['meta_keys']}', '{$item['price']}', {$item['canmany']}, {$inUser->id}, {$item['on_moderate']})";
		$this->inDB->query($sql);

        $item_id = dbLastId('cms_uc_items');

		cmsInsertTags($item['tags'], 'catalog', $item_id);

        return $item_id;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function copyItem($id, $copies){
        cmsCore::callEvent('COPY_CATALOG_ITEM', $id);

        $sql = "SELECT * FROM cms_uc_items WHERE id = $id";
        $rs = $this->inDB->query($sql);
        if ($this->inDB->num_rows($rs)==1){
            $item = mysql_fetch_row($rs);
            for($c=1; $c<=$copies; $c++){
                //COPY ITEM
                $sql = "INSERT INTO cms_uc_items VALUES (";
                foreach($item as $key=>$value){
                    if ($key>0){ $sql .= "'$value'"; } else { $sql .= "''"; }
                    if ($key<sizeof($item)-1){ $sql .= ", "; } else { $sql .= ')'; }
                }
                $this->inDB->query($sql);
                //COPY ITEM TAGS
                $id = dbLastId('cms_uc_items');
                $sql = "SELECT * FROM cms_tags WHERE target='catalog' AND item_id=".$item_id;
                $rst = $this->inDB->query($sql);
                if ($this->inDB->num_rows($rst)){
                    while ($itag = $this->inDB->fetch_assoc($rst)){
                        $sql = "INSERT INTO cms_tags VALUES ('', '{$itag['tag']}', 'catalog', '$id')";
                        $this->inDB->query($sql);
                    }
                }
            }
        }
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function deleteDiscount($id){
        cmsCore::callEvent('DELETE_CATALOG_DISCOUNT', $id);
        $sql = "DELETE FROM cms_uc_discount WHERE id = $id LIMIT 1";
        $this->inDB->query($sql) ;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function updateDiscount($id, $item){
        $item = cmsCore::callEvent('UPDATE_CATALOG_DISCOUNT', $item);
        $sql = "UPDATE cms_uc_discount
                SET cat_id = '{$item['cat_id']}',
                    title = '{$item['title']}',
                    sign = '{$item['sign']}',
                    value = '{$item['value']}',
                    unit = '{$item['unit']}',
                    if_limit = {$item['if_limit']}
                WHERE id = $id
                LIMIT 1";
        $this->inDB->query($sql);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function addDiscount($item){
        $item = cmsCore::callEvent('ADD_CATALOG_DISCOUNT', $item);
        $sql = "INSERT INTO cms_uc_discount (title, cat_id, sign, value, unit, if_limit)
				VALUES ('{$item['title']}', {$item['cat_id']}, {$item['sign']}, '{$item['value']}', '{$item['unit']}', {$item['if_limit']})";
		$this->inDB->query($sql);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function deleteCategory($id){
        $inCore = cmsCore::getInstance();
        cmsCore::callEvent('DELETE_CATALOG_CAT', $id);
        $sql = "SELECT id FROM cms_uc_items WHERE category_id = $id";
        $result = dbQuery($sql) ;
        if ($this->inDB->num_rows($result)){
            while($item = $this->inDB->fetch_assoc($result)){
                $this->deleteItem($item['id']);
            }
        }
        $ns = $inCore->nestedSetsInit('cms_uc_cats');
        $ns->DeleteNode($id);
        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function updateCategory($id, $cat){
        $inCore = cmsCore::getInstance();
        
        $cat = cmsCore::callEvent('UPDATE_CATALOG_CAT', $cat);

        $ns = $inCore->nestedSetsInit('cms_uc_cats');
        $ns->MoveNode($id, $cat['parent_id']);

        $sql = "UPDATE cms_uc_cats
                SET parent_id = '{$cat['parent_id']}',
                    title = '{$cat['title']}',
                    description = '{$cat['description']}',
                    published = '{$cat['published']}',
                    fieldsstruct = '{$cat['fields']}',
                    view_type = '{$cat['view_type']}',
                    fields_show = '{$cat['fields_show']}',
                    showmore = '{$cat['showmore']}',
                    perpage = '{$cat['perpage']}',
                    showtags = '{$cat['showtags']}',
                    showsort = '{$cat['showsort']}',
                    is_ratings = '{$cat['is_ratings']}',
                    orderby = '{$cat['orderby']}',
                    orderto = '{$cat['orderto']}',
                    showabc = '{$cat['showabc']}',
                    shownew = '{$cat['shownew']}',
                    newint = '{$cat['newint']}',
                    filters = '{$cat['filters']}',
                    is_public = '{$cat['is_public']}',
                    can_edit = '{$cat['can_edit']}'
                WHERE id = $id
                LIMIT 1";
        $this->inDB->query($sql);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function addCategory($cat){

        $inCore = cmsCore::getInstance();
        $cat    = cmsCore::callEvent('ADD_CATALOG_CAT', $cat);

        $ns = $inCore->nestedSetsInit('cms_uc_cats');
		$cat['id'] = $ns->AddNode($cat['parent_id']);

        $sql = "UPDATE cms_uc_cats
                SET parent_id = '{$cat['parent_id']}',
                    title = '{$cat['title']}',
                    description = '{$cat['description']}',
                    published = '{$cat['published']}',
                    fieldsstruct = '{$cat['fields']}',
                    view_type = '{$cat['view_type']}',
                    fields_show = '{$cat['fields_show']}',
                    showmore = '{$cat['showmore']}',
                    perpage = '{$cat['perpage']}',
                    showtags = '{$cat['showtags']}',
                    showsort = '{$cat['showsort']}',
                    is_ratings = '{$cat['is_ratings']}',
                    orderby = '{$cat['orderby']}',
                    orderto = '{$cat['orderto']}',
                    showabc = '{$cat['showabc']}',
                    shownew = '{$cat['shownew']}',
                    newint = '{$cat['newint']}',
                    filters = '{$cat['filters']}',
                    is_public = '{$cat['is_public']}',
                    can_edit = '{$cat['can_edit']}'
                WHERE id = {$cat['id']}
                LIMIT 1";
        $this->inDB->query($sql);

        return $cat['id'];
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function copyCategory($id, $copies){
        cmsCore::callEvent('COPY_CATALOG_CAT', $id);
        $sql = "SELECT * FROM cms_uc_cats WHERE id = $id";
        $rs = $this->inDB->query($sql) ;
        if ($this->inDB->num_rows($rs)==1){
            $item = mysql_fetch_row($rs);
            for($c=1; $c<=$copies; $c++){
                $sql = "INSERT INTO cms_uc_cats VALUES (";
                foreach($item as $key=>$value){
                    if ($key>0){ $sql .= "'$value'"; } else { $sql .= "''"; }
                    if ($key<sizeof($item)-1){ $sql .= ", "; } else { $sql .= ')'; }
                }
                $this->inDB->query($sql);
            }
        }
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategoryPath($left_key, $right_key) {

        $path = array();

        $sql = "SELECT id, title, NSLevel
                FROM cms_uc_cats
                WHERE NSLeft <= $left_key AND NSRight >= $right_key AND parent_id > 0
                ORDER BY NSLeft";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($cat = $this->inDB->fetch_assoc($result)){
            $path[] = $cat;
        }

        return $path;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getSubCats($parent_id, $left_key, $right_key) {

        $subcats=array();

        $sql = "SELECT cat.*
                FROM cms_uc_cats cat
                WHERE cat.parent_id = '$parent_id' AND cat.published = 1
                ORDER BY cat.title";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($subcat = $this->inDB->fetch_assoc($result)){

            $count_sql = "SELECT con.id
                          FROM cms_uc_items con, cms_uc_cats cat
                          WHERE con.category_id = cat.id AND (cat.NSLeft >= {$subcat['NSLeft']} AND cat.NSRight <= {$subcat['NSRight']}) AND con.published = 1";

            $count_result = $this->inDB->query($count_sql);

            $subcat['content_count'] = $this->inDB->num_rows($count_result);

            $subcats[] = $subcat;

        }

        $subcats = cmsCore::callEvent('GET_CATALOG_SUBCATS', $subcats);

        return $subcats;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function setCategoryAccess($id, $showfor_list){

        $this->clearCategoryAccess($id);

        if (!sizeof($showfor_list)){ return true; }

        foreach ($showfor_list as $key=>$value){
            $sql = "INSERT INTO cms_uc_cats_access (cat_id, group_id)
                    VALUES ($id, $value)";
            $this->inDB->query($sql);
        }

        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function clearCategoryAccess($id){

        $sql = "DELETE FROM cms_uc_cats_access WHERE cat_id = $id";

        $this->inDB->query($sql);

        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function checkCategoryAccess($cat_id, $cat_public, $group_id) {
        return ($cat_public && $this->inDB->rows_count('cms_uc_cats_access', "cat_id={$cat_id} AND group_id={$group_id}", 1));
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function addModerationItem($item_id) {

    }

}
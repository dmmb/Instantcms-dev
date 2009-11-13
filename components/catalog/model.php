<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_catalog{

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function deleteItem($id){
        $inCore = cmsCore::getInstance();
        $imageurl = $this->getItemImageUrl($id);

        @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", 0777);
        @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$file.jpg", 0777);
        @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$file.jpg", 0777);

        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/catalog'.$imageurl);
        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/catalog/small/'.$imageurl.'.jpg');
        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/catalog/medium/'.$imageurl.'.jpg');
        
        $this->inDB->query("DELETE FROM cms_uc_items WHERE id={$id}");
        $this->inDB->query("DELETE FROM cms_tags WHERE target='catalog' AND item_id = {$id}");
        $this->inDB->query("DELETE FROM cms_comments WHERE target = 'catalog' AND target_id = {$id}");
        $this->inDB->query("DELETE FROM cms_uc_ratings WHERE item_id = {$id}");
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function updateItem($id, $item){

        $item = cmsCore::callEvent('UPDATE_CATALOG_ITEM', $item);

        $sql = "UPDATE cms_uc_items
                SET title='{$item['title']}',
                    pubdate='{$item['pubdate']}',
                    published='{$item['published']}',
                    imageurl='{$item['imageurl']}',
                    fieldsdata='{$item['fields']}',
                    is_comments='{$item['is_comments']}',
                    tags='{$item['tags']}',
                    meta_desc='{$item['meta_desc']}',
                    meta_keys='{$item['meta_keys']}',
                    price='{$item['price']}',
                    canmany='{$item['canmany']}'
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
        $inDB = cmsDatabase::getInstance();
        $item = cmsCore::callEvent('ADD_CATALOG_ITEM', $item);

		$sql = "INSERT INTO cms_uc_items (category_id, title, pubdate, published, imageurl, fieldsdata, is_comments, tags, rating, meta_desc, meta_keys, price, canmany)
				VALUES ({$item['cat_id']}, '{$item['title']}', '{$item['pubdate']}', '{$item['published']}',
                        '{$item['file']}', '{$item['fields']}', {$item['is_comments']}, '{$item['tags']}', 0,
                        '{$item['meta_desc']}', '{$item['meta_keys']}', '{$item['price']}', {$item['canmany']})";
		$this->inDB->query($sql);

		cmsInsertTags($item['tags'], 'catalog', dbLastId('cms_uc_items'));
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
        cmsCore::callEvent('DELETE_CATALOG_CAT', $id);
        $sql = "SELECT id FROM cms_uc_items WHERE category_id = $id";
        $result = dbQuery($sql) ;
        if ($this->inDB->num_rows($result)){
            while($item = $this->inDB->fetch_assoc($result)){
                $this->deleteItem($item['id']);
            }
        }
        $sql = "DELETE FROM cms_uc_cats WHERE id = $id LIMIT 1";
        $this->inDB->query($sql);
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
                    filters = '{$cat['filters']}'
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
                    filters = '{$cat['filters']}'
                WHERE id = {$cat['id']}
                LIMIT 1";
        $this->inDB->query($sql);
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

        $sql = "SELECT cat.*, IFNULL(COUNT(con.id), 0) as content_count
                FROM cms_uc_cats cat
                LEFT JOIN cms_uc_items con ON con.category_id = cat.id AND con.published = 1
                WHERE (cat.parent_id=$parent_id) AND cat.published = 1
                GROUP BY cat.id";

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
}
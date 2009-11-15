<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_catalog{

	function __construct(){}

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function deleteItem($id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        $imageurl = $this->getItemImageUrl($id);

        @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", 0777);
        @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$file.jpg", 0777);
        @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$file.jpg", 0777);

        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/catalog'.$imageurl);
        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/catalog/small/'.$imageurl.'.jpg');
        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/catalog/medium/'.$imageurl.'.jpg');
        
        $inDB->query("DELETE FROM cms_uc_items WHERE id={$id}");
        $inDB->query("DELETE FROM cms_tags WHERE target='catalog' AND item_id = {$id}");
        $inDB->query("DELETE FROM cms_comments WHERE target = 'catalog' AND target_id = {$id}");
        $inDB->query("DELETE FROM cms_uc_ratings WHERE item_id = {$id}");
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function updateItem($id, $item){

        $inDB = cmsDatabase::getInstance();

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
        $inDB->query($sql) ;
        cmsInsertTags($item['tags'], 'catalog', $id);
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function renewItem($id){        
        $inDB = cmsDatabase::getInstance();
        cmsCore::callEvent('RENEW_CATALOG_ITEM', $id);
        $sql = "UPDATE cms_uc_items SET pubdate = NOW() WHERE id = $id";
		$inDB->query($sql);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function getItemImageUrl($id){
        $inDB       = cmsDatabase::getInstance();
        $imageurl   = $inDB->get_field('cms_uc_items', "id={$id}", 'imageurl');
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
		$inDB->query($sql);

		cmsInsertTags($item['tags'], 'catalog', dbLastId('cms_uc_items'));
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function copyItem($id, $copies){
        $inDB = cmsDatabase::getInstance();

        cmsCore::callEvent('COPY_CATALOG_ITEM', $id);

        $sql = "SELECT * FROM cms_uc_items WHERE id = $id";
        $rs = $inDB->query($sql);
        if ($inDB->num_rows($rs)==1){
            $item = mysql_fetch_row($rs);
            for($c=1; $c<=$copies; $c++){
                //COPY ITEM
                $sql = "INSERT INTO cms_uc_items VALUES (";
                foreach($item as $key=>$value){
                    if ($key>0){ $sql .= "'$value'"; } else { $sql .= "''"; }
                    if ($key<sizeof($item)-1){ $sql .= ", "; } else { $sql .= ')'; }
                }
                $inDB->query($sql);
                //COPY ITEM TAGS
                $id = dbLastId('cms_uc_items');
                $sql = "SELECT * FROM cms_tags WHERE target='catalog' AND item_id=".$item_id;
                $rst = $inDB->query($sql);
                if ($inDB->num_rows($rst)){
                    while ($itag = $inDB->fetch_assoc($rst)){
                        $sql = "INSERT INTO cms_tags VALUES ('', '{$itag['tag']}', 'catalog', '$id')";
                        $inDB->query($sql);
                    }
                }
            }
        }
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function deleteDiscount($id){
        $inDB = cmsDatabase::getInstance();
        cmsCore::callEvent('DELETE_CATALOG_DISCOUNT', $id);
        $sql = "DELETE FROM cms_uc_discount WHERE id = $id LIMIT 1";
        $inDB->query($sql) ;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function updateDiscount($id, $item){
        $inDB = cmsDatabase::getInstance();
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
        $inDB->query($sql);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function addDiscount($item){
        $inDB = cmsDatabase::getInstance();
        $item = cmsCore::callEvent('ADD_CATALOG_DISCOUNT', $item);
        $sql = "INSERT INTO cms_uc_discount (title, cat_id, sign, value, unit, if_limit)
				VALUES ('{$item['title']}', {$item['cat_id']}, {$item['sign']}, '{$item['value']}', '{$item['unit']}', {$item['if_limit']})";
		$inDB->query($sql);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function deleteCategory($id){
        $inDB = cmsDatabase::getInstance();
        cmsCore::callEvent('DELETE_CATALOG_CAT', $id);
        $sql = "SELECT id FROM cms_uc_items WHERE category_id = $id";
        $result = dbQuery($sql) ;
        if ($inDB->num_rows($result)){
            while($item = $inDB->fetch_assoc($result)){
                $this->deleteItem($item['id']);
            }
        }
        $sql = "DELETE FROM cms_uc_cats WHERE id = $id LIMIT 1";
        $inDB->query($sql);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function updateCategory($id, $cat){
        $inDB = cmsDatabase::getInstance();
        $cat = cmsCore::callEvent('UPDATE_CATALOG_CAT', $cat);
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
        $inDB->query($sql);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function addCategory($cat){
        $inDB = cmsDatabase::getInstance();
        $cat = cmsCore::callEvent('ADD_CATALOG_CAT', $cat);
        $sql = "INSERT INTO cms_uc_cats (parent_id, title, description, published, fieldsstruct, view_type, fields_show, showmore, perpage, showtags, showsort, is_ratings, orderby, orderto, showabc, shownew, newint, filters)
				VALUES ({$cat['parent_id']}, '{$cat['title']}', '{$cat['description']}', '{$cat['published']}',
                        '{$cat['fields']}', '{$cat['view_type']}', '{$cat['fields_show']}', {$cat['showmore']}, {$cat['perpage']},
                        {$cat['showtags']}, {$cat['showsort']}, {$cat['is_ratings']}, '{$cat['orderby']}', '{$cat['orderto']}', {$cat['showabc']},
                        {$cat['shownew']}, '{$cat['newint']}', {$cat['filters']})";
		$inDB->query($sql);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function copyCategory($id, $copies){
        $inDB = cmsDatabase::getInstance();
        cmsCore::callEvent('COPY_CATALOG_CAT', $id);
        $sql = "SELECT * FROM cms_uc_cats WHERE id = $id";
        $rs = $inDB->query($sql) ;
        if ($inDB->num_rows($rs)==1){
            $item = mysql_fetch_row($rs);
            for($c=1; $c<=$copies; $c++){
                $sql = "INSERT INTO cms_uc_cats VALUES (";
                foreach($item as $key=>$value){
                    if ($key>0){ $sql .= "'$value'"; } else { $sql .= "''"; }
                    if ($key<sizeof($item)-1){ $sql .= ", "; } else { $sql .= ')'; }
                }
                $inDB->query($sql);
            }
        }
    }

}
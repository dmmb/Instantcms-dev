<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_content{

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        cmsCore::registerAction('content', 'add', 'добавл€ет статью', 'cms_content');
        cmsCore::registerAction('content', 'remove', 'удал€ет статью', 'cms_content');

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategory($category_id) {

        $sql = "SELECT *
                FROM cms_category
                WHERE id = '$category_id'
                LIMIT 1";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        $cat = $this->inDB->fetch_assoc($result);

        $cat = cmsCore::callEvent('GET_CONTENT_CAT', $cat);

        return $cat;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategoryByLink($seolink) {

        $sql = "SELECT *
                FROM cms_category
                WHERE seolink = '$seolink'
                LIMIT 1";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        $cat = $this->inDB->fetch_assoc($result);

        $cat = cmsCore::callEvent('GET_CONTENT_CAT', $cat);

        return $cat;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategoryPath($left_key, $right_key) {
        
        $path = array();

        $sql = "SELECT id, title, NSLevel, seolink
                FROM cms_category
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

        $sql = "SELECT cat.*, IFNULL(COUNT(con.id), 0) as content_count, cat.seolink as seolink
                FROM cms_category cat
                LEFT JOIN cms_content con ON con.category_id = cat.id AND con.published = 1
                WHERE (cat.parent_id=$parent_id) AND cat.published = 1
                GROUP BY cat.id";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($subcat = $this->inDB->fetch_assoc($result)){

            $count_sql = "SELECT con.id
                          FROM cms_content con, cms_category cat
                          WHERE con.category_id = cat.id AND (cat.NSLeft >= {$subcat['NSLeft']} AND cat.NSRight <= {$subcat['NSRight']}) AND con.published = 1";

            $count_result = $this->inDB->query($count_sql);

            $subcat['content_count'] = $this->inDB->num_rows($count_result);

            $subcats[] = $subcat;
            
        }

        $subcats = cmsCore::callEvent('GET_CONTENT_SUBCATS', $subcats);

        return $subcats;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getSubCatsCount($parent_id) {

        return $this->inDB->rows_count('cms_category', 'parent_id='.$parent_id);
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getArticles($category_id, $page=1, $perpage=100, $orderby='title', $orderto='asc') {

        $articles = array();

        $sql = "SELECT con.*, 
                       DATE_FORMAT(con.pubdate, '%d-%m-%Y') as fpubdate,
                       DATE_FORMAT(con.pubdate, '%H:%i') as fpubtime,
                       u.nickname as author,
                       u.login as user_login
                FROM cms_content con, cms_users u
                WHERE con.category_id = $category_id AND con.published = 1 AND con.is_arhive = 0 AND con.user_id = u.id
                ORDER BY con.".$orderby." ".$orderto."
                LIMIT ".(($page-1)*$perpage).", $perpage";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($article = $this->inDB->fetch_assoc($result)){
            $articles[] = $article;
        }

        $articles = cmsCore::callEvent('GET_ARTICLES', $articles);

        return $articles;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getSeoLink($article){

        $seolink    = '';
        
        $category   = $this->inDB->get_fields('cms_category', "id={$article['category_id']}", 'NSLeft, NSRight');

        $left_key   = $category['NSLeft'];
        $right_key  = $category['NSRight'];

        $path_list  = $this->getCategoryPath($left_key, $right_key);

        if ($path_list){
            foreach($path_list as $pcat){
                if ($pcat['id']!=1){
                    $seolink .= cmsCore::strToURL($pcat['title']) . '/';
                }
            }
        }

        $seolink .= cmsCore::strToURL($article['title']);

        return $seolink;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategorySeoLink($category){

        $seolink    = '';

        //—троим путь к разделу
        $keys       = $this->inDB->get_fields('cms_category', "id={$category['id']}", 'NSLeft, NSRight');

        $left_key   = $keys['NSLeft'] + 1;
        $right_key  = $keys['NSRight'] + 1;

        $path_list  = $this->getCategoryPath($left_key, $right_key);

        if ($path_list){
            foreach($path_list as $pcat){
                if ($pcat['id']!=1){
                    $seolink .= cmsCore::strToURL($pcat['title']) . '/';
                }
            }
        }

        $seolink .= cmsCore::strToURL($category['title']);

        //ќбновл€ем пути всех статей этого раздела
        $sql = "SELECT id, title FROM cms_content WHERE category_id = {$category['id']}";

        $result = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)){

            while($article = $this->inDB->fetch_assoc($result)){

                $article_seolink = $seolink . '/' . cmsCore::strToURL($article['title']);

                $this->inDB->query("UPDATE cms_content SET seolink='{$article_seolink}' WHERE id={$article['id']}");

            }

        }

        return $seolink;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getArticle($article_id) {

		$sql = "SELECT con.*, DATE_FORMAT(con.pubdate, '%d-%m-%Y (%H:%i)') pubdate,
						DATE_FORMAT(con.pubdate, '%d-%m-%Y') shortdate,
						cat.title cat_title, cat.id cat_id, cat.NSLeft as leftkey, cat.NSRight as rightkey, cat.showtags as showtags,
						u.nickname as author, con.user_id as user_id
				FROM cms_content con, cms_category cat, cms_users u
				WHERE con.id = $article_id AND con.category_id = cat.id AND con.user_id = u.id AND con.published = 1";

		$result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        $article = $this->inDB->fetch_assoc($result);

        $article = cmsCore::callEvent('GET_ARTICLE', $article);

        return $article;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getArticleByLink($seolink) {

		$sql = "SELECT con.*, DATE_FORMAT(con.pubdate, '%d-%m-%Y (%H:%i)') pubdate,
						DATE_FORMAT(con.pubdate, '%d-%m-%Y') shortdate,
						cat.title cat_title, cat.id cat_id, cat.NSLeft as leftkey, cat.NSRight as rightkey, cat.showtags as showtags,
						u.nickname as author, con.user_id as user_id
				FROM cms_content con, cms_category cat, cms_users u
				WHERE con.seolink = '$seolink' AND con.category_id = cat.id AND con.user_id = u.id AND con.published = 1";

		$result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        $article = $this->inDB->fetch_assoc($result);

        $article = cmsCore::callEvent('GET_ARTICLE', $article);

        return $article;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getArticleURL($menuid, $seolink, $page=1){

        $page_section = ($page>1 ? '/page-'.$page : '');

        $url = '/content/'.$menuid.'/'.$seolink.$page_section.'.html';

        return $url;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategoryURL($menuid, $seolink, $page=1){

        $page_section = ($page>1 ? '/page-'.$page : '');

        $url = '/content/'.$menuid.'/'.$seolink.$page_section;

        return $url;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function increaseHits($article_id) {

        $this->inDB->query("UPDATE cms_content SET hits = hits + 1 WHERE id = $article_id");

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getRelatedThread($article_id) {
        return $this->inDB->get_field('cms_forum_threads', "rel_to='content' AND rel_id={$article_id}", 'id');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function deleteArticle($id, $forum_delete=false){
        
        cmsCore::callEvent('DELETE_ARTICLE', $id);

        $this->inDB->query("DELETE FROM cms_content WHERE id={$id}");
        $this->inDB->query("DELETE FROM cms_content_access WHERE content_id={$id}");
        $this->inDB->query("DELETE FROM cms_comments WHERE target='article' AND target_id={$id}");
        $this->inDB->query("DELETE FROM cms_ratings WHERE target='content' AND item_id={$id}");
        $this->inDB->query("DELETE FROM cms_tags WHERE target='content' AND item_id={$id}");

        if ($forum_delete){
            $inCore = cmsCore::getInstance();
            $inCore->loadModel('forum');
            $forum_model = new cms_model_forum();
            $forum_model->deleteAutoThread('content', $id);
        }

        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteArticles($id_list, $forum_delete=false){
        foreach($id_list as $key=>$id){
            $this->deleteArticle($id, $forum_delete);
        }
        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function addArticle($article){
        $inCore = cmsCore::getInstance();

        $article['seolink'] = $this->getSeoLink($article);

        $article = cmsCore::callEvent('ADD_ARTICLE', $article);

        $sql = "INSERT INTO cms_content (category_id, user_id, pubdate, enddate, is_end, title, description, content, published, hits, meta_desc, meta_keys, showtitle, showdate, showlatest, showpath, ordering, comments, seolink, canrate, pagetitle)
				VALUES ({$article['category_id']}, {$article['user_id']}, '{$article['pubdate']}', '{$article['enddate']}',
                         {$article['is_end']}, '{$article['title']}', '{$article['description']}', '{$article['content']}', {$article['published']}, 0,
                        '{$article['meta_desc']}', '{$article['meta_keys']}', '{$article['showtitle']}', '{$article['showdate']}', '{$article['showlatest']}',
                        '{$article['showpath']}', 1, {$article['comments']}, '{$article['seolink']}', {$article['canrate']}, '{$article['pagetitle']}')";

		$this->inDB->query($sql) ;

		$id = $this->inDB->get_last_id('cms_content');

        $inCore->loadLib('tags');
		cmsInsertTags($article['tags'], 'content', $id);

        return $id;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function updateArticle($id, $article){
        $inCore  = cmsCore::getInstance();

        $article['seolink'] = $this->getSeoLink($article);

        $article = cmsCore::callEvent('UPDATE_ARTICLE', $article);

        $sql = "UPDATE cms_content
                SET category_id = {$article['category_id']},
                    pubdate = '{$article['pubdate']}',
                    enddate = '{$article['enddate']}',
                    is_end = {$article['is_end']},
                    title='{$article['title']}',
                    description='{$article['description']}',
                    content='{$article['content']}',
                    published={$article['published']},
                    meta_desc='{$article['meta_desc']}',
                    meta_keys='{$article['meta_keys']}',
                    showtitle={$article['showtitle']},
                    showdate={$article['showdate']},
                    showlatest={$article['showlatest']},
                    showpath={$article['showpath']},
                    comments={$article['comments']},
                    seolink='{$article['seolink']}',
                    canrate={$article['canrate']},
                    pagetitle='{$article['pagetitle']}'
                WHERE id = $id
                LIMIT 1";

        $this->inDB->query($sql) ;

        $inCore->loadLib('tags');
        cmsInsertTags($article['tags'], 'content', $id);

        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function setArticleAccess($id, $showfor_list){

        if (!sizeof($showfor_list)){ return true; }

        $sql = "DELETE FROM cms_content_access WHERE content_id = $id";
        
        $this->inDB->query($sql);

        foreach ($showfor_list as $key=>$value){
            $sql = "INSERT INTO cms_content_access (content_id, content_type, group_id)
                    VALUES ($id, 'material', $value)";
            $this->inDB->query($sql);
        }
        
        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}
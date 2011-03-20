<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_content{

    private $where      = '';
    private $group_by   = '';
    private $order_by   = '';
    private $limit      = '100';
    
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

            case 'article': $article            = $this->inDB->get_fields('cms_content', "id='{$target_id}'", 'seolink, title');
                            if (!$article) { return false; }
                            $result['link']     = $this->getArticleURL(null, $article['seolink']);
                            $result['title']    = $article['title'];
                            break;

        }

        return ($result ? $result : false);

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategory($category_id) {

        if (!$category_id){
            $where = 'NSLevel = 0';
        } else {
            $where = 'id = '.$category_id;
        }

        $sql = "SELECT *
                FROM cms_category
                WHERE {$where}
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

        $sql = "SELECT id, title, NSLevel, seolink, url
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

    public function getCatsTree() {

        $subcats=array();

        $sql = "SELECT  cat.id as id,
                        cat.title as title,
                        cat.NSLeft as NSLeft,
                        cat.NSRight as NSRight,
                        cat.NSLevel as NSLevel,
                        cat.seolink as seolink
                FROM cms_category cat
                WHERE cat.NSLevel>0
                ORDER BY cat.NSLeft";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($subcat = $this->inDB->fetch_assoc($result)){

            $subcats[] = $subcat;

        }

        $subcats = cmsCore::callEvent('GET_CONTENT_CATS_TREE', $subcats);

        return $subcats;

    }

    public function getSubCats($parent_id, $left_key, $right_key) {

        $subcats=array();

        $sql = "SELECT cat.*
                FROM cms_category cat
                WHERE cat.parent_id = '$parent_id' AND cat.published = 1";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($subcat = $this->inDB->fetch_assoc($result)){

            $count_sql = "SELECT con.id
                          FROM cms_content con
						  INNER JOIN cms_category cat ON cat.id = con.category_id AND (cat.NSLeft >= {$subcat['NSLeft']} AND cat.NSRight <= {$subcat['NSRight']})
                          WHERE con.published = 1 AND con.is_arhive = 0";

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

    private function resetConditions(){

        $this->where        = '';
        $this->group_by     = '';
        $this->order_by     = '';
        $this->limit        = '';

    }

    public function where($condition){
        $this->where .= ' AND ('.$condition.')' . "\n";
    }

    public function whereCatIs($category_id) {
        $this->where("con.category_id = {$category_id}");
    }

    public function groupBy($field){
        $this->group_by = "GROUP BY {$field}";
    }

    public function orderBy($field, $direction='ASC'){
        $this->order_by = "ORDER BY {$field} {$direction}";
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

    public function getArticlesList($only_published=true) {

//        $this->reorder();

        $articles = array();

		$today    = date("Y-m-d H:i:s");

        if ($only_published){
            $this->where("con.published = 1 AND (con.is_end=0 OR (con.is_end=1 AND con.enddate >= '$today' AND con.pubdate <= '$today'))");
        }

        $sql = "SELECT con.*,
                       con.pubdate as fpubdate,
                       u.nickname as author,
                       u.login as user_login

                FROM cms_content con

				LEFT JOIN cms_users u ON u.id = con.user_id

                WHERE con.is_arhive = 0
                      {$this->where}

                {$this->group_by}                      
                
                {$this->order_by}\n";

        if ($this->limit){
            $sql .= "LIMIT {$this->limit}";
        }

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($article = $this->inDB->fetch_assoc($result)){
			$article['fpubdate'] = date('d.m.Y', strtotime($article['fpubdate']));
            $articles[] = $article;
        }

        $articles = cmsCore::callEvent('GET_ARTICLES', $articles);

        $this->resetConditions();

        return $articles;

    }

    public function getArticlesCount($only_published=true) {

        $articles = array();

		$today    = date("Y-m-d H:i:s");

        if ($only_published){
            $this->where("con.published = 1 AND (con.is_end=0 OR (con.is_end=1 AND con.enddate >= '$today' AND con.pubdate <= '$today'))");
        }

        $sql = "SELECT 1

                FROM cms_content con

                WHERE con.is_arhive = 0
                      {$this->where}

                {$this->group_by}

                {$this->order_by}\n";

        $result = $this->inDB->query($sql);

        return $this->inDB->num_rows($result);

    }

    public function reorder() {

        $table      = 'cms_content';
        $cat_field  = 'category_id';
        $item_field = 'id';

        $sql = "SELECT {$cat_field} FROM {$table} GROUP BY {$cat_field}";
        $res = $this->inDB->query($sql);

        while($r = $this->inDB->fetch_assoc($res)){

            $ord = 1;

            $sql2 = "SELECT {$item_field}
                     FROM {$table}
                     WHERE {$cat_field} = {$r[$cat_field]}
                     ORDER BY ordering";

            $res2 = $this->inDB->query($sql2);

            while($r2 = $this->inDB->fetch_assoc($res2)){
                $this->inDB->query("UPDATE {$table} SET ordering = {$ord} WHERE {$item_field}={$r2[$item_field]} AND {$cat_field}={$r[$cat_field]}");
                $ord++;
            }

        }

        return true;

    }

    public function moveItem($item_id, $cat_id, $dir, $step=1) {

        $sign   = $dir>0 ? '+' : '-';

        $current = $this->inDB->get_field('cms_content', "id={$item_id}", 'ordering');

        if ($dir>0){
            //движение вверх
            //у элемента следующего за текущим нужно уменьшить порядковый номер
            $sql = "UPDATE cms_content
                    SET ordering = ordering-1
                    WHERE category_id={$cat_id} AND ordering = ({$current}+1)
                    LIMIT 1";
            $this->inDB->query($sql);
        }
        if ($dir<0){
            //движение вниз
            //у элемента предшествующего текущему нужно увеличить порядковый номер
            $sql = "UPDATE cms_content
                    SET ordering = ordering+1
                    WHERE category_id={$cat_id} AND ordering = ({$current}-1)
                    LIMIT 1";
            $this->inDB->query($sql);
        }

        $sql    = "UPDATE cms_content
                   SET ordering = ordering {$sign} {$step}
                   WHERE id={$item_id}";
        $this->inDB->query($sql);

        return true;

    }

    public function moveArticlesToCat($articles, $to_cat_id) {

        $ids = rtrim(implode(',', $articles), ',');

        $this->inDB->query("UPDATE cms_content SET category_id = {$to_cat_id} WHERE id IN ({$ids})");

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getArticles($category_id, $page=1, $perpage=100, $orderby='title', $orderto='asc') {

        $articles = array();
		$today = date("Y-m-d H:i:s");
        $sql = "SELECT con.*, 
                       con.pubdate as fpubdate,
                       u.nickname as author,
                       u.login as user_login
                FROM cms_content con
				LEFT JOIN cms_users u ON u.id = con.user_id
                WHERE con.category_id = $category_id AND con.published = 1 AND con.is_arhive = 0
                      AND (con.is_end=0 OR (con.is_end=1 AND con.enddate >= '$today' AND con.pubdate <= '$today'))
                ORDER BY con.".$orderby." ".$orderto."
                LIMIT ".(($page-1)*$perpage).", $perpage";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($article = $this->inDB->fetch_assoc($result)){
			$article['fpubdate'] = cmsCore::dateFormat($article['fpubdate']);
            $articles[] = $article;
        }

        $articles = cmsCore::callEvent('GET_ARTICLES', $articles);

        //Переносим в архив просроченные статьи
        $sql = "UPDATE cms_content SET is_arhive = 1 WHERE is_end = 1 AND enddate < NOW()";
        $this->inDB->query($sql);

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
                    $seolink .= cmsCore::strToURL(($pcat['url'] ? $pcat['url'] : $pcat['title'])) . '/';
                }
            }
        }

        $seolink .= cmsCore::strToURL(($article['url'] ? $article['url'] : $article['title']));

        if ($article['id']){
            $where = ' AND id<>'.$article['id'];
        } else {
            $where = '';
        }

        $is_exists = $this->inDB->rows_count('cms_content', "seolink='{$seolink}'".$where, 1);

        if ($is_exists) { $seolink .= '-' . $article['id']; }

        return $seolink;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategorySeoLink($category){

        $seolink    = '';

        //Строим путь к разделу
        $keys       = $this->inDB->get_fields('cms_category', "id={$category['id']}", 'NSLeft, NSRight');

        $left_key   = $keys['NSLeft'] + 1;
        $right_key  = $keys['NSRight'] + 1;

        $path_list  = $this->getCategoryPath($left_key, $right_key);

        if ($path_list){
            foreach($path_list as $pcat){
                if ($pcat['id']!=1){
                    $seolink .= cmsCore::strToURL(($pcat['url'] ? $pcat['url'] : $pcat['title'])) . '/';
                }
            }
        }

        $seolink .= cmsCore::strToURL(($category['url'] ? $category['url'] : $category['title']));

        //Обновляем пути всех статей этого раздела
        $sql = "SELECT id, title, url FROM cms_content WHERE category_id = '{$category['id']}'";

        $result = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)){

            while($article = $this->inDB->fetch_assoc($result)){

                $article_seolink = $seolink . '/' . cmsCore::strToURL(($article['url'] ? $article['url'] : $article['title']));

                $this->inDB->query("UPDATE cms_content SET seolink='{$article_seolink}' WHERE id='{$article['id']}'");

                //обновляем ссылки на комментарии
                $comments_sql = "UPDATE cms_comments c,
                                        cms_content a
                                 SET c.target_link = CONCAT('/content/', a.seolink, '.html')
                                 WHERE a.id = '{$article['id']}' AND
                                 c.target = 'article' AND c.target_id = a.id";
                
                $this->inDB->query($comments_sql);

            }

        }

        return $seolink;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getArticle($article_id) {
		$today = date("Y-m-d H:i:s");
		$sql = "SELECT  con.*,
						cat.title cat_title, cat.id cat_id, cat.NSLeft as leftkey, cat.NSRight as rightkey, cat.modgrp_id,
						cat.showtags as showtags, cat.seolink as catseolink, u.nickname as author, u.login as user_login
				FROM cms_content con
				LEFT JOIN cms_category cat ON cat.id = con.category_id
				LEFT JOIN cms_users u ON u.id = con.user_id
				WHERE con.id = '$article_id' LIMIT 1";

		$result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        $article = $this->inDB->fetch_assoc($result);

        $article = cmsCore::callEvent('GET_ARTICLE', $article);

        return $article;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getArticleByLink($seolink) {

		$sql = "SELECT con.*,
						cat.title cat_title, cat.id cat_id, cat.NSLeft as leftkey, cat.NSRight as rightkey, cat.showtags as showtags,
						cat.modgrp_id, u.nickname as author, con.user_id as user_id, u.login as user_login
				FROM cms_content con
				LEFT JOIN cms_category cat ON cat.id = con.category_id
				LEFT JOIN cms_users u ON u.id = con.user_id
				WHERE con.seolink = '$seolink' AND (con.is_end=0 OR (con.is_end=1 AND con.enddate >= '$today' AND con.pubdate <= '$today')) LIMIT 1";

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

        $url = '/'.$seolink.$page_section.'.html';

        return $url;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCategoryURL($menuid, $seolink, $page=1, $pagetag = false){

        if (!$pagetag){
            $page_section = ($page>1 ? '/page-'.$page : '');
        } else {
            $page_section = '/page-%page%';
        }

        $url = '/'.$seolink.$page_section;

        return $url;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function increaseHits($article_id) {

        $this->inDB->query("UPDATE cms_content SET hits = hits + 1 WHERE id = '$article_id'");

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getRelatedThread($article_id) {
        return $this->inDB->get_field('cms_forum_threads', "rel_to='content' AND rel_id='$article_id'", 'id');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function deleteArticle($id, $forum_delete=false){

        $inCore = cmsCore::getInstance();

        cmsCore::callEvent('DELETE_ARTICLE', $id);

        $this->inDB->query("DELETE FROM cms_content WHERE id='$id'");
        $this->inDB->query("DELETE FROM cms_content_access WHERE content_id='$id'");
        $this->inDB->query("DELETE FROM cms_tags WHERE target='content' AND item_id='$id'");

        cmsActions::removeObjectLog('add_article', $id);

		@unlink(PATH.'/images/photos/small/article'.$id.'.jpg');
		@unlink(PATH.'/images/photos/medium/article'.$id.'.jpg');
	   
        $inCore->deleteRatings('content', $id);
        $inCore->deleteComments('article', $id);

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

        $article = cmsCore::callEvent('ADD_ARTICLE', $article);

        if ($article['url']) { $article['url'] = cmsCore::strToURL($article['url']); }

        $sql = "INSERT INTO cms_content (category_id, user_id, pubdate, enddate, 
                                         is_end, title, description, content,
                                         published, hits, meta_desc, meta_keys,
                                         showtitle, showdate, showlatest,
                                         showpath, ordering, comments, seolink,
                                         canrate, pagetitle, url, tpl)
				VALUES ('{$article['category_id']}', '{$article['user_id']}', '{$article['pubdate']}', '{$article['enddate']}',
                         '{$article['is_end']}', '{$article['title']}', '{$article['description']}', '{$article['content']}', '{$article['published']}', 0,
                        '{$article['meta_desc']}', '{$article['meta_keys']}', '{$article['showtitle']}', '{$article['showdate']}', '{$article['showlatest']}',
                        '{$article['showpath']}', 1, '{$article['comments']}', '',
                        '{$article['canrate']}', '{$article['pagetitle']}', '{$article['url']}', '{$article['tpl']}')";

		$this->inDB->query($sql) ;

		$article['id'] = $this->inDB->get_last_id('cms_content');

        if ($article['id']){

            $article['seolink'] = $this->getSeoLink($article);
            $this->inDB->query("UPDATE cms_content SET seolink='{$article['seolink']}' WHERE id = '{$article['id']}'");

            $inCore->loadLib('tags');
            cmsInsertTags($article['tags'], 'content', $article['id']);

            if ($article['published']) { cmsCore::callEvent('ADD_ARTICLE_DONE', $article); }
            
        }

        return $article['id'] ? $article['id'] : false;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function updateArticle($id, $article, $not_upd_seo = false){

        $inCore = cmsCore::getInstance();
        $inUser = cmsUser::getInstance();

        $article['id'] = $id;

		if(!$not_upd_seo){
			if ($article['url']) { 
				$article['url']  = cmsCore::strToURL($article['url']);
				$article_url_sql = "url='{$article['url']}',";
			}
			$article['seolink'] = $this->getSeoLink($article);
			$article_seo_sql = "seolink='{$article['seolink']}',";
		}

        if (!$article['user_id']) { $article['user_id'] = $inUser->id; }

        $article = cmsCore::callEvent('UPDATE_ARTICLE', $article);        

        $sql = "UPDATE cms_content
                SET category_id = {$article['category_id']},
                    pubdate = '{$article['pubdate']}',
                    enddate = '{$article['enddate']}',
                    is_end = '{$article['is_end']}',
                    title='{$article['title']}',
                    description='{$article['description']}',
                    content='{$article['content']}',
                    published='{$article['published']}',
                    meta_desc='{$article['meta_desc']}',
                    meta_keys='{$article['meta_keys']}',
                    showtitle='{$article['showtitle']}',
                    showdate='{$article['showdate']}',
                    showlatest='{$article['showlatest']}',
                    showpath='{$article['showpath']}',
                    comments='{$article['comments']}',
                    $article_seo_sql
                    canrate='{$article['canrate']}',
                    pagetitle='{$article['pagetitle']}',
                    user_id='{$article['user_id']}',
                    $article_url_sql
                    tpl='{$article['tpl']}'
                WHERE id = '$id'
                LIMIT 1";

        $this->inDB->query($sql);

        $inCore->loadLib('tags');
        cmsInsertTags($article['tags'], 'content', $article['id']);

		if(!$not_upd_seo){
			//обновляем ссылки меню
			$menuid = $this->inDB->get_field('cms_menu', "linktype='content' AND linkid={$id}", 'id');
			if ($menuid){
				$menulink = $inCore->getMenuLink('content', $id, $menuid);
				$this->inDB->query("UPDATE cms_menu SET link='{$menulink}' WHERE id='{$menuid}'");
			}
	
			//обновляем ссылки на комментарии
			$comments_sql = "UPDATE cms_comments c,
									cms_content a
							 SET c.target_link = CONCAT('/content/', a.seolink, '.html')
							 WHERE a.id = '$id' AND
								   c.target = 'article' AND c.target_id = a.id";
	
			$this->inDB->query($comments_sql);
		}

        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function publishArticle($article_id, $flag=1){

        $this->inDB->query("UPDATE cms_content SET published = '$flag' WHERE id = '$article_id'");
        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function setArticleAccess($id, $showfor_list){

        if (!sizeof($showfor_list)){ return true; }

        $this->clearArticleAccess($id);

        foreach ($showfor_list as $key=>$value){
            $sql = "INSERT INTO cms_content_access (content_id, content_type, group_id)
                    VALUES ('$id', 'material', '$value')";
            $this->inDB->query($sql);
        }
        
        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function clearArticleAccess($id){

        $sql = "DELETE FROM cms_content_access WHERE content_id = '$id' AND content_type = 'material'";

        $this->inDB->query($sql);

        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getNestedArticles($category_id) {

        $cat = $this->getCategory($category_id);

        $sql = "SELECT  con.id as id, con.title as title
				FROM    cms_content con
				JOIN cms_category cat ON cat.id = con.category_id AND
                                         cat.NSLeft >= {$cat['NSLeft']} AND
                                         cat.NSRight <= {$cat['NSRight']}
				";

		$result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        $articles = array();

        while($article = $this->inDB->fetch_assoc($result)){
            $articles[] = $article['id'];
        }

        return $articles ? $articles : false;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteCategory($id, $is_with_content = false) {

        if ($is_with_content){
            $articles = $this->getNestedArticles($id);
            foreach($articles as $article_id){
                $this->deleteArticle($article_id);
            }
        }

        $this->inDB->deleteNS('cms_category', $id);

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}
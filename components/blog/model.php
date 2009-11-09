<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_blog{

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

    public function install(){

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getBlog($id){

        $sql = "SELECT *
				FROM cms_blogs
				WHERE id = $id
				LIMIT 1";
		$result = $this->inDB->query($sql);

        $blog = $this->inDB->num_rows($result) ? $this->inDB->fetch_assoc($result) : false;
        $blog = cmsCore::callEvent('GET_BLOG', $blog);

		return $blog;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getUserBlogId($user_id){

        $blog_id = $this->inDB->get_field('cms_blogs', "user_id={$user_id} AND owner='user'", "id");

        return $blog_id ? $blog_id : false;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getBlogByLink($seolink){

        $sql = "SELECT *
				FROM cms_blogs
				WHERE seolink = '$seolink'
				LIMIT 1";
		$result = $this->inDB->query($sql);

        $blog = $this->inDB->num_rows($result) ? $this->inDB->fetch_assoc($result) : false;
        $blog = cmsCore::callEvent('GET_BLOG', $blog);

		return $blog;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getPostSeoLink($post){

        $seolink = cmsCore::strToURL($post['title']);

        return $seolink;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getBlogSeoLink($blog){

        if ($blog['owner'] == 'user'){
            $seolink = cmsCore::strToURL($blog['title']);
        }

        if ($blog['owner'] == 'club'){
            $club    = $this->inDB->get_field('cms_clubs', "id = {$blog['user_id']}", 'title');
            $seolink = cmsCore::strToURL($club);
        }

        //Обновляем пути всех постов этого блога
        $sql = "SELECT id, title FROM cms_blog_posts WHERE blog_id = {$blog['id']}";

        $result = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)){

            while($post = $this->inDB->fetch_assoc($result)){

                $post_seolink = $this->getPostSeoLink($post);

                $this->inDB->query("UPDATE cms_blog_posts SET seolink='{$post_seolink}' WHERE id={$post['id']}");

            }

        }

        return $seolink;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getPostShort($post_content, $post_url){

        $regex      = '/\[(cut=)\s*(.*?)\]/i';
        $matches    = array();
        preg_match_all( $regex, $post_content, $matches, PREG_SET_ORDER );

        if (is_array($matches)){

            $elm        = $matches[0];
            $elm[0]     = str_replace('[', '', $elm[0]);
            $elm[0]     = str_replace(']', '', $elm[0]);

            parse_str( $elm[0], $args );

            $cut_title  = $args['cut'];

            $pages  = preg_split( $regex, $post_content );

            if ($pages) { $post_content = $pages[0]; }

            $post_content .= '<div class="blog_cut_link">
                                    <a href="'.$post_url.'">'.$cut_title.'</a>
                              </div>';

        }

        return $post_content;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getPostURL($menuid, $bloglink, $seolink){

        $url = '/blogs/'.$menuid.'/'.$bloglink.'/'.$seolink.'.html';

        return $url;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getBlogURL($menuid, $bloglink, $page=1, $cat_id=0){

        $cat_section  = ($cat_id >0 ? '/cat-'.$cat_id   : '');
        $page_section = ($page   >1 ? '/page-'.$page    : '');

        $url = '/blogs/'.$menuid.'/'.$bloglink.'/'.$cat_section.$page_section;

        return $url;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function addBlog($item){

        if (!$item['forall']) { $item['forall'] = 0; }
        if (!$item['owner']) { $item['owner'] = 'user'; }

        $item['seolink'] = '';

        $item       = cmsCore::callEvent('ADD_BLOG', $item);
        
        $sql        = "INSERT INTO cms_blogs (user_id, title, pubdate, allow_who, ownertype, premod, forall, owner, seolink)
                       VALUES ('{$item['user_id']}', '{$item['title']}', NOW(), '{$item['allow_who']}', '{$item['ownertype']}', 0,
                               {$item['forall']}, '{$item['owner']}', '{$item['seolink']}')";
        
        $result     = $this->inDB->query($sql);
        $blog_id    = $this->inDB->get_last_id('cms_blogs');

        if ($blog_id){
            
            $item['id'] = $blog_id;
            $item['seolink'] = $this->getBlogSeoLink($item);

            $this->inDB->query("UPDATE cms_blogs SET seolink='{$item['seolink']}' WHERE id = {$blog_id}");

        }

        return $blog_id ? $blog_id : false;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function updateBlogAuthors($id, $authors){

        //Удаляем прежний набор авторов
        $this->inDB->query("DELETE FROM cms_blog_authors WHERE blog_id = ".$id);

        $authors = cmsCore::callEvent('UPDATE_BLOG_AUTHORS', $authors);

        //Сохраняем всех авторов из нового списка в базу
        foreach ($authors as $key=>$author_id){
            $author_id = (int)$author_id;
            $sql = "INSERT INTO cms_blog_authors (user_id, blog_id, description, startdate)
                    VALUES ($author_id, $id, '', NOW())";
            $this->inDB->query($sql);
        }

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function updateBlog($id, $item){

        if (!$item['forall']) { $item['forall'] = 0; }
        if (!$item['owner']) { $item['owner'] = 'user'; }
        
        $item['id']         = $id;

        $item['seolink']    = $this->getBlogSeoLink($item);

        $item = cmsCore::callEvent('UPDATE_BLOG', $item);

        //Сохраняем настройки блога
        $sql = "UPDATE cms_blogs
                SET title='{$item['title']}',
                    allow_who='{$item['allow_who']}',
                    showcats={$item['showcats']},
                    ownertype='{$item['ownertype']}',
                    premod={$item['premod']},
                    forall={$item['forall']},
                    owner='{$item['owner']}',
                    seolink='{$item['seolink']}'
                WHERE id = $id";

        $this->inDB->query($sql);

        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getSingleBlogsCount(){
        return $this->inDB->rows_count('cms_blogs', "ownertype='single' AND owner='user'");
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getMultiBlogsCount(){
        return $this->inDB->rows_count('cms_blogs', "ownertype='multi' AND owner='user'");
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getBlogs($ownertype){
        $list = array();

        //Формируем запрос
        $sql = "SELECT u.id, b. * , u.id AS uid, u.nickname AS author,
                       COUNT(p.id) as records,
                       IFNULL( SUM( r.points ) , 0 ) AS points
                FROM cms_users u, cms_blogs b
                LEFT JOIN cms_blog_posts p ON p.blog_id = b.id
                LEFT JOIN cms_ratings r ON r.item_id = p.id AND r.target = 'blogpost'
                WHERE b.user_id = u.id ";

        //Добавляем к запросу ограничение по типу хозяина (пользователи или клубы)
        if ($ownertype!='all') { 
            $sql .= "AND ownertype='$ownertype' AND owner='user'\n";
        } else {
            $sql .= "AND owner='user'";
        }

        $sql .= "GROUP BY b.id
                 ORDER BY points DESC";

        $result = $this->inDB->query($sql);

        while($blog = $this->inDB->fetch_assoc($result)){
            $list[] = $blog;
        }

        return $list ? cmsCore::callEvent('GET_BLOGS', $list) : false;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getPostsCount($blog_id, $category_id, $owner){

        if ($category_id != -1){
            $cat_sql = "AND p.cat_id = {$category_id}";
        } else {
            $cat_sql = '';
        }

        $sql = "SELECT p.*, DATE_FORMAT(p.pubdate, '%d-%m-%Y (%H:%i)') as fpubdate
                FROM cms_blogs b, cms_blog_posts p
                WHERE p.blog_id = b.id AND b.id = $blog_id AND p.published = 1 AND b.owner = '$owner' {$cat_sql}
                GROUP BY p.id";
        $result = $this->inDB->query($sql);
        return $this->inDB->num_rows($result);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getPosts($blog_id, $page=0, $perpage=0, $category_id=0, $owner='user'){
        $list = array();

        if ($category_id != -1){
            $cat_sql = "AND p.cat_id = {$category_id}";
        } else {
            $cat_sql = '';
        }

        //Получаем записи, относящиеся к нужной странице блога
        $sql = "SELECT p.*, DATE_FORMAT(p.pubdate, '%d-%m-%Y (%H:%i)') as fpubdate, IFNULL(SUM(r.points), 0) as points, u.nickname as author, u.id as author_id
                FROM cms_blogs b, cms_users u, cms_blog_posts p
                LEFT JOIN cms_ratings r ON r.item_id=p.id AND r.target='blogpost'
                WHERE p.blog_id = b.id AND b.id = $blog_id AND p.user_id = u.id AND p.published = 1 AND b.owner = '$owner' {$cat_sql}
                GROUP BY p.id
                ORDER BY p.pubdate DESC";

        if ($page && $perpage) { $sql .= " LIMIT ".(($page-1)*$perpage).", $perpage"; }

        $result = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)){
            while($post = $this->inDB->fetch_assoc($result)){
                $list[] = $post;
            }
        }

        return $list ?  cmsCore::callEvent('GET_POSTS', $list) : false;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getPost($post_id){

	$sql = "SELECT p.*,
                   DATE_FORMAT(p.pubdate, '%d-%m-%Y %H:%i') fpubdate,
                   DATE_FORMAT(p.edit_date, '%d-%m-%Y %H:%i') feditdate,
                   u.nickname as author,
                   u.id as author_id
			FROM cms_blog_posts p, cms_users u
			WHERE p.id = $post_id AND p.user_id = u.id LIMIT 1";

		$result = $this->inDB->query($sql);
		$post   = $this->inDB->num_rows($result) ? $this->inDB->fetch_assoc($result) : false;

        if ($post){  $post = cmsCore::callEvent('GET_POST', $post); }

        return $post;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getPostByLink($seolink){

	$sql = "SELECT p.*,
                   DATE_FORMAT(p.pubdate, '%d-%m-%Y %H:%i') fpubdate,
                   DATE_FORMAT(p.edit_date, '%d-%m-%Y %H:%i') feditdate,
                   u.nickname as author,
                   u.id as author_id
			FROM cms_blog_posts p, cms_users u
			WHERE p.seolink = '$seolink' AND p.user_id = u.id LIMIT 1";

		$result = $this->inDB->query($sql);
		$post   = $this->inDB->num_rows($result) ? $this->inDB->fetch_assoc($result) : false;

        if ($post){  $post = cmsCore::callEvent('GET_POST', $post); }

        return $post;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getLatestPosts($page=1, $perpage=20){
        $list = array();

        $sql = "SELECT p.*,
                       DATE_FORMAT(p.pubdate, '%d-%m-%Y (%H:%i)') as fpubdate,
                       IFNULL(SUM(r.points), 0) as points,
                       u.nickname as author, u.id as author_id,
                       b.allow_who blog_allow_who,
                       b.seolink bloglink,
                       b.title blog_title
                FROM cms_blogs b, cms_users u, cms_blog_posts p
                LEFT JOIN cms_ratings r ON r.item_id=p.id AND r.target='blogpost'
                WHERE p.user_id = u.id AND p.published = 1 AND p.blog_id = b.id
                GROUP BY p.id
                ORDER BY p.pubdate DESC
                LIMIT ".(($page-1)*$perpage).", $perpage";

        $result = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)){
            while($post = $this->inDB->fetch_assoc($result)){
                $list[] = $post;
            }
        }

        return $list ?  cmsCore::callEvent('GET_LATEST_POSTS', $list) : false;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getBestPosts($page=1, $perpage=20){
        $list = array();

        $sql = "SELECT p.*, DATE_FORMAT(p.pubdate, '%d-%m-%Y (%H:%i)') as fpubdate, IFNULL(SUM(r.points), 0) as points, u.nickname as author, u.id as author_id, b.allow_who blog_allow_who, b.seolink bloglink
                FROM cms_blogs b, cms_users u, cms_blog_posts p
                LEFT JOIN cms_ratings r ON r.item_id=p.id AND r.target='blogpost'
                WHERE p.user_id = u.id AND p.published = 1 AND p.blog_id = b.id AND DATEDIFF(NOW(), p.pubdate) <= 7
                GROUP BY p.id
                ORDER BY points DESC
                LIMIT ".(($page-1)*$perpage).", $perpage";

        $result = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)){
            while($post = $this->inDB->fetch_assoc($result)){
                $list[] = $post;
            }
        }

        return $list ?  cmsCore::callEvent('GET_BEST_POSTS', $list) : false;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getBlogCategory($cat_id){
		$sql    = "SELECT * FROM cms_blog_cats WHERE id = $cat_id";
		$result = $this->inDB->query($sql);
		$cat    = $this->inDB->num_rows($result) ? $this->inDB->fetch_assoc($result) : false;
        if ($cat) { $cat = cmsCore::callEvent('GET_BLOG_CAT', $cat); }
        return $cat;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getModerationCount($blog_id){
        return $this->inDB->rows_count('cms_blog_posts', 'blog_id='.$blog_id.' AND published = 0');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getLatestCount(){
        $sql = "SELECT p.*, DATE_FORMAT(p.pubdate, '%d-%m-%Y (%H:%i)') as fpubdate, IFNULL(SUM(r.points), 0) as points
				FROM cms_blogs b, cms_blog_posts p
				LEFT JOIN cms_ratings r ON r.item_id=p.id AND r.target='blogpost'
				WHERE p.published = 1
				GROUP BY p.id";
		$result = $this->inDB->query($sql);
		$total  = $this->inDB->num_rows($result);
        return $total;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getBestCount(){
		$sql = "SELECT p.*, DATE_FORMAT(p.pubdate, '%d-%m-%Y (%H:%i)') as fpubdate, IFNULL(SUM(r.points), 0) as points
				FROM cms_blogs b, cms_blog_posts p
				LEFT JOIN cms_ratings r ON r.item_id=p.id AND r.target='blogpost'
				WHERE p.published = 1 AND DATEDIFF(NOW(), p.pubdate) <= 7
				GROUP BY p.id";
		$result = $this->inDB->query($sql);
		$total  = $this->inDB->num_rows($result);
        return $total;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getModerationPosts($blog_id){

        $list = array();

        $sql = "SELECT p.*, DATE_FORMAT(p.pubdate, '%d-%m-%Y (%H:%i)') as fpubdate, u.nickname as author, u.id as author_id
                FROM cms_blogs b, cms_users u, cms_blog_posts p
                WHERE p.blog_id = b.id AND b.id = $blog_id AND p.user_id = u.id AND p.published = 0
                ORDER BY p.pubdate DESC";
        $result  = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)){
            while($post = $this->inDB->fetch_assoc($result)){
                $list[] = $post;
            }
        }

        return $list ?  cmsCore::callEvent('GET_MODER_POSTS', $list) : false;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getBlogAuthors($blog_id){

        $list = array();

        //Получаем список авторов
        $sql = "SELECT a.*, 
                       p.imageurl as imageurl,
                       u.nickname as nickname,
                       u.id as user_id,
                       u.login as user_login
                FROM cms_blog_authors a
                LEFT JOIN cms_user_profiles p ON p.user_id=a.user_id
                LEFT JOIN cms_users u ON p.user_id=u.id
                WHERE a.blog_id = {$blog_id}
                ORDER BY u.nickname ASC";

        $result = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)){
            while($author = $this->inDB->fetch_assoc($result)){
                $list[] = $author;
            }
        }

        return $list ?  cmsCore::callEvent('GET_BLOG_AUTHORS', $item) : false;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function isUserAuthor($blog_id, $user_id){
        return $this->inDB->get_field('cms_blog_authors', 'blog_id='.$blog_id.' AND user_id='.$user_id, 'id');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function isUserPostAuthor($post_id, $user_id){
        return $this->inDB->get_field('cms_blog_posts', 'id='.$post_id.' AND user_id='.$user_id, 'id');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getClubBlogMinKarma($club_id){
        return $this->inDB->get_field('cms_clubs', 'id='.$club_id, 'blog_min_karma');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function addPost($item){

        $item = cmsCore::callEvent('ADD_POST', $item);

        $item['seolink'] = '';

        $sql = "INSERT INTO cms_blog_posts (user_id, cat_id, blog_id, pubdate, title, feel, music,
                            content, allow_who, edit_times, edit_date, published, seolink)
                VALUES ('{$item['user_id']}', '{$item['cat_id']}', '{$item['id']}', NOW(),
                        '{$item['title']}', '{$item['feel']}', '{$item['music']}', '{$item['content']}',
                        '{$item['allow_who']}', 0, NOW(), {$item['published']}, '{$item['seolink']}')";
        
        $result = $this->inDB->query($sql);

        $post_id = $this->inDB->get_last_id('cms_blog_posts');

        cmsInsertTags($item['tags'], 'blogpost', $post_id);

        if ($post_id){
            $item['id']      = $post_id;
            $item['seolink'] = $this->getPostSeoLink($item);

            $this->inDB->query("UPDATE cms_blog_posts SET seolink='{$item['seolink']}' WHERE id = {$post_id}");
        }

        return $post_id ? $post_id : false;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function addBlogCategory($item){
        $item = cmsCore::callEvent('ADD_BLOG_CAT', $item);
        $sql = "INSERT INTO cms_blog_cats (blog_id, title)
                VALUES ('{$item['id']}', '{$item['title']}')";

        $result = $this->inDB->query($sql);

        $cat_id = $this->inDB->get_last_id('cms_blog_cats');

        return $cat_id ? $cat_id : false;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function updatePost($post_id, $item){
        $item = cmsCore::callEvent('UPDATE_POST', $item);
        $sql = "UPDATE cms_blog_posts
                SET cat_id={$item['cat_id']},
                    pubdate=NOW(),
                    title='{$item['title']}',
                    feel='{$item['feel']}',
                    music='{$item['music']}',
                    content='{$item['content']}',
                    allow_who='{$item['allow_who']}',
                    edit_times = edit_times+1,
                    edit_date = NOW()
                WHERE id = $post_id";
        $result = $this->inDB->query($sql);

        cmsInsertTags($item['tags'], 'blogpost', $post_id);

        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function updateBlogCategory($cat_id, $item){
        $item = cmsCore::callEvent('UPDATE_BLOG_CAT', $item);
        $sql = "UPDATE cms_blog_cats
                SET title='{$item['title']}'
                WHERE id = $cat_id";
        $result = $this->inDB->query($sql);

        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deletePost($post_id){
        cmsCore::callEvent('DELETE_POST', $post_id);
        $inCore = cmsCore::getInstance();

        $inCore->loadLib('tags');

        $this->inDB->query("DELETE FROM cms_blog_posts WHERE id = $post_id");
        $this->inDB->query("DELETE FROM cms_comments WHERE target='blog' AND target_id = '$post_id'");
        $this->inDB->query("DELETE FROM cms_tags WHERE target='blogpost' AND item_id = '$post_id'");
        $this->inDB->query("DELETE FROM cms_ratings WHERE target='blogpost' AND item_id = '$post_id'");

        cmsClearTags('blogpost', $post_id);

        $inCore->deleteUploadImages($post_id, 'blog');

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function publishPost($post_id, $flag=1){

        $this->inDB->query("UPDATE cms_blog_posts SET published = $flag WHERE id = $post_id");
        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteBlog($blog_id){
        cmsCore::callEvent('DELETE_BLOG', $blog_id);
        $inCore = cmsCore::getInstance();
        $posts = $this->inDB->get_table('cms_blog_posts', 'blog_id = '.$blog_id);

        foreach($posts as $key=>$post){
             $this->deletePost($post['id']);
        }

        $this->inDB->query("DELETE FROM cms_blog_posts WHERE blog_id = $blog_id");
        $this->inDB->query("DELETE FROM cms_blogs WHERE id = $blog_id");

        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteBlogCategory($cat_id){
        cmsCore::callEvent('DELETE_BLOG_CAT', $cat_id);
        $inCore = cmsCore::getInstance();
        $posts = $this->inDB->get_table('cms_blog_posts', 'cat_id = '.$cat_id);
        foreach($posts as $key=>$post){
            $this->deletePost($post['id']);
        }
        $this->inDB->query("DELETE FROM cms_blog_cats WHERE id = $cat_id");
        return true;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}
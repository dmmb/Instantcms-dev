<?php
/*********************************************************************************************/
//																							 //
//						   InstantCMS v1.0 (c) 2008 COMMERCIAL VERSION                       //
//   						Source code protected by copyright laws                          //
//                                                                                           //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function isNew($item_id, $shownew, $newint){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    if ($shownew){
        $sql = "SELECT id FROM cms_uc_items WHERE id = $item_id AND pubdate >= DATE_SUB(NOW(), INTERVAL $newint)";
        $result = $inDB->query($sql) ;
        return $inDB->num_rows($result);
    } else { return 0; }
}

function getAlphaList($cat_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
    $html = '';
    $sql = "SELECT UPPER(SUBSTRING(LTRIM( title ) , 1, 1)) AS first_letter, COUNT( id ) AS num
            FROM cms_uc_items
            WHERE category_id = $cat_id AND published = 1
            GROUP BY first_letter";
    $result = $inDB->query($sql) ;
    if ($inDB->num_rows($result)){
        $html .= '<div class="uc_alpha_list">';
        while($a = $inDB->fetch_assoc($result)){
            $html .= '<a class="uc_alpha_link" href="/catalog/'.$cat_id.'/find-first/'.urlencode($a['first_letter']).'" title="'.$_LANG['ARTICLES'].': '.$a['num'].'">'.$a['first_letter'].'</a>';
        }
        $html .= '</div>';
    }
    return $html;
}

function ratingData($item_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $sql = "SELECT *, IFNULL(AVG(points), 0) as rating, COUNT(id) as votes
            FROM cms_uc_ratings
            WHERE item_id = $item_id
            GROUP BY item_id";
    $result = $inDB->query($sql) ;
    if ($inDB->num_rows($result)){
        $data = $inDB->fetch_assoc($result);
    } else {
        $data['rating'] = 0;
        $data['votes'] = 0;
    }
    return $data;
}

function buildRating($rating){
    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    global $_LANG;
    $rating     = round($rating, 2);
    $html = '<a href="#" title="'.$_LANG['RATING'].': '.$rating.'">';
    for($r = 1; $r < 5; $r++){
        if (round($rating) > $r){
            $html .= '<img src="/images/ratings/starfull.gif" border="0" />';
        } else {
            $html .= '<img src="/images/ratings/starhalf.gif" border="0" />';
        }
    }
    $html .= '</a>';
    return $html;
}

function alreadyVoted($item_id){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $ip     = $_SERVER['REMOTE_ADDR'];
    $sql    = "SELECT points FROM cms_uc_ratings WHERE item_id = $item_id AND ip = '$ip' LIMIT 1";
    $result = $inDB->query($sql) ;
    if ($inDB->num_rows($result)){
        $data = $inDB->fetch_assoc($result);
        return (int)$data['points'];
    }    
    return false;
}

function ratingForm($ratingdata, $item_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
    $html = '';
    $html .= '<form name="rateform" action="" method="POST"><div class="uc_detailrating"><table><tr>' ."\n";
    $html .= '<td width="90">'."\n";
    $html .= '<strong>'.$_LANG['RATING'].':</strong> '.round($ratingdata['rating'], 2)."\n";
    $html .= '</td>'."\n";
    $html .= '<td width="100" valign="middle">'."\n";
    $html .= buildRating($ratingdata['rating'])."\n";
    $html .= '</td>'."\n";
    $html .= '<td width="50">'."\n";
    $html .= '<strong>'.$_LANG['VOTES'].':</strong> '."\n";
    $html .= '</td>'."\n";
    $html .= '<td width="40" valign="middle">'."\n";
    $html .= $ratingdata['votes']."\n";
    $html .= '</td>'."\n";
    $html .= '<td width="100">'."\n";
    $html .= '<strong>'.$_LANG['YOUR_VOTE'].':</strong> '."\n";
    $html .= '</td>'."\n";
    $html .= '<td width=""> '."\n";
    $myvote = alreadyVoted($item_id);
    if (!$myvote){
        $html .= '<input type="hidden" name="rating" value="1"/>'."\n";
        $html .= '<input type="hidden" name="item_id" value="'.$item_id.'"/>'."\n";
        $html .= '<select name="points" style="width:50px" onchange="document.rateform.submit();">'."\n";
        $html .= '<option value="-1"> -- </option>'."\n";
        for($p=1; $p<=5; $p++) { $html .= '<option value="'.$p.'">'.$p.'</option>'."\n"; }
        $html .= '</select>'."\n";
    } else {
        $html .= $myvote;
    }
    $html .= '</td>'."\n";
    $html .= '</tr></table></div></form>'."\n";
    return $html;
}

function orderForm($orderby, $orderto, $shop=false){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
    $html = '';
    $html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="POST"><div class="catalog_sortform"><table cellspacing="2" cellpadding="2" >' ."\n";
    $html .= '<tr>' ."\n";
    $html .= '<td>'.$_LANG['ORDER_ARTICLES'].': </td>' ."\n";
    $html .= '<td valign="top"><select name="orderby" id="orderby">' ."\n";
    if($shop){
        $html .= '<option value="price" '; if($orderby=='price') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_PRICE'].'</option>' ."\n";
    }
    $html .= '<option value="title" '; if($orderby=='title') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_TITLE'].'</option>' ."\n";
    $html .= '<option value="pubdate" '; if($orderby=='pubdate') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_DATE'].'</option>' ."\n";
    $html .= '<option value="rating" '; if($orderby=='rating') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_RATING'].'</option>' ."\n";
    $html .= '<option value="hits" '; if($orderby=='hits') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_HITS'].'</option>' ."\n";
    $html .= '</select> <select name="orderto" id="orderto">';
    $html .= '<option value="desc" '; if($orderto=='desc') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_DESC'].'</option>' ."\n";
    $html .= '<option value="asc" '; if($orderto=='asc') { $html .= 'selected'; } $html .= '>'.$_LANG['ORDERBY_ASC'].'</option>' ."\n";
    $html .= '</select>';
    $html .= ' <input type="submit" value=">>" />' ."\n";
    $html .= '</td>' ."\n";
    $html .= '</tr>' ."\n";
    $html .= '</table></div></form>' ."\n";
    return $html;
}

function tagsList($cat_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $html = '';
    $sql = "SELECT t.*, COUNT(t.tag) as num, c.id as cat_id
                FROM cms_tags t, cms_uc_items i, cms_uc_cats c
                WHERE t.target='catalog' AND t.item_id = i.id AND i.category_id = c.id AND c.id = $cat_id
                GROUP BY t.tag
                ORDER BY t.tag";
    $result = $inDB->query($sql) ;
    if ($inDB->num_rows($result)>0){
        while($tag = $inDB->fetch_assoc($result)){
            $html .= '<a href="#" onclick="addTag(\''.strtolower($tag['tag']).'\')">'.strtolower($tag['tag']).'</a> ('.$tag['num'].') ';
        }
    }
    return $html;
}

function tagLine($tagstr, $cat_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $html = '';
    if (!$tagstr) { return ''; }
    $tagstr = str_replace(', ', ',', $tagstr);
    $tagstr = str_replace(' ,', ',', $tagstr);
    $tags = explode(',', $tagstr);
    $num = 0;
    foreach($tags as $key=>$value){
        $value = strtolower($value);
        $html .= '<a href="/catalog/'.$cat_id.'/tag/'.urlencode($value).'">'.$value.'</a>';
        if ($num < sizeof($tags)-1) { $html .= ', '; $num++; }
    }
    return $html;
}

function getContentCount($cat_id, &$total, $inDB){

    $sql = "SELECT c.*, IFNULL(COUNT(i.id), 0) as content_count
            FROM cms_uc_cats c
            LEFT JOIN cms_uc_items i ON i.category_id = c.id AND i.published = 1
            WHERE (c.parent_id = {$cat_id}) AND c.published = 1
            GROUP BY i.category_id
            ORDER BY c.title";

    $result = $inDB->query($sql);

    if ( !$inDB->num_rows($result)>0 ){ return ''; }

    $cats = array();

    while($cat = $inDB->fetch_assoc($result)){
        $total   += $cat['content_count'];
        getContentCount($cat['id'], $total, $inDB);
    }

    return ;
    
}

function subCatsList($parent_id, $left_key, $right_key){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();

    $html   = '';
    $model  = new cms_model_catalog();

    if (!$parent_id) { $parent_id = $inDB->get_field('cms_uc_cats', 'parent_id=0', 'id'); }

    $cats = $model->getSubCats($parent_id, $left_key, $right_key);

    if ($cats){
        $smarty = $inCore->initSmarty('components', 'com_catalog_cats.tpl');
        $smarty->assign('cfg', $inCore->loadComponentConfig('catalog'));
        $smarty->assign('cats', $cats);

        ob_start();

        $smarty->display('com_catalog_cats.tpl');

        $html = ob_get_clean();
    }

    return $html;
    
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function catalog(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();

    global $_LANG;

    $inCore->loadModel('catalog');
    $model = new cms_model_catalog();

    $menutitle  = $inCore->menuTitle();
    if (!$menutitle) { $menutitle = $_LANG['CATALOG']; }
    $cfg        = $inCore->loadComponentConfig('catalog');

    if (!isset($cfg['email'])) { $cfg['email'] = 'shop@site.ru'; }
    if (!isset($cfg['delivery'])) { $cfg['delivery'] = 'Сведения о доставке'; }
    if (!isset($cfg['notice'])) { $cfg['notice'] = 0; }
    if (!isset($cfg['premod'])) { $cfg['premod'] = 1; }
    if (!isset($cfg['premod_msg'])) { $cfg['premod_msg'] = 1; }
    if (!isset($cfg['is_comments'])) { $cfg['is_comments'] = 1; }

    if ($inCore->inRequest('cat_id')){
        $id = $inCore->request('cat_id', 'int', 0);
    } else {
        $id = $inCore->request('id', 'int', 0);
    }

    $do = $inCore->request('do', 'str', 'view');

    $inCore->includeFile('components/catalog/includes/shopcore.php');

    //////////////////////////// RATING SUBMISSION ///////////////////////////////////////////////////////////////////
    if ($inCore->inRequest('rating')){
        $points     = $inCore->request('points', 'int', 0);
        $item_id    = $inCore->request('item_id', 'int', 0);
        $ip         = $_SERVER['REMOTE_ADDR'];
        if (!alreadyVoted($item_id)){
            $inDB->query("INSERT INTO cms_uc_ratings (item_id, points, ip) VALUES ($item_id, $points, '$ip')") ;
            $inDB->query("DELETE FROM cms_uc_ratings WHERE item_id = $item_id AND ip = '0.0.0.0'") ;
        }
    }

    //////////////////////////// SEARCH BY TAG ///////////////////////////////////////////////////////////////////////
    if ($do == 'tag') {

        $tag = $inCore->request('tag', 'str');
        $sql = "SELECT tag FROM cms_tags WHERE tag = '$tag' AND target='catalog' LIMIT 1";
        $result = $inDB->query($sql) ;
        if ($inDB->num_rows($result)==1){
            $item = $inDB->fetch_assoc($result);
            $query = $item['tag'];
            $findsql = "SELECT *
                        FROM cms_uc_items
                        WHERE category_id = $id AND published = 1 AND tags LIKE '%$query%'";
            $do = 'cat';
        } else { echo $_LANG['NO_MATCHING_FOUND']; }

    }
    //////////////////////////// ADVANCED SEARCH ////////////////////////////////////////////////////////////////////
    if ($do == 'search') {

        //Perform search
        if (isset($_POST['gosearch'])){
            $fdata = $_POST['fdata'];
            $title = $inCore->request('title', 'str');

            $query = '';
            $fstr = 'a:%:{';

            if (is_array($fdata)){
                foreach($fdata as $key=>$value) {
                    $value = str_replace("'", '?', $value);
                    $value = str_replace("{", '?', $value);
                    $value = str_replace("}", '?', $value);
                    $value = str_replace(":", '?', $value);
                    $fstr .= 'i:'.$key.';s:%:"%'.trim($value).'%";';
                    $query .= $value;
                }

                $fstr .= '}';
                if ($query=='') { unset($query); }
				$query = $inCore->strClear($query);
                $findsql = "SELECT i.* , IFNULL(AVG(r.points),0) AS rating
                            FROM cms_uc_items i
                            LEFT JOIN cms_uc_ratings r ON r.item_id = i.id
                            WHERE i.published = 1 AND i.category_id = $id AND i.fieldsdata LIKE '%{$query}%' AND i.title LIKE '%$title%'";

                if ($_POST['tags'] != '') {
                    $findsql .= " AND (";
                    $tags = explode(' ', $inCore->request('tags', 'str'));
                    $t = 1;
                    foreach($tags as $key=>$value){
                        $findsql .= "(i.tags LIKE '%".$value."%')";
                        if ($t<sizeof($tags)) { $findsql .= " AND "; } else { $findsql .= ")"; }
                        $t++;
                    }
                }
                $findsql .=	"GROUP BY i.id";
                $advsearch = 1;
            }
            $do = 'cat';
        } else {
            //show search form
            $sql = "SELECT * FROM cms_uc_cats WHERE id = $id";
            $result = $inDB->query($sql) ;

            if ($inDB->num_rows($result)==1){
                $cat = $inDB->fetch_assoc($result);
                $fstruct = unserialize($cat['fieldsstruct']);

                //heading
                $inPage->addPathway($cat['title'], '/catalog/'.$cat['id']);
                $inPage->addPathway($_LANG['SEARCH'], '/catalog/'.$cat['id'].'/search.html');
                $inPage->setTitle($_LANG['SEARCH_IN_CAT'] . ' - ' . $menutitle);

                $inPage->addHeadJS('components/catalog/js/search.js');

                $fstruct_ready = array();
                foreach($fstruct as $key=>$value) {
                    if (strstr($value, '/~h~/')) { $ftype = 'html'; $value=str_replace('/~h~/', '', $value); }
                    elseif (strstr($value, '/~l~/')) { $ftype = 'link'; $value=str_replace('/~l~/', '', $value); } else { $ftype='text'; }
                    if (strstr($value, '/~m~/')) {
                        $value = str_replace('/~m~/', '', $value);
                    }
                    $fstruct_ready[$key] = $value;
                }

                $inPage->backButton(false);

                //searchform
                $smarty = $inCore->initSmarty('components', 'com_catalog_search.tpl');
                $smarty->assign('id', $id);
                $smarty->assign('cat', $cat);
                $smarty->assign('fstruct', $fstruct_ready);
                $smarty->display('com_catalog_search.tpl');

            } else { cmsCore::error404(); }
        }//search form

    }
    //////////////////////////// SEARCH BY FIRST LETTER OF TITLE ///////////////////////////////////////////////////////
    if ($do == 'findfirst') {

        $id = $inCore->request('cat_id', 'int');

        $query = urldecode($inCore->request('text', 'str'));
        $query = str_replace("'", '?', $query);
        $query = str_replace("{", '?', $query);
        $query = str_replace("}", '?', $query);
        $query = str_replace(":", '?', $query);

        $findsql = "SELECT i.* , IFNULL(AVG( r.points ),0) AS rating
                    FROM cms_uc_items i
                    LEFT JOIN cms_uc_ratings r ON r.item_id = i.id
                    WHERE i.published = 1 AND i.category_id = $id AND UPPER(LTRIM(i.title)) LIKE UPPER('$query%')
                    GROUP BY i.id";

        $do = 'cat';
        $advsearch = 0;

        $pagemode = 'findfirst';

    }

    //////////////////////////// SEARCH BY FIELD ////////////////////////////////////////////////////////////////////
    if ($do == 'find') {
        $id = $inCore->request('cat_id', 'int');

        $query = urldecode($inCore->request('text', 'str'));
        $query = str_replace("'", '?', $query);
        $query = str_replace("{", '?', $query);
        $query = str_replace("}", '?', $query);
        $query = str_replace(":", '?', $query);

        $findsql = "SELECT i.* , IFNULL(AVG(r.points),0) AS rating
                    FROM cms_uc_items i
                    LEFT JOIN cms_uc_ratings r ON r.item_id = i.id
                    WHERE i.published = 1 AND i.category_id = $id AND i.fieldsdata LIKE '%$query%'
                    GROUP BY i.id";

        $do = 'cat';
        $advsearch = 0;

        $pagemode = 'find';
    }

    //////////////////////////// LIST OF CATEGORIES ////////////////////////////////////////////////////////////////////
    if ($do == 'view'){ //List of all categories

        $inPage->setTitle($menutitle);
        $cats_html = subCatsList();
        
        $smarty = $inCore->initSmarty('components', 'com_catalog_index.tpl');
        $smarty->assign('cfg', $cfg);
        $smarty->assign('title', $menutitle);
        $smarty->assign('cats_html', $cats_html);
        $smarty->display('com_catalog_index.tpl');

    }

    //////////////////////////// VIEW CATEGORY ///////////////////////////////////////////////////////////////////////
    if ($do == 'cat'){
        //get category data
        $sql = "SELECT * FROM cms_uc_cats WHERE id = $id";
        $catres = $inDB->query($sql);

        $inPage->addHeadJS('includes/jquery/lightbox/js/jquery.lightbox.js');
        $inPage->addHeadCSS('includes/jquery/lightbox/css/jquery.lightbox.css');

        if ($inDB->num_rows($catres)>0){
            
            $cat        = $inDB->fetch_assoc($catres);
            $fstruct    = unserialize($cat['fieldsstruct']);

            //heading
            //PATHWAY ENTRY
            $left_key   = $cat['NSLeft'];
            $right_key  = $cat['NSRight'];

            $path_list  = $model->getCategoryPath($left_key, $right_key);

            if ($path_list){
                foreach($path_list as $pcat){
                    $inPage->addPathway($pcat['title'], '/catalog/'.$pcat['id']);
                }
            }
           
            $inPage->addPathway($cat['title'], '/catalog/'.$cat['id']);
            $inPage->setTitle($cat['title'] . ' - ' . $menutitle);

            //subcategories
            $subcats = subCatsList($cat['id'], $cat['NSLeft'], $cat['NSRight']);

            //alphabetic list
            if ($cat['showabc']){ $alphabet = getAlphaList($cat['id']);	} else { $alphabet = ''; }

            //Tool links
            $shopcartlink = shopCartLink();

            //get items SQL
            if (!isset($findsql)){
                $sql = "SELECT i.* , IFNULL(AVG( r.points ), 0) AS rating, i.price as price
                        FROM cms_uc_items i 
                        LEFT JOIN cms_uc_ratings r ON r.item_id = i.id 
                        WHERE i.published = 1 AND i.category_id = $id
                        GROUP BY i.id";
            } else {
                $sql = $findsql;
                if (!$advsearch){ $inPage->addPathway(ucfirst($query), $_SERVER['REQUEST_URI']); } else
                { $inPage->addPathway($_LANG['SEARCH_RESULT'], $_SERVER['REQUEST_URI']); }
            }

            //ordering
            if (isset($_POST['orderby'])) {
                $orderby = $inCore->request('orderby', 'str');
                $_SESSION['uc_orderby'] = $orderby;
            } elseif(isset($_SESSION['uc_orderby'])) {
                $orderby = $_SESSION['uc_orderby'];
            } else {
                $orderby = $cat['orderby'];
            }

            if (isset($_POST['orderto'])) {
                $orderto = $inCore->request('orderto', 'str');
                $_SESSION['uc_orderto'] = $orderto;
            } elseif(isset($_SESSION['uc_orderto'])) {
                $orderto = $_SESSION['uc_orderto'];
            } else {
                $orderto = $cat['orderto'];
            }

            $sql .=  " ORDER BY ".$orderby." ".$orderto;
            
            //get total items count
            $result = $inDB->query($sql);
            $itemscount = $inDB->num_rows($result);

            //can user add items here?
            $is_cat_access = $model->checkCategoryAccess($cat['id'], $cat['is_public'], $inUser->group_id);
            $is_can_add = $is_cat_access || $inUser->is_admin;

            $smarty = $inCore->initSmarty('components', 'com_catalog_view.tpl');
            $smarty->assign('id', $id);
            $smarty->assign('cat', $cat);
            $smarty->assign('subcats', $subcats);
            $smarty->assign('alphabet', $alphabet);
            $smarty->assign('shopcartlink', $shopcartlink);
            $smarty->assign('itemscount', $itemscount);
            $smarty->assign('is_can_add', $is_can_add);
            $smarty->assign('orderform', orderForm($orderby, $orderto, ($cat['view_type']=='shop')));

            if ($itemscount>0){

                //pagination
                if (!@$advsearch) { $perpage = $cat['perpage']; } else { $perpage='1000'; }
                $page = $inCore->request('page', 'int', 1);

                //request items using pagination
                $sql .= " LIMIT ".(($page-1)*$perpage).", $perpage";
                $result = $inDB->query($sql) ;

                //search details, if needed
                $search_details = '';
                if (isset($findsql)){
                    if ($advsearch){
                        $search_details = '<div class="uc_queryform"><strong>'.$_LANG['SEARCH_RESULT'].' - </strong> '.$_LANG['FOUNDED'].': '.$itemscount.' | <a href="/catalog/'.$cat['id'].'">'.$_LANG['CANCEL_SEARCH'].'</a></div>';
                    } else {
                        $search_details = '<div class="uc_queryform"><strong>'.$_LANG['SEARCH_BY_TAG'].'</strong> "'.ucfirst($query).'" ('.$_LANG['MATCHES'].': '.$itemscount.') <a href="/catalog/'.$cat['id'].'">'.$_LANG['CANCEL_SEARCH'].'</a></div>';
                    }
                }

                $items = array();
                while($item = $inDB->fetch_assoc($result)){
                    $item['ratingdata'] = ratingData($item['id']);
                    $item['fdata'] = unserialize($item['fieldsdata']);
                    $item['price'] = number_format(shopDiscountPrice($item['id'], $item['category_id'], $item['price']), 2, '.', ' ');
                    $item['rating'] = buildRating($item['ratingdata']['rating']);
                    $item['is_new'] = isNew($item['id'], $cat['shownew'], $cat['newint']);
                    $item['tagline'] = tagLine($item['tags'], $cat['id']);

                    $item['can_edit'] = ($cat['can_edit'] && $is_cat_access && ($inUser->id == $item['user_id'])) || $inUser->is_admin;

                    $item['fields'] = array();

                    if (sizeof($fstruct)>0){
                        $fields_show = 0;
                        foreach($fstruct as $key=>$value){
                            if ($fields_show < $cat['fields_show']){

                                if ($item['fdata'][$key]){

                                    if (strstr($value, '/~h~/')){ $value = str_replace('/~h~/', '', $value); $is_html = true; } else { $is_html = false; }
                                    if (strstr($value, '/~m~/')){
                                        $value = str_replace('/~m~/', '', $value);
                                        $makelink = true;
                                    } else {$makelink = false; }
                                    if (!$is_html){
                                        if (strstr($value, '/~l~/')){
                                            if (@$item['fdata'][$key]!=''){
                                                $field = '<a class="uc_fieldlink" href="/load/url='.$item['fdata'][$key].'" target="_blank">'.str_replace('/~l~/', '', $value).'</a> ('.$inCore->fileDownloadCount($item['fdata'][$key]).')';
                                            }
                                        } else {
                                            if ($makelink){
                                                $field = $inCore->getUCSearchLink($cat['id'], null, $key, $item['fdata'][$key]);
                                            } else {
                                                $field = $item['fdata'][$key];
                                            }
                                        }
                                    } else {                                        
                                        $field = $item['fdata'][$key];
                                    }

                                    if (isset($query)) { if (@strstr($query, $fdata[$key]) || @strstr($fdata[$key], $query)) { $field .= '<span class="uc_findsame"> &larr; <i>'.$_LANG['MATCHE'].'</i></span>';} }
                                    $fields_show++;

                                    $item['fields'][$value] = $field;

                                }

                            } else { break; }
                        }
                    }

                    $items[] = $item;
                }

                if (!$pagemode){
                    $pagebar = cmsPage::getPagebar($itemscount, $page, $perpage, '/catalog/'.$id.'-%page%');
                } else {

                    if ($pagemode=='findfirst'){
                        $pagebar = cmsPage::getPagebar($itemscount, $page, $perpage, '/catalog/'.$id.'-%page%/find-first/'.urlencode($query));
                    }

                    if ($pagemode=='find'){
                        $pagebar = cmsPage::getPagebar($itemscount, $page, $perpage, '/catalog/'.$id.'-%page%/find/'.urlencode($query));
                    }

                }

                $smarty->assign('cfg', $cfg);
                $smarty->assign('page', $page);
                $smarty->assign('search_details', $search_details);
                $smarty->assign('fstruct', $fstruct);
                $smarty->assign('items', $items);
                $smarty->assign('pagebar', $pagebar);
            }

            $smarty->display('com_catalog_view.tpl');

        } else { cmsCore::error404(); }

        return true;

    }

    //////////////////////////// VIEW ITEM DETAILS ///////////////////////////////////////////////////////////////////////
    if ($do == 'item'){
        $id = $inCore->request('id', 'int');
        $sql = "SELECT * FROM cms_uc_items WHERE id = $id";
        $itemres = $inDB->query($sql) ;

        $inPage->addHeadJS('includes/jquery/lightbox/js/jquery.lightbox.js');
        $inPage->addHeadCSS('includes/jquery/lightbox/css/jquery.lightbox.css');

        if ($inDB->num_rows($itemres)>0){
            $item = $inDB->fetch_assoc($itemres);

            if ((!$item['published'] || $item['on_premod']) && !$inUser->is_admin){
                $inCore->halt();
            }

            $fdata = unserialize($item['fieldsdata']);

            if ($item['meta_keys']) { $inPage->setKeywords($item['meta_keys']); }
            if ($item['meta_desc']) { $inPage->setDescription($item['meta_desc']); }

            $ratingdata = ratingData($id);

            $sql = "SELECT * FROM cms_uc_cats WHERE id = ".$item['category_id'];
            $catres = $inDB->query($sql) ;
            $cat = $inDB->fetch_assoc($catres);
            $fstruct = unserialize($cat['fieldsstruct']);

            //PATHWAY ENTRY
            $left_key   = $cat['NSLeft'];
            $right_key  = $cat['NSRight'];

            $path_list  = $model->getCategoryPath($left_key, $right_key);

            if ($path_list){
                foreach($path_list as $pcat){
                    $inPage->addPathway($pcat['title'], '/catalog/'.$pcat['id']);
                }
            }

            $inPage->addPathway($item['title'], '/catalog/item'.$item['id'].'.html');
            $inPage->setTitle($item['title'] . ' - ' . $menutitle);


            if ($cat['view_type']=='shop'){

				$shopCartLink=shopCartLink();
				
            }

            //update hits
            $inDB->query("UPDATE cms_uc_items SET hits = hits + 1 WHERE id = ".$id) ;

            //print item details

			
			$fields = array();
			
            if (sizeof($fstruct)>0){
                foreach($fstruct as $key=>$value){
                    if (@$fdata[$key]!=''){
                        if (strstr($value, '/~h~/')){
                            $value = str_replace('/~h~/', '', $value);
                            $htmlfield = true;
                        }
                        if (strstr($value, '/~m~/')){
                            $value = str_replace('/~m~/', '', $value);
                            $makelink = true;
                        } else {$makelink = false; }
                        $field = str_replace('<p>', '<p style="margin-top:0px; margin-bottom:5px">', $fdata[$key]);
                        if (strstr($value, '/~l~/')){
                            $field = '<a class="uc_detaillink" href="/load/url='.$field.'" target="_blank">'.str_replace('/~l~/', '', $value).'</a> ('.$inCore->fileDownloadCount($field).')';

                        } else {
 
                            if (isset($htmlfield)) {
                                if ($makelink) {
                                     $field = $inCore->getUCSearchLink($cat['id'], null, $key, strip_tags($field));
                                } else {
                                    //PROCESS FILTERS, if neccessary
                                    if ($cat['filters']){
                                        $filters = $inCore->getFilters();
                                        if ($filters){
                                            foreach($filters as $id=>$_data){
                                                require_once $_SERVER['DOCUMENT_ROOT'].'/filters/'.$_data['link'].'/filter.php';
                                                $_data['link']($field);
                                            }
                                        }
                                    }
                                     $field =  str_replace('\"', '"', $field);
                                }
                            } else {
                                if ($makelink) {
                                     $field =  $inCore->getUCSearchLink($cat['id'], null, $key, $field);
                                }
                            }
                            
                        }
						$fields[$value] = $field;
                    }
                }
            }
            if ($cat['view_type']=='shop'){
                $already = shopIsInCart($item['id']);
                $item['price'] = number_format(shopDiscountPrice($item['id'], $item['category_id'], $item['price']), 2, '.', ' ');
            }
			
			
            if ($item['on_moderate']){
                $user = $inDB->get_fields('cms_users', "id={$item['user_id']}", 'login, nickname');
                $getProfileLink = cmsUser::getProfileLink($user['login'], $user['nickname']);
            }

            if ($cat['is_ratings']){
				$ratingForm = ratingForm($ratingdata, $item['id']);	

            }

			
			$smarty = $inCore->initSmarty('components', 'com_catalog_item.tpl');
			$smarty->assign('shopCartLink', $shopCartLink);
			$smarty->assign('getProfileLink', $getProfileLink);
			$smarty->assign('tagline', tagLine($item['tags'], $cat['id']));
            $smarty->assign('item', $item);
            $smarty->assign('cat', $cat);
            $smarty->assign('fields', $fields);
			$smarty->assign('ratingForm', $ratingForm);
			$smarty->assign('show_comments', $show_comments);		
			$smarty->display('com_catalog_item.tpl');

            //show user comments
            if($item['is_comments'] && $inCore->isComponentInstalled('comments')){
                $inCore->includeComments();
                comments('catalog', $item['id']);
            }

        } else { cmsCore::error404(); }

        return true;
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////// S H O P /////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////// ADD TO CART /////////////////////////////////////////////////////////////////////////////
    if ($do == 'addcart'){
        shopAddToCart($id, 1);
        header('location:/catalog/viewcart.html');
    }
    ///////////////////////// VIEW CART /////////////////////////////////////////////////////////////////////////////
    if ($do == 'viewcart'){
        $inPage->backButton(false);
        shopCart();
    }
    ///////////////////////// DELETE FROM CART /////////////////////////////////////////////////////////////////////////////
    if ($do == 'cartremove'){
        shopRemoveFromCart($id);
        header('location:'.$_SERVER['HTTP_REFERER']);
    }
    ///////////////////////// CLEAR CART /////////////////////////////////////////////////////////////////////////////
    if ($do == 'clearcart'){
        shopClearCart();
        header('location:'.$_SERVER['HTTP_REFERER']);
    }
    ///////////////////////// CLEAR CART /////////////////////////////////////////////////////////////////////////////
    if ($do == 'savecart'){
        $itemcounts =  $inCore->request('kolvo', 'array');
        if (is_array($itemcounts)){
            shopUpdateCart($itemcounts);
        }
        header('location:'.$_SERVER['HTTP_REFERER']);
    }
    ///////////////////////// ORDER //////////////////////////////////////////////////////////////////////////////////
    if ($do == 'order'){
        shopOrder($cfg);
    }
    ///////////////////////// ORDER //////////////////////////////////////////////////////////////////////////////////
    if ($do == 'finish'){
        shopFinishOrder($cfg);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ($do == 'add_item' || $do == 'edit_item'){

        $cat_id     = $inCore->request('cat_id', 'int');
        $cat        = $inDB->get_fields('cms_uc_cats', 'id='.$cat_id, '*');

        if (!$cat){ cmsCore::error404(); }

        $is_can_add = $model->checkCategoryAccess($cat['id'], $cat['is_public'], $inUser->group_id) || $inUser->is_admin;

        $left_key   = $cat['NSLeft'];
        $right_key  = $cat['NSRight'];
        $path_list  = $model->getCategoryPath($left_key, $right_key);
        if ($path_list){
            foreach($path_list as $pcat){
                $inPage->addPathway($pcat['title'], '/catalog/'.$pcat['id']);
            }
        }

        if ($do == 'add_item'){

            $inPage->setTitle($_LANG['ADD_ITEM']);
            $inPage->addPathway($_LANG['ADD_ITEM']);
            
            if (!$is_can_add){ $inCore->halt(); }

            $item = array();
            $fdata = array();

        }
        
        if ($do == 'edit_item'){

            $inPage->setTitle($_LANG['EDIT_ITEM']);
            $inPage->addPathway($_LANG['EDIT_ITEM']);

            $item_id        = $inCore->request('item_id', 'int', 0);
            $item           = $inDB->get_fields('cms_uc_items', 'id='.$item_id, '*');

            if (!$item) { $inCore->halt(); }

            $is_cat_access  = $model->checkCategoryAccess($cat['id'], $cat['is_public'], $inUser->group_id);
            $is_can_edit    = ($cat['can_edit'] && $is_cat_access && ($inUser->id == $item['user_id'])) || $inUser->is_admin;

            if (!$is_can_edit) { $inCore->halt(); }
            $fdata = unserialize($item['fieldsdata']);

        }

        $fields = array();

        $fstruct = unserialize($cat['fieldsstruct']);

        foreach($fstruct as $f_id=>$value){

            if (strstr($value, '/~h~/')) { $ftype = 'html'; $value=str_replace('/~h~/', '', $value); }
            elseif (strstr($value, '/~l~/')) { $ftype = 'link'; $value=str_replace('/~l~/', '', $value); } else { $ftype='text'; }

            if (strstr($value, '/~m~/')) { $makelink = true; $value=str_replace('/~m~/', '', $value); }
            else { $makelink = false; }

            $next['ftype']      = $ftype;
            $next['title']      = $value;
            $next['makelink']   = $makelink;

            if ($fdata[$f_id]){
                $next['value']  = str_replace('\"', '"', $fdata[$f_id]);
            } else {
                $next['value']  = '';
            }

            $fields[] = $next;
            
        }

        $smarty = $inCore->initSmarty('components', 'com_catalog_add.tpl');
            $smarty->assign('do', $do);
            $smarty->assign('item', $item);
            $smarty->assign('fields', $fields);
            $smarty->assign('cat', $cat);
            $smarty->assign('cfg', $cfg);
            $smarty->assign('is_admin', $inUser->is_admin);
            $smarty->assign('cat_id', $cat_id);
        $smarty->display('com_catalog_add.tpl');

        return;

    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ($do == 'submit_item'){

        $opt        = $inCore->request('opt', 'str', 'add');
        $cat_id     = $inCore->request('cat_id', 'int');
        $cat        = $inDB->get_fields('cms_uc_cats', 'id='.$cat_id, '*');
        $item_id    = $inCore->request('item_id', 'int');

        if ($opt == 'add'){
            $item = array();
            $is_can_add = $model->checkCategoryAccess($cat['id'], $cat['is_public'], $inUser->group_id) || $inUser->is_admin;
            if (!$is_can_add){ $inCore->halt(); }
        }

        if ($opt == 'edit'){
            $item = $inDB->get_fields('cms_uc_items', "id={$item_id}", '*');
            $is_cat_access  = $model->checkCategoryAccess($cat['id'], $cat['is_public'], $inUser->group_id);
            $is_can_edit    = ($cat['can_edit'] && $is_cat_access && ($inUser->id == $item['user_id'])) || $inUser->is_admin;
            if (!$is_can_edit){ $inCore->halt(); }
        }

        $inCore->includeGraphics();

        $inCore->loadLib('tags');

        //get variables
        $item['cat_id']         = $cat_id;
        $item['title']          = $inCore->request('title', 'str');

        $item['published']      = ($cfg['premod']&&!$inUser->is_admin ? 0 : 1);
        $item['on_moderate']    = ($cfg['premod']&&!$inUser->is_admin ? 1 : 0);

        $item['fdata']          = $_POST['fdata'];
        foreach($item['fdata'] as $key=>$value) { $item['fdata'][$key] = trim($value); }

        $item['is_comments']    = $cfg['is_comments'];
        $item['meta_desc']      = $item['title'];
        $item['meta_keys']      = $item['title'];
        $item['tags']           = $inCore->request('tags', 'str');

        $item['pubdate']        = date('Y-m-d H:i');

        $item['canmany']        = 1;

        //get fields data
        $item['fields']         = serialize($item['fdata']);
        $item['fields']         = $inDB->escape_string($item['fields']);

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

        $item['file']   = ($opt == 'add' ? '' : $item['imageurl']);

        if ($inCore->request('delete_img', 'int', 0)){
            
            @unlink($_SERVER['DOCUMENT_ROOT']."/images/catalog/".$item['imageurl']);
            @unlink($_SERVER['DOCUMENT_ROOT']."/images/catalog/small/".$item['imageurl'].".jpg");
            @unlink($_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/".$item['imageurl'].".jpg");

            $item['file'] = '';
            $item['imageurl'] = '';

        } else {

            if (isset($_FILES["imgfile"]["name"]) && @$_FILES["imgfile"]["name"]!=''){
                //generate image file
                $tmp_name       = $_FILES["imgfile"]["tmp_name"];
                $file           = $_FILES["imgfile"]["name"];
                $path_parts     = pathinfo($file);
                $ext            = $path_parts['extension'];
                $file           = md5($file.time()).'.'.$ext;
                $item['file']   = $file;
                //upload image and insert record in db
                if (@move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT']."/images/catalog/$file")){
                    if ($item['imageurl']) {
                        @unlink($_SERVER['DOCUMENT_ROOT']."/images/catalog/".$item['imageurl']);
                        @unlink($_SERVER['DOCUMENT_ROOT']."/images/catalog/small/".$item['imageurl'].".jpg");
                        @unlink($_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/".$item['imageurl'].".jpg");
                    }
					if ( $cfg['watermark'] ) { @img_add_watermark($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file"); }
                    @img_resize($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", $_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$file.jpg", 100, 100);
                    @img_resize($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", $_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$file.jpg", 250, 250);
                    @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/$file", 0744);
                    @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/small/$file.jpg", 0644);
                    @chmod($_SERVER['DOCUMENT_ROOT']."/images/catalog/medium/$file.jpg", 0644);
                }
            }

        }

        if ($opt=='add'){ 
		
				$item_id = $model->addItem($item);
				if (!$cfg['premod'] && !$cfg['premod_msg']) {
					//регистрируем событие
					cmsActions::log('add_catalog', array(
						'object' => $item['title'],
						'object_url' => '/catalog/item'.$item_id.'.html',
						'object_id' => $item_id,
						'target' => $cat['title'],
						'target_url' => '/catalog/'.$cat['id'],
						'target_id' => $cat['id'], 
						'description' => ''
					));
				}
		}
        if ($opt=='edit'){ $model->updateItem($item_id, $item); }

        if ($inUser->id != 1 && $cfg['premod'] && $cfg['premod_msg']){

            $link = '<a href="/catalog/item'.$item_id.'.html">'.$item['title'].'</a>';
            $user = '<a href="'.cmsUser::getProfileURL($inUser->login).'">'.$inUser->nickname.'</a>';

            if ($opt=='add')  { $message = $_LANG['MSG_ITEM_SUBMIT']; }
            if ($opt=='edit') { $message = $_LANG['MSG_ITEM_EDITED']; }
            $message = str_replace('%user%', $user, $message);
            $message = str_replace('%link%', $link, $message);

            cmsUser::sendMessage(USER_UPDATER, 1, $message);
            
        }

        $inCore->redirect('/catalog/'.$cat_id);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ($do == 'accept_item'){

        $item_id = $inCore->request('item_id', 'int');

        if (!$item_id || !$inUser->is_admin){ $inCore->halt(); }

        $inDB->query("UPDATE cms_uc_items SET published=1, on_moderate=0 WHERE id={$item_id}");

        $item = $inDB->get_fields('cms_uc_items', "id={$item_id}", 'title, user_id, category_id');
		
		$cat  = $inDB->get_fields('cms_uc_cats', 'id='.$item['category_id'], 'id, title');
		
		//регистрируем событие
		cmsActions::log('add_catalog', array(
				'object' => $item['title'],
				'user_id' => $item['user_id'],
				'object_url' => '/catalog/item'.$item_id.'.html',
				'object_id' => $item_id,
				'target' => $cat['title'],
				'target_url' => '/catalog/'.$cat['id'],
				'target_id' => $cat['id'], 
				'description' => ''
		));
        
        $item_link  = '<a href="/catalog/item'.$item_id.'.html">'.$item['title'].'</a>';

        $message = str_replace('%link%', $item_link, $_LANG['MSG_ITEM_ACCEPTED']);

        cmsUser::sendMessage(USER_UPDATER, $item['user_id'], $message);

        $inCore->redirectBack();

    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ($do == 'delete_item'){

        $item_id = $inCore->request('item_id', 'int');

        if (!$item_id){ $inCore->halt(); }

        $item = $inDB->get_fields('cms_uc_items', "id={$item_id}", '*');

        if (!($item['user_id']==$inUser->id || $inUser->is_admin)){ $inCore->halt(); }

        $model->deleteItem($item_id);

        $message = str_replace('%item%', $item['title'], $_LANG['MSG_ITEM_REJECTED']);
        cmsUser::sendMessage(USER_UPDATER, $item['user_id'], $message);

        $inCore->redirect('/catalog/'.$item['category_id']);

    }

} //function
?>
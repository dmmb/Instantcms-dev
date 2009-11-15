<?php
	session_start();

	define("VALID_CMS", 1);	
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/includes/config.inc.php');
	include(PATH.'/includes/database.inc.php');
	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('config');
    $inCore->loadClass('db');
    $inCore->loadClass('user');

    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

    $inUser->update();

    $comment_id     = $inCore->request('comment_id', 'int');
    $comment_type   = $inCore->request('comment_type', 'str', 'com');
    $vote           = $_REQUEST['vote'];
    $user_id        = $inUser->id;

    if ($user_id && $comment_id){

        //insert new vote
        $sql = "INSERT INTO cms_comments_votes(comment_id, comment_type, vote, user_id)
                VALUES({$comment_id}, '{$comment_type}', '{$vote}', {$user_id})";
        $inDB->query($sql);

        //calculate votes
        $sql = "SELECT c.id, IFNULL(SUM(v.vote), 0) as votes
				FROM cms_comments c
                LEFT JOIN cms_comments_votes v ON v.comment_id = c.id AND v.comment_type = '{$comment_type}'
				WHERE c.id = {$comment_id}
                GROUP BY c.id";
		$result = $inDB->query($sql);
		$comment = $inDB->fetch_assoc($result);

        if ($comment['votes']>0){
            $comment['votes'] = '<span class="cmm_good">+'.$comment['votes'].'</span>';
        } elseif ($comment['votes']<0){
            $comment['votes'] = '<span class="cmm_bad">'.$comment['votes'].'</span>';
        }

        $inCore->loadLib('karma');
        setUsersRating();

        echo $comment['votes'];
    }
	
?>
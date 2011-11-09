<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function polls(){

    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

    //Определяем адрес для редиректа назад
    $back   = $inCore->getBackURL();

    $do = $inCore->request('do', 'str', 'vote');

//========================================================================================================================//
//========================================================================================================================//
    if ($do=='vote'){

        $answer     = $inCore->request('answer', 'str', '');
        $poll_id    = $inCore->request('poll_id', 'int', 0);

        if (!$answer || !$poll_id) { $inCore->redirect($back); }

        $answer     = str_replace('"', '&quot;', $answer);

        $sql        = "SELECT * FROM cms_polls WHERE id = $poll_id";
        $result     = $inDB->query($sql);

        if ($inDB->num_rows($result)){
            $poll       = $inDB->fetch_assoc($result);
            $answers    = unserialize($poll['answers']);

            //Прибавляем голос к переданному нам варианту ответа
            foreach($answers as $key=>$value){
                if ($key == $answer){
                    $answers[$key] += 1;
                }
            }

            //Сохраняем результаты опроса
            $sql = "UPDATE cms_polls SET answers = '".serialize($answers)."' WHERE id = $poll_id";
            $inDB->query($sql);

            //MARK USER VOTING
            $user_id    = $inUser->id;
            $ip         = $inUser->ip;

            $sql = "INSERT cms_polls_log (poll_id, answer_id, user_id, ip)
                    VALUES ('$poll_id', '$answer', '$user_id', '$ip')";
            $inDB->query($sql);

            $_SESSION['poll_voted'] = $poll_id;
        }

        $inCore->redirect($back);
 
    }

//========================================================================================================================//
$inCore->executePluginRoute($do);
} //function
?>
<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

    session_start();

    define('VALID_CMS', 1);
    
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

    require(PATH."/core/cms.php");
    include(PATH."/includes/config.inc.php");

    $inCore     = cmsCore::getInstance();

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('user');

    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $steps_count = 6;

    $version_prev = '1.6';
    $version_next = '1.7';

    $step   = $inCore->request('step', 'int', 0);

    $locker = file_exists('locker');

// ========================================================================== //
// ========================================================================== //

    echo '<style type="text/css">
            body { font-family:Arial; font-size:14px; }

            a { color: #0099CC; }
            a:hover { color: #375E93; }
            h2 { color: #375E93; }

            #wrapper { padding:10px 30px; }
            #wrapper p{ line-height: 20px; }

            .migrate p { 
                           line-height:16px;
                           padding-left:20px;
                           margin:2px;
                           margin-left:20px;                           
                           background:url(/admin/images/actions/on.gif) no-repeat;
                       }
            .important {
                           margin:20px;
                           margin-left:0px;
                           border:solid 1px silver;
                           padding:15px;
                           padding-left:65px;
                           background:url(important.png) no-repeat 15px 15px;
                       }
             .nextlink {
                           margin-top:15px;
                           font-size:18px;
             }
          </style>';

    echo '<div id="wrapper">';

    echo "<h2>Миграция InstantCMS {$version_prev} &rarr; {$version_next}</h2>";

    if ($step){

        echo "<h4>Шаг {$step} из {$steps_count}</h4>";
        echo '<div class="migrate">'; include "step{$step}.php"; echo '</div>';

        if ($step < $steps_count){
            echo '<div class="nextlink"><a href="?step='.($step+1).'">Далее &rarr;</a></div>';
        } else {
            //COMPLETED
            echo '<p><strong>Создайте задание для CRON!</strong></p>';

            echo '<p>
                        Добавьте файл <strong>/cron.php</strong> в расписание заданий CRON в панели вашего хостинга.<br/>
                        Интервал выполнения — 24 часа. Это позволит системе выполнять периодические сервисные задачи.
                  </p>';

            echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">Миграция завершена. Удалите папку /migrate/ прежде чем продолжить!</div>';
            echo '<div class="nextlink"><a href="/">Перейти на сайт</a></div>';
        }

    } else {

        echo "<p>
                База данных сайта будет обновлена до новой версии.<br/>
                Чтобы не превысить максимальное время выполнения скрипта<br/>
                процесс миграции разбит на {$steps_count} шагов.
              </p>";
                
        echo '<p>От вас требуется только нажимать ссылку "Далее" после каждого шага.</p>';

        echo '<p>
                Прежде чем начать сделайте бекап базы данных и убедитесь<br/>
                что сайт недоступен для посетителей на время миграции.
              </p>';

        if (is_writable(dirname(__FILE__))){
            echo '<div class="nextlink"><a href="?step=1">Начать миграцию</a></div>';
        } else {
            echo '<p style="color:red">Папка /migrate/ не доступна для записи</p>';
        }

    }

    echo '</div>';

    
?>
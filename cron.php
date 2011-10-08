<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

	define('PATH', dirname(__FILE__));
	define("VALID_CMS", 1);

	include(PATH.'/core/cms.php');

    $inCore 	= cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $inCore->loadClass('cron');
	$inCore->loadClass('actions');

    $jobs = cmsCron::getJobs();

    //if we have a enabled jobs
    if(is_array($jobs)){

        //run each job
        foreach($jobs as $job){

			// check job interval
			if (!$job['job_interval'] || ($job['hours_ago'] > $job['job_interval']) || $job['is_new']) {
				//run job
				cmsCron::executeJob($job);
			}

        }

    }

    exit;
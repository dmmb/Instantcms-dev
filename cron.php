<?php

	define('PATH', dirname(__FILE__));
	define("VALID_CMS", 1);

	include(PATH.'/core/cms.php');

    $inCore 	= cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $inCore->loadClass('cron');

    $jobs = cmsCron::getJobs();

    //if we have a enabled jobs
    if(is_array($jobs)){

        //run each job
        foreach($jobs as $job){

            //check job interval
            if ((($job['hours_ago'] < $job['job_interval']) || !$job['job_interval']) && !$job['is_new']) { continue; }

            //run job
            cmsCron::executeJob($job);

        }

    }

    exit;
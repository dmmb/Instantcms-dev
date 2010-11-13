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


            /* ================================================ */
            /* ==============  run custom script ============== */
            /* ================================================ */
            if ($job['custom_file']){

                $inCore->includeFile(ltrim($job['custom_file'], '/'));

            }

            /* ================================================ */
            /* ==============  run component job ============== */
            /* ================================================ */
            if ($job['component'] && $job['model_method']){

                $inCore->loadModel($job['component']);

                $classname  = "cms_model_{$job['component']}";

                if (class_exists($classname)) {

                    $model      = new $classname();

                    if (method_exists($model, $job['model_method'])){

                        $job_result = $model->$job['model_method']();

                        if ($job_result){ cmsCron::jobSuccess($job['id']); }
                        
                    }

                }

            }

            /* ================================================ */
            /* ==============  run class method =============== */
            /* ================================================ */
            if ($job['class_name'] && $job['class_method']){

                $classfile = '';

                if (!strstr($job['class_name'], '|')){
                    $classname = $job['class_name'];
                } else {
                    $job['class_name'] = explode('|', $job['class_name']);
                    $classfile = $job['class_name'][0];
                    $classname = $job['class_name'][1];
                }

                if ($classfile){ $inCore->loadClass($classfile); }

                if (class_exists($classname)) {

                    if (method_exists($classname, $job['class_method'])){

                        $job_result = $classname::$job['class_method']();

                        if ($job_result){ cmsCron::jobSuccess($job['id']); }

                    }

                }

            }


        }

    }

    exit;
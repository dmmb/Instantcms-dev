<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

class cmsCron {

    private static $instance;

// ============================================================================ //
// ============================================================================ //

    private function __construct() {}

    private function __clone() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Регистрирует новую задачу СRON
     * @param str $job_name
     * @param array $action (interval, component, model_method, custom_file, comment)
     * @return bool
     */
    public static function registerJob($job_name, $job){

        $inDB = cmsDatabase::getInstance();

        if (!isset($job['enabled'])) { $job['enabled'] = 1; }
        if (!isset($job['class_name'])) { $job['class_name'] = ''; }
        if (!isset($job['class_method'])) { $job['class_method'] = ''; }

        $sql = "INSERT INTO cms_cron_jobs (job_name, job_interval, job_run_date,
                                           component, model_method, custom_file,
                                           is_enabled, is_new, comment,
                                           class_name, class_method)
                VALUES ('{$job_name}', '{$job['interval']}', CURRENT_TIMESTAMP,
                        '{$job['component']}', '{$job['model_method']}', '{$job['custom_file']}',
                        '{$job['enabled']}', '1', '{$job['comment']}',
                        '{$job['class_name']}', '{$job['class_method']}')";

        $inDB->query($sql);

        return true;

    }

    /**
     * Обновляет описание задачи СRON
     * @param int $job_id
     * @param array $action (interval, component, model_method, custom_file, comment, enabled)
     * @return bool
     */
    public static function updateJob($job_id, $job){

        $inDB = cmsDatabase::getInstance();

        $sql = "UPDATE cms_cron_jobs
                SET job_name = '{$job['name']}',
                    job_interval = '{$job['interval']}',
                    component = '{$job['component']}',
                    model_method = '{$job['model_method']}',
                    custom_file = '{$job['custom_file']}',
                    is_enabled = '{$job['enabled']}',
                    comment = '{$job['comment']}',
                    class_method = '{$job['class_method']}',
                    class_name = '{$job['class_name']}'
                WHERE id = '{$job_id}'";

        $inDB->query($sql);

        return true;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Находит описание задачи CRON по названию
     * @param str $job_name
     * @param bool $only_enabled
     * @return array | false
     */
    public static function getJob($job_name, $only_enabled=true){

        $inDB = cmsDatabase::getInstance();

        $enabled = $only_enabled ? 'AND is_enabled=1' : '';

        $job = $inDB->get_fields('cms_cron_jobs', "name='{$job_name}' {$enabled}", '*');

        return is_array($job) ? $job : false;

    }

    /**
     * Находит описание задачи CRON по id
     * @param str $job_name
     * @param bool $only_enabled
     * @return array | false
     */
    public static function getJobById($job_id){

        $inDB = cmsDatabase::getInstance();

        $job = $inDB->get_fields('cms_cron_jobs', "id='{$job_id}'", '*');

        return is_array($job) ? $job : false;

    }

// ============================================================================ //
// ============================================================================ //

    public static function getJobs($only_enabled=true, $only_custom=false){

        $inDB = cmsDatabase::getInstance();

        $enabled = $only_enabled ? 'AND is_enabled=1' : '';

        $custom = $only_custom ? "AND component='' AND model_method=''" : '';

        $sql = "SELECT id,
                       job_name as name,
                       job_interval,
                       job_run_date as run_date,
                       component,
                       model_method,
                       custom_file,
                       is_enabled,
                       is_new,
                       comment,
                       class_name,
                       class_method

                FROM cms_cron_jobs

                WHERE 1=1 {$enabled} {$custom}

                ORDER BY job_run_date ASC

                ";

        $result = $inDB->query($sql);

        if (!$inDB->num_rows($result)){ return false; }

        $jobs = array();

        while($job = $inDB->fetch_assoc($result)){

            $job['hours_ago'] = round((time() - strtotime($job['run_date']))/3600);

            $jobs[] = $job;

        }

        return $jobs;

    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Удаляет задачу CRON
     * @param string $job_name
     * @return bool
     */
    public static function removeJob($job_name){
        
        $inDB = cmsDatabase::getInstance();        
       
        $sql = "DELETE 
                FROM cms_cron_jobs
                WHERE job_name = '{$job_name}'";

        $inDB->query($sql);

        return true;
        
    }

    /**
     * Удаляет задачу CRON по id
     * @param int $job_id
     * @return bool
     */
    public static function removeJobById($job_id){

        $inDB = cmsDatabase::getInstance();

        $sql = "DELETE
                FROM cms_cron_jobs
                WHERE id = '{$job_id}'";

        $inDB->query($sql);

        return true;

    }

    public static function jobEnabled($job_id, $is_enabled){

        $is_enabled = (int)$is_enabled;

        $inDB = cmsDatabase::getInstance();

        $sql = "UPDATE cms_cron_jobs SET is_enabled = '{$is_enabled}' WHERE id = '{$job_id}'";

        $inDB->query($sql);

        return true;

    }


// ============================================================================ //
// ============================================================================ //

    public static function jobSuccess($job_id){
        
        $inDB = cmsDatabase::getInstance();        
       
        $sql = "UPDATE cms_cron_jobs SET job_run_date = CURRENT_TIMESTAMP, is_new = 0 WHERE id = '{$job_id}'";

        $inDB->query($sql);

        return true;
        
    }

    
}
?>

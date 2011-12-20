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

        $job = $inDB->get_fields('cms_cron_jobs', "job_name='{$job_name}' {$enabled}", '*');

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

    /**
     * Возвращает список задач CRON
     * @param bool $only_enabled Только активные
     * @param bool $only_custom Только задачи выполнения скрипта
     * @return array
     */
    public static function getJobs($only_enabled=true, $only_custom=false){

        $inDB = cmsDatabase::getInstance();

        $enabled = $only_enabled ? 'AND is_enabled=1' : '';

        $custom = $only_custom ? "AND component='' AND model_method='' AND class_name='' AND class_method=''" : '';

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

            $job['hours_ago'] = round((time() - strtotime($job['run_date']))/3600, 2);

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

    /**
     * Изменяет активность задачи
     * @param int $job_id ID задачи
     * @param bool $is_enabled Активность
     * @return bool
     */
    public static function jobEnabled($job_id, $is_enabled){

        $is_enabled = (int)$is_enabled;

        $inDB = cmsDatabase::getInstance();

        $sql = "UPDATE cms_cron_jobs SET is_enabled = '{$is_enabled}' WHERE id = '{$job_id}'";

        $inDB->query($sql);

        return true;

    }


// ============================================================================ //
// ============================================================================ //

    /**
     * Отмечает задачу как успешно выполненную
     * @param int $job_id ID задачи
     * @return bool
     */
    public static function jobSuccess($job_id){
        
        $inDB = cmsDatabase::getInstance();        
       
        $sql = "UPDATE cms_cron_jobs SET job_run_date = CURRENT_TIMESTAMP, is_new = 0 WHERE id = '{$job_id}'";

        $inDB->query($sql);

        return true;
        
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Выполняет задачу с указанным ID
     * @param int $job_id
     */
    public static function executeJobById($job_id){

        $job = self::getJobById($job_id);
        self::executeJob($job);

    }

    /**
     * Выполняет переданную задачу
     * @param array $job
     */
    public static function executeJob($job){

        $inCore = cmsCore::getInstance();

        $job_result = true;

        /* ================================================ */
        /* ==============  внешний php-файл  ============== */
        /* ================================================ */
        if ($job['custom_file']){

            $inCore->includeFile(ltrim($job['custom_file'], '/'));

        }

        /* ================================================ */
        /* ================  метод модели ================= */
        /* ================================================ */
        if ($job['component'] && $job['model_method']){

            $inCore->loadModel($job['component']);

            $classname  = "cms_model_{$job['component']}";

            if (class_exists($classname)) {

                $model = new $classname();

                if (method_exists($model, $job['model_method'])){

                    $job_result = call_user_func(array($model, $job['model_method']));
                    
                }

            }

        }

        /* ================================================ */
        /* =================  метод класса ================ */
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

                    $job_result = $job_result && call_user_func(array($classname, $job['class_method']));

                }

            }

        }

        if ($job_result){ self::jobSuccess($job['id']); }

    }


// ============================================================================ //
// ============================================================================ //
    
}
?>

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

class p_demo_route extends cmsPlugin {

// ==================================================================== //

    public function __construct(){
        
        parent::__construct();

        // ���������� � �������

        $this->info['plugin']           = 'p_demo_route';
        $this->info['title']            = 'Demo Plugin';
        $this->info['description']      = '������ ������� - ��� ������� /users/get_demo.html';
        $this->info['author']           = 'InstantCMS Team';
        $this->info['version']          = '1.0';

        // �������, ������� ����� ������������� ��������

        $this->events[]                 = 'GET_ROUTE_USERS';

    }

// ==================================================================== //

    /**
     * ��������� ��������� �������
     * @return bool
     */
    public function install(){

        return parent::install();

    }

// ==================================================================== //

    /**
     * ��������� ���������� �������
     * @return bool
     */
    public function upgrade(){

        return parent::upgrade();

    }

// ==================================================================== //

    /**
     * ��������� �������
     * @param string $event
     * @param mixed $item
     * @return mixed
     */
    public function execute($event, $routes){

        parent::execute();

        switch ($event){
            case 'GET_ROUTE_USERS': $routes = $this->eventGetRoutes($routes); break;
        }

        return $routes;

    }

// ==================================================================== //

    private function eventGetRoutes($routes) {
		
		// ��������� ������ �� �������� � router.php
		$add_routes[] = array(
					'_uri'  => '/^users\/get_demo.html$/i',
					'do'    => 'get_demo'
				 );

		// ���������� ������ $add_routes, ������ ������ � ������ �������� ������� $routes
		foreach($add_routes as $route){
			array_unshift($routes, $route);
		}

        return $routes;

    }

// ==================================================================== //

}

?>

<?php

namespace Drupal\acdh_repo_gui\Model;

/**
 * Description of RootModel
 *
 * @author nczirjak
 */
class RootViewModel {
    
    private $repodb;
    
    public function __construct() {
        //set up the DB connections
        \Drupal\Core\Database\Database::setActiveConnection('repo');
        $this->repodb = \Drupal\Core\Database\Database::getConnection('repo');
    }
    
    private function changeBackDBConnection()
    {
        \Drupal\Core\Database\Database::setActiveConnection();
    }
    
    /**
     * get the root views data
     * 
     * @return array
     */
    public function getRootViewData(): array {
        $result = array();
        $query = $this->repodb->query("SELECT * FROM rootids_view;");
        $result = $query->fetchAll();
        $this->changeBackDBConnection();
        return $result;
    }
}

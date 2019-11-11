<?php

namespace Drupal\acdh_repo_gui\Model;

/**
 * Description of DetailViewModel
 *
 * @author nczirjak
 */
class DetailViewModel {
    
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
    
    
    public function getBasicDetailViewData(string $identifier): array {
        $result = array();
        //run the actual query
        $query = $this->repodb->query(" select * from detail_view_func(:id) ", array(':id' => $identifier));
        $result = $query->fetchAll();
        $this->changeBackDBConnection();
        return $result;
    }
        
}
<?php

namespace Drupal\acdh_repo_gui\Model;

use Drupal\acdh_repo_gui\Model\ArcheModel;
/**
 * Description of DetailViewModel
 *
 * @author nczirjak
 */
class DetailViewModel extends ArcheModel {
    
    private $repodb;
    
    public function __construct() {
        //set up the DB connections
        \Drupal\Core\Database\Database::setActiveConnection('repo');
        $this->repodb = \Drupal\Core\Database\Database::getConnection('repo');
    }
    
    public function getViewData(string $identifier = ""): array {
        if(empty($identifier)) { return array();}
        $result = array();
        //run the actual query
        $query = $this->repodb->query(" select * from detail_view_func(:id) ", array(':id' => $identifier));
        $result = $query->fetchAll();
        $this->changeBackDBConnection();
        return $result;
    }
        
}
<?php

namespace Drupal\acdh_repo_gui\Model;

use Drupal\acdh_repo_gui\Model\ArcheModel;

/**
 * Description of DisseminationServicesModel
 *
 * @author nczirjak
 */
class DisseminationServicesModel extends ArcheModel {
    
    private $repodb;
    
    
    public function __construct() {
        //set up the DB connections
        \Drupal\Core\Database\Database::setActiveConnection('repo');
        $this->repodb = \Drupal\Core\Database\Database::getConnection('repo');
    }
    
    /**
     * Get the data for the left side boxes
     * 
     * @param string $identifier
     * @return array
     */
    public function getViewData(string $identifier = "entity"): array {
        return array();
    }
    
    
        
}
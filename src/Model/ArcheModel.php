<?php

namespace Drupal\acdh_repo_gui\Model;

use acdhOeaw\acdhRepoLib\Repo;
use Drupal\acdh_repo_gui\Helper\ConfigConstants as CC;

/**
 * Description of ArcheModel
 *
 * @author nczirjak
 */
abstract class ArcheModel {
    
    private $repodb;
    
    public function __construct() {
        //set up the DB connections
        \Drupal\Core\Database\Database::setActiveConnection('repo');
        $this->repodb = \Drupal\Core\Database\Database::getConnection('repo');
    }
    
    public function changeBackDBConnection()
    {
        \Drupal\Core\Database\Database::setActiveConnection();
    }
    
    /**
     * get the root views data
     * 
     * @return array
     */
    abstract public function getViewData(string $identifier = ""): array;    
}

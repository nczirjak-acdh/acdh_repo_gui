<?php

namespace Drupal\acdh_repo_gui\Model;

use Drupal\acdh_repo_gui\Model\ArcheModel;
/**
 * Description of RootModel
 *
 * @author nczirjak
 */
class RootViewModel extends ArcheModel {
    
    private $repodb;
    private $sqlResult;
    
    public function __construct() {
        //set up the DB connections
        \Drupal\Core\Database\Database::setActiveConnection('repo');
        $this->repodb = \Drupal\Core\Database\Database::getConnection('repo');
    }
        
    /**
     * get the root views data
     * 
     * @return array
     */
    public function getViewData(string $identifier = ""): array {
        $result = array();
        $query = $this->repodb->query("SELECT * FROM public.root_view_func() order by id;");
        $this->sqlResult = $query->fetchAll();
        $this->changeBackDBConnection();
        return $this->sqlResult;
    }
}

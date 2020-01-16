<?php

namespace Drupal\acdh_repo_gui\Helper;

use Drupal\acdh_repo_gui\Helper\GeneralFunctions;
use Drupal\acdh_repo_gui\Object\ResourceObject;
use Drupal\acdh_repo_gui\Helper\ConfigConstants as CC;
use acdhOeaw\acdhRepoLib\Repo;
use acdhOeaw\acdhRepoAcdh\RepoResource as RR;
use Drupal\acdh_repo_gui\Helper\ArcheHelper;

/**
 * Description of RootViewHelper
 *
 * @author nczirjak
 */
class RootViewHelper extends ArcheHelper {
    
    
    private $rootViewObjectArray;
    private $lng = "en";
    
            
    public function createView(array $data): array {
        $this->data = $data;
        $this->extendActualObj(true);  
          
        if(count((array)$this->data) == 0) {
            return array();
        }
        
        foreach ($this->data as $k => $v) {
            $this->rootViewObjectArray[] = new ResourceObject($v, $this->config);
        }
        
        return $this->rootViewObjectArray;
    }
    
    
   
}

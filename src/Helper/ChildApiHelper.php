<?php

namespace Drupal\acdh_repo_gui\Helper;

use Drupal\acdh_repo_gui\Helper\GeneralFunctions;
use Drupal\acdh_repo_gui\Object\ResourceObject;
use Drupal\acdh_repo_gui\Helper\ConfigConstants as CC;
use acdhOeaw\acdhRepoLib\Repo;
use acdhOeaw\acdhRepoDisserv\RepoResource as RR;
use Drupal\acdh_repo_gui\Helper\ArcheHelper;

/**
 * Description of ApiViewHelper
 *
 * @author nczirjak
 */
class ChildApiHelper extends ArcheHelper {
    
    
    private $rootViewObjectArray;
    private $lng = "en";
    
            
    public function createView(array $data): array {
        $this->reorderData($data);
        
        $this->extendActualObj(true);  
        
        echo "<pre>";
        var_dump($this->data);
        echo "</pre>";
        if(count((array)$this->data) == 0) {
            return array();
        }
        
        foreach ($this->data as $k => $v) {
            $this->rootViewObjectArray[] = new ResourceObject($v, $this->config);
        }
        echo "<pre>";
        var_dump($this->rootViewObjectArray);
        echo "</pre>";
        die();
        return $this->rootViewObjectArray;
    }
    
    private function reorderData(array $data) {
        foreach($data as $v) {
            if(isset($v->id)) {
                $this->data[$v->id][] = $v;
            }
        }
    }
    
    protected function extendActualObj(bool $root = false) {
        $result = array();
        
        foreach($this->data as $k => $v) {
            echo $k;
            echo "<pre>";
            var_dump($v);
            echo "</pre>";
            
        }
        
        
        $this->data = $result;
    }
    
   
}

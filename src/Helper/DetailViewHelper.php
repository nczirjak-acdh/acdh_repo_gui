<?php

namespace Drupal\acdh_repo_gui\Helper;

use Drupal\acdh_repo_gui\Helper\GeneralFunctions;
use Drupal\acdh_repo_gui\Helper\ConfigConstants as CC;
use Drupal\acdh_repo_gui\Object\ResourceObject;
use acdhOeaw\acdhRepoLib\Repo;
use acdhOeaw\acdhRepoDisserv\RepoResource;

use Drupal\acdh_repo_gui\Helper\ArcheHelper;
/**
 * Description of DetailViewHelper
 *
 * @author nczirjak
 */
class DetailViewHelper extends ArcheHelper {
    
    private $detailViewObjectArray;
    private $lng = "en";
    
    /**
     * 
     * Build up the necessary data for the detail view 
     * 
     * @param array $data
     * @return array
     */
    public function createView(array $data = array(), string $dissemination = ''): array {
        $this->data = $data;
        $this->extendActualObj();
        
        if(count((array)$this->data) == 0) {
            return array();
        }
        $this->detailViewObjectArray[] = new ResourceObject($this->data, $this->config);
        
        return $this->detailViewObjectArray;
    }
  
    /**
     * Get the dissemination services
     * 
     * @param string $id
     * @return array
     */
    public function getDissServices(string $id): array {
        $result = array();
        //internal id 
        $rep = new \acdhOeaw\acdhRepoDisserv\RepoResource($this->config->getBaseUrl().$id, $this->config);
       
        try {
            $dissServ = array();
            $dissServ = $rep->getDissServices();
            
            foreach($dissServ as $k => $v) {
                $result[$k] = (string) $v->getRequest($rep)->getUri();
            }
            return $result;
        } catch (Exception $ex) {

            error_log("DetailViewhelper-getDissServices: ".$ex->getMessage());
            return array();
        } catch (\GuzzleHttp\Exception\ServerException $ex) {
            error_log("DetailViewhelper-getDissServices: ".$ex->getMessage());
            return array();
        }
    }
    
}

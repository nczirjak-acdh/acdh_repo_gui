<?php

namespace Drupal\acdh_repo_gui\Helper;

use Drupal\acdh_repo_gui\Helper\GeneralFunctions;
use Drupal\acdh_repo_gui\Helper\ConfigConstants as CC;
use Drupal\acdh_repo_gui\Object\ResourceObject;
use acdhOeaw\acdhRepoLib\Repo;
use acdhOeaw\acdhRepoLib\RepoDb;
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
        $this->detailViewObjectArray[] = new ResourceObject($this->data, $this->repo);
        
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
        $repodb = \acdhOeaw\acdhRepoLib\RepoDb::factory($_SERVER["DOCUMENT_ROOT"].'/modules/custom/acdh_repo_gui/config.yaml');
        $repDiss = new \acdhOeaw\arche\disserv\RepoResourceDb($this->repo->getBaseUrl().$id, $repodb);
        try {
            $dissServ = array();
            $dissServ = $repDiss->getDissServices();
            //echo (string)$rep->getDissServices()['thumbnail']->getRequest($rep)->getUri();
            foreach($dissServ as $k => $v) {
                $result[$k] = (string) $v->getRequest($repDiss)->getUri();
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

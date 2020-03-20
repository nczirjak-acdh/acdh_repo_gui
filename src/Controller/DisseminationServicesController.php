<?php

namespace Drupal\acdh_repo_gui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\acdh_repo_gui\Model\DisseminationServicesModel;
use Drupal\acdh_repo_gui\Helper\DisseminationServicesHelper;

/**
 * Description of DisseminationServicesController
 *
 * @author norbertczirjak
 */
class DisseminationServicesController extends ControllerBase {
    
    private $config;
    private $model;
    private $helper;
    private $basicViewData;
    private $extraViewData;
    
    private $disseminations = array(
        'collection', '3d', 'iiif', 'turtle'
    );
    
    public function __construct($config) {
        $this->config = $config;
        $this->model = new DisseminationServicesModel();
        $this->helper = new DisseminationServicesHelper($config);
    }
    
    public function generateView(string $identifier, string $dissemination): array {
        if(empty($identifier) || !in_array($dissemination, $this->disseminations)){
            return array();
        }
        $vd = array();
        $vd = $this->model->getViewData($identifier, $dissemination);
        if(count((array)$vd) == 0) {
            return array();
        } 
        $this->basicViewData = $this->helper->createView($vd, $dissemination, $identifier);
        
        return array($this->basicViewData);
    }
    
    
    
}

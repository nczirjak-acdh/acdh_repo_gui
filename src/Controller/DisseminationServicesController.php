<?php

namespace Drupal\acdh_repo_gui\Controller;

use Drupal\Core\Controller\ControllerBase;
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
    
     public function __construct($config) {
        $this->config = $config;
        $this->model = new DisseminationServicesModel();
        $this->helper = new DisseminationServicesHelper($config);
    }
    
}

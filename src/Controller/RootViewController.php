<?php

namespace Drupal\acdh_repo_gui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\acdh_repo_gui\Model\RootViewModel;
use Drupal\acdh_repo_gui\Helper\RootViewHelper;


/**
 * Description of RootViewController
 *
 * @author nczirjak
 */
class RootViewController  extends ControllerBase {
    private $config;
    private $model;
    private $helper;
    
    public function __construct($config) {
        $this->config = $config;
        $this->model = new RootViewModel();        
        $this->helper = new RootViewHelper();
    }
    
    public function generateRootView(): array {
        $result = array();
        $data = array();
        
        $data = $this->model->getViewData();
        if(count((array)$data) <= 0) {
            echo "no data";
        }
        
        return $this->helper->createView($data);
    }
}

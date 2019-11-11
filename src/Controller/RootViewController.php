<?php

namespace Drupal\acdh_repo_gui\Controller;

use Drupal\acdh_repo_gui\Model\RootViewModel;
use Drupal\acdh_repo_gui\Helper\RootViewHelper;


/**
 * Description of RootViewController
 *
 * @author nczirjak
 */
class RootViewController {
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
        
        $data = $this->model->getRootViewData();
        if(count((array)$data) <= 0) {
            echo "no data";
        }
        
        $result = $this->helper->setUpRootViewObject($data);

        return $result;
    }
}

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
    private $siteLang;
    
    public function __construct($config) {
        $this->config = $config;
        $this->model = new RootViewModel();        
        $this->helper = new RootViewHelper();
        (isset($_SESSION['language'])) ? $this->siteLang = strtolower($_SESSION['language'])  : $this->siteLang = "en";
    }
    
    public function countRoots(): int {
        //count the actual root values
        $counts = 0;
        $counts = $this->model->countRoots($this->siteLang);
        //if we dont have root elements then we will send back an empty array
        return (int)$counts;
    }
    
    public function generateRootView(string $limit = "10", string $page = "0", string $order = "datedesc"): array {
        
        $data = $this->model->getViewData($limit, $page, $order, $this->siteLang);
        if(count((array)$data) <= 0) {
            echo "no data";
        }
        return $this->helper->createView($data);
    }
}

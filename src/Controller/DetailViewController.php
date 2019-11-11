<?php

namespace Drupal\acdh_repo_gui\Controller;

use Drupal\acdh_repo_gui\Model\DetailViewModel;
use Drupal\acdh_repo_gui\Helper\DetailViewHelper;

/**
 * Description of DetailViewController
 *
 * @author nczirjak
 */
class DetailViewController {
    
    private $config;
    private $model;
    private $helper;
    private $basicViewData;
    
    public function __construct($config) {
        $this->config = $config;
        $this->model = new DetailViewModel();
        $this->helper = new DetailViewHelper($config);
    }
    
    
    public function generateDetailView(string $identifier) {
        $dv = array();
        $dv = $this->model->getBasicDetailViewData($identifier);
        
        if(count((array)$dv) == 0) {
            return array();
        } 
        
        
        //extend the data object with the shortcuts
        $this->basicViewData = new \stdClass();
        $this->basicViewData->basic = $this->helper->createDetailView($dv);
        
        return $this->basicViewData;
    }
    
}

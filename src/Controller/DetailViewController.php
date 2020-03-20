<?php

namespace Drupal\acdh_repo_gui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\acdh_repo_gui\Model\DetailViewModel;
use Drupal\acdh_repo_gui\Helper\DetailViewHelper;
use Drupal\acdh_repo_gui\Helper\CiteHelper as CH;


/**
 * Description of DetailViewController
 *
 * @author nczirjak
 */
class DetailViewController extends ControllerBase {
    
    private $config;
    private $model;
    private $helper;
    private $basicViewData;
    
    public function __construct($config) {
        $this->config = $config;
        $this->model = new DetailViewModel();
        $this->helper = new DetailViewHelper($config);
    }
    
    /**
     * Generate the detail view
     * 
     * @param string $identifier
     * @return type
     */
    public function generateDetailView(string $identifier): object {
        $dv = array();
        $dv = $this->model->getViewData($identifier);
        echo $identifier;
        if(count((array)$dv) == 0) {
            return new \stdClass();
        } 
       
        //extend the data object with the shortcuts
        $this->basicViewData = new \stdClass();
        $this->basicViewData->basic = $this->helper->createView($dv);
        $this->basicViewData->basic = $this->basicViewData->basic[0];
        
        // check the dissemination services
        if(isset($dv[0]->id) && !is_null($dv[0]->id)) {
            $this->basicViewData->dissemination = $this->helper->getDissServices($dv[0]->id);
        }
        
        //get the cite widget data
        $cite = new CH($this->config);
        $this->basicViewData->extra = new \stdClass();
        $this->basicViewData->extra->citeWidgetData = $cite->createCiteThisWidget($this->basicViewData->basic);

        return $this->basicViewData;
    }
    
    /**
     * 
     * generate the basic metadata for the root resource/collection in the dissemination services view
     * @param string $identifier
     * @return object
     */
    public function generateObjDataForDissService(string $identifier): object {
        $dv = array();
        $dv = $this->model->getViewData($identifier);
        
        if(count((array)$dv) == 0) {
            return new \stdClass();
        } 
       
        //extend the data object with the shortcuts
        $obj = new \stdClass();
        $obj = $this->helper->createView($dv);
        if(isset($obj[0])) {
            return $obj[0];
        }
        return new \stdClass();
    }
    
}

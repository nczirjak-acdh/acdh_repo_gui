<?php

namespace Drupal\acdh_repo_gui\Controller;

use Drupal\Core\Controller\ControllerBase;
use acdhOeaw\acdhRepoLib\Repo;
use Drupal\acdh_repo_gui\Controller\RootViewController as RVC;
use Drupal\acdh_repo_gui\Controller\DetailViewController as DVC;
use Drupal\acdh_repo_gui\Helper\GeneralFunctions;


/**
 * Description of AcdhRepoController
 *
 * @author nczirjak
 */
class AcdhRepoGuiController extends ControllerBase 
{    
    private $config;
    private $rootViewController;
    private $detailViewController;
    private $siteLang;
    private $langConf;
    
    public function __construct() {
        $this->config = Repo::factory($_SERVER["DOCUMENT_ROOT"].'/modules/acdh_repo_gui/config.yaml');
        (isset($_SESSION['language'])) ? $this->siteLang = strtolower($_SESSION['language'])  : $this->siteLang = "en";
        $this->rootViewController = new RVC($this->config);
        $this->detailViewController = new DVC($this->config);
        $this->generalFunctions = new GeneralFunctions();
        $this->langConf = $this->config('acdh_repo_gui.settings');
    }
    
    public function repo_main()
    {
        $roots = array();
        $roots = $this->rootViewController->generateRootView();
        
        if(count($roots) <= 0) {
            drupal_set_message(
                $this->langConf->get('errmsg_no_root_resources') ? $this->langConf->get('errmsg_no_root_resources') : 'You do not have Root resources',
                'error',
                false
            );
            return array();
        }
        
        return [
            '#theme' => 'acdh-repo-gui-main',
            '#data' => $roots,
        ]; 
        
    }
    
    public function repo_detail(string $identifier)
    {   
        $dv = array();
        $identifier = $this->generalFunctions->detailViewUrlDecodeEncode($identifier, 0);
        
        $dv = $this->detailViewController->generateDetailView($identifier);
        
        if(count((array)$dv) == 0) {
             drupal_set_message(
                $this->langConf->get('errmsg_no_data') ? $this->langConf->get('errmsg_no_data') : 'You do not have data',
                'error',
                false
            );
            return array();
        }
        
        return [
            '#theme' => 'acdh-repo-gui-detail',
            '#basic' => $dv->basic
        ]; 
        
    }
}

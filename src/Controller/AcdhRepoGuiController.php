<?php

namespace Drupal\acdh_repo_gui\Controller;

use Drupal\Core\Controller\ControllerBase;
use acdhOeaw\acdhRepoLib\Repo;
use acdhOeaw\acdhRepoLib\RepoResource;
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
        
        $_SERVER["DOCUMENT_ROOT"].'/modules/custom/acdh_repo_gui/';
        $this->config = Repo::factory($_SERVER["DOCUMENT_ROOT"].'/modules/custom/acdh_repo_gui/config.yaml');
        (isset($_SESSION['language'])) ? $this->siteLang = strtolower($_SESSION['language'])  : $this->siteLang = "en";
        
        $this->rootViewController = new RVC($this->config);
        $this->detailViewController = new DVC($this->config);
        $this->generalFunctions = new GeneralFunctions();
        $this->langConf = $this->config('acdh_repo_gui.settings');
    }
    
    /**
     * 
     * Root view
     * 
     * @return type
     */
    public function repo_root(string $limit = "10", string $page = "1", string $order = "datedesc"): array
    {
        $limit = (int)$limit;
        $page = (int)$page;
        // on the gui we are displaying 1 as the first page.
        $page = $page-1;
        
        $roots = array();
        $roots = $this->rootViewController->generateRootView($limit, $page, $order);
        
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
            '#attached' => [
                'library' => [
                    'acdh_repo_gui/repo-styles',
                ]
            ]
        ]; 
        
    }
    
    /**
     * Repo search/root main view
     * 
     * @param string $metavalue
     * @param string $limit
     * @param string $page
     * @param string $order
     * @return array
     */
    public function repo_complexsearch(string $metavalue = "root", string $limit = "10", string $page = "1", string $order = "titleasc"): array
    {         
        //this is the root collection view
        if (empty($metavalue) ||  $metavalue == "root") {
            //If a cookie setting exists and the query is coming without a specific parameter
            if ((isset($_COOKIE["resultsPerPage"]) && !empty($_COOKIE["resultsPerPage"])) && empty($limit)) {
                $limit = $_COOKIE["resultsPerPage"];
            }
            if ((isset($_COOKIE["resultsOrder"]) && !empty($_COOKIE["resultsOrder"])) && empty($order)) {
                $order = $_COOKIE["resultsOrder"];
            }
            if (empty($page)) {
                $page = "1";
            }
            return $this->repo_root($limit, $page, $order);
        } 
        
        //the search view
        echo "the search view";    
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
            '#attached' => [
                'library' => [
                    'acdh_repo_gui/repo-styles',
                ]
            ]
        ]; 
    }
    
    /**
     * the detail view
     * 
     * @param string $identifier
     * @return type
     */
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
            '#basic' => $dv->basic,
            '#extra' => $dv->extra,
            '#dissemination' => (isset($dv->dissemination)) ? $dv->dissemination : array(),
            '#attached' => [
                'library' => [
                    'acdh_repo_gui/repo-styles',
                ]
            ]
        ]; 
        
    }
    
    public function search_view(string $str) {
        
        $this->config->getResourcesBySqlQuery($str, $parameters);
        
        return [
            '#theme' => 'acdh-repo-gui-search',
            '#result' => "sss",
            '#attached' => [
                'library' => [
                    'acdh_repo_gui/repo-styles',
                ]
            ]
        ]; 
    }
}

<?php

namespace Drupal\acdh_repo_gui\Helper;

use Drupal\acdh_repo_gui\Helper\GeneralFunctions;
use Drupal\acdh_repo_gui\Helper\ConfigConstants as CC;
use Drupal\acdh_repo_gui\Object\DetailViewObject;
use acdhOeaw\acdhRepoLib\Repo;

/**
 * Description of DetailViewHelper
 *
 * @author nczirjak
 */
class DetailViewHelper {
    
    private $generalFunctions;
    private $detailViewObject;
    private $lng = "en";
    private $config;
    private $data;
    
    public function __construct() {
        $this->generalFunctions = new GeneralFunctions();
        $this->config = Repo::factory($_SERVER["DOCUMENT_ROOT"].'/modules/acdh_repo_gui/config.yaml');
    }
    
    private $basicProperties = array(
        "https://vocabs.acdh.oeaw.ac.at/schema#hasTitleImage",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasTitle",
        "http://www.w3.org/1999/02/22-rdf-syntax-ns#type",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasPrincipalInvestigator",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasContact",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasEditor",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasAuthor",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasCreator",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasContributor",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasCreatedDate",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasCreationStartDate",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasCreationEndDate",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasAvailableDate",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasExtent",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasNumberOfItems",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasBinarySize",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasCategory",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasLicensor",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasLicense",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasAccessRestriction",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasSchema",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasMetadata",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasUrl",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasPid",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasRelatedProject",
        "https://vocabs.acdh.oeaw.ac.at/schema#isPartOf",
        "https://vocabs.acdh.oeaw.ac.at/schema#isderivedFrom",
        "http://www.w3.org/2000/01/rdf-schema#seeAlso"
    );
    
    private $summaryProperties = array(
       "https://vocabs.acdh.oeaw.ac.at/schema#hasSpatialCoverage",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasCoverageStartDate",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasCoverageEndDate",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasDescription",
        "https://vocabs.acdh.oeaw.ac.at/schema#hasNote"
    );
    
    /**
     * Create shortcut from the property for the gui
     * 
     * @param string $prop
     * @return string
     */
    private function createShortcut(string $prop): string {
        $prefix = array();
        $prefix = explode('#', $prop);
        $property = end($prefix);
        $prefix = $prefix[0];
        if (isset(CC::$prefixesToChange[$prefix.'#'])) {
           return CC::$prefixesToChange[$prefix.'#'].':'.$property;
        }
    }
    
    /**
     * Create gui inside uri from the identifier
     * 
     * @param string $data
     * @return string
     */
    private function makeInsideUri(string $data): string {
        if(!empty($data)) {
            return $this->generalFunctions->detailViewUrlDecodeEncode($data, 1);
        }
        return "";
    }
    
    /**
     * 
     * Build up the necessary data for the detail view 
     * 
     * @param array $data
     * @return array
     */
    public function createDetailView(array $data) {
        $this->data = $data;
        
        $this->extendActualObj();
        
        if(count((array)$this->data) == 0) {
            return array();
        }
        

        $this->setUpDetailViewObject();
        
        return $this->detailViewObject;
    }
    
    private function extendActualObj() {
        $result = array();
        foreach($this->data as $d) {
            if(is_null($d->property) === false) {
                //create the shortcur
                $d->title = "";
                $d->title = $d->value;
                if(isset($d->relvalue) && !empty($d->relvalue)) {
                    $d->title = $d->relvalue;
                }
                if(isset($d->acdhid) && !empty($d->acdhid)) {
                    $d->insideUri = "";
                    $d->insideUri = $this->makeInsideUri($d->acdhid);
                }
                $d->shortcut = $this->createShortcut($d->property);
                $result[$d->shortcut][] = $d;
            }
        }
        $this->data = $result;
    }
    
    public function setUpDetailViewObject() {
        $this->detailViewObject = new DetailViewObject($this->data);
    }
}

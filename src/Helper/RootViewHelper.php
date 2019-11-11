<?php

namespace Drupal\acdh_repo_gui\Helper;

use Drupal\acdh_repo_gui\Helper\GeneralFunctions;
use Drupal\acdh_repo_gui\Object\RootViewObject;
use acdhOeaw\acdhRepoLib\Repo;

/**
 * Description of RootViewHelper
 *
 * @author nczirjak
 */
class RootViewHelper {
    
    private $rootViewObject;
    private $lng = "en";
    private $generalFunctions;
    private $config;
    
    public function __construct() {
        $this->config = Repo::factory($_SERVER["DOCUMENT_ROOT"].'/modules/acdh_repo_gui/config.yaml');
        $this->generalFunctions = new GeneralFunctions();
    }
    
    public function setUpRootViewObject(array $data): array {
        $result = array();
       
        foreach ($data as $v) {
            $this->rootViewObject = new RootViewObject();
            $this->rootViewObject->setData("title", $v->title);  
            $this->rootViewObject->setData("repoUrl", $v->repourl);
            $this->rootViewObject->setData("insideUri", $this->generalFunctions->detailViewUrlDecodeEncode($v->identifier, 1));
            $this->rootViewObject->setData("identifier", $v->identifier);
            $this->rootViewObject->setData("identifiers", $v->identifiers);
            $this->rootViewObject->setData("availableDate", $v->availabledate);
            $this->rootViewObject->setData("accessRestriction", $v->accessrestriction);
            $this->rootViewObject->setData("author", $v->author);
            $this->rootViewObject->setData("contributor", $v->contributor);
            $this->rootViewObject->setData("typeUri", $v->acdhtype);
            $this->rootViewObject->setData("type", str_replace($this->config->getSchema()->__get('drupal')->vocabsNamespace, '', $v->acdhtype));
            $this->rootViewObject->setData("description", $v->description);
           
            try {
                $obj = $this->rootViewObject;
                $result[] = $obj;
            } catch (\ErrorException $ex) {
                throw new \ErrorException(t('Error message').':  FrontendController -> OeawResource Exception ');
            }
        }
        return $result;
    
    }
}

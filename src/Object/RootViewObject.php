<?php

namespace Drupal\acdh_repo_gui\Object;

/**
 * Description of RootViewObject
 *
 * @author nczirjak
 */
class RootViewObject {
    
    private $title;  
    private $repoUrl;
    private $identifier;
    private $identifiers;
    private $description;
    private $insideUri;
    private $availableDate;
    private $accessRestriction;
    private $author;
    private $contributor;
    private $type;
    private $typeUri;
   
    public function getData(string $property) {
        return (isset($this->$property) && !empty($this->$property)) ? $this->$property : "";
    }
    
    public function setData(string $prop = null, string $v = null) {
        if($prop && $v) {
            $this->$prop = $v;
        }
    }
}

<?php

namespace Drupal\acdh_repo_gui\Object;

/**
 * Description of RootViewObject
 *
 * @author nczirjak
 */
class DetailViewObject {
    private $properties;
   
    public function __construct(array $data) {
        $this->properties = array();
        foreach($data as $k => $v) {
            $this->setData($k, $v);  
        }
    }
    public function getData(string $property): array {
        return (isset($this->properties[$property]) && !empty($this->properties[$property])) ? $this->properties[$property] : array();
    }
    
    private function setData(string $prop = null, array $v = null) {
        if(
            isset($prop) && count((array)$v) > 0  
        ) {
            $this->properties[$prop] = $v;
        }
    }
    
    public function getTitle(): string {
        return (isset($this->properties["acdh:hasTitle"][0]->title) && !empty($this->properties["acdh:hasTitle"][0]->title)) ? $this->properties["acdh:hasTitle"][0]->title : "";
    }
    
    public function getAcdhType(): string {
        if(isset($this->properties["rdf:type"])){
            foreach($this->properties["rdf:type"] as $v){
                if(isset($v->title) && !empty($v->title) && (strpos($v->title, 'https://vocabs.acdh.oeaw.ac.at/schema#') !== false) ) {
                    return str_replace('https://vocabs.acdh.oeaw.ac.at/schema#', '', $v->title);
                }
            }
        }
        return "";
    }
    public function getExpertTableData(): array {
        return $this->properties;
    }
}

<?php

namespace Drupal\acdh_repo_gui\Object;

class ResourceObject {
    private $config;
    private $properties;
   
    public function __construct(array $data, $config) {
        $this->properties = array();
        $this->config = $config;
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
    
    public function getIdentifiers(): array {
        return (isset($this->properties["acdh:hasIdentifier"]) && !empty($this->properties["acdh:hasIdentifier"])) ? $this->properties["acdh:hasIdentifier"] : array();
    }
    
    public function getPid(): string {
        return (isset($this->properties["acdh:hasPid"][0]->title) && !empty($this->properties["acdh:hasPid"][0]->title)) ? $this->properties["acdh:hasPid"][0]->title : "";
    }
    
     /**
     * Get resource inside uri
     * 
     * @return string
     */
    public function getInsideUrl(): string {
        if(isset($this->properties["acdh:hasIdentifier"])){
            foreach($this->properties["acdh:hasIdentifier"] as $v){
                if(isset($v->acdhid) && !empty($v->acdhid) ) {
                    return str_replace('https://', '', $v->acdhid);
                }
            }
        }
        return "";
    }
    
    /**
     * Get the resource acdh uuid
     * 
     * @return string
     */
    public function getUUID(): string {
        if(isset($this->properties["acdh:hasIdentifier"])){
            foreach($this->properties["acdh:hasIdentifier"] as $v){
                if(isset($v->acdhid) && !empty($v->acdhid) ) {
                    return $v->acdhid;
                }
            }
        }
        return "";
    }
    
    
    public function getTitleImage(): string {
        $result = "";
        if(isset($this->properties["acdh:hasTitleImage"]) && count($this->properties["acdh:hasTitleImage"]) > 0) {
            if(isset($this->properties["acdh:hasTitleImage"][0]->acdhid)) {
                if (strpos($this->properties["acdh:hasTitleImage"][0]->acdhid, '/uuid/') !== false) {
                    $baseurl = str_replace('/browser', '/services/thumbnails/', 'https://fedora.hephaistos.arz.oeaw.ac.at/browser');
                    $thumbOptions = '?width=150';            
                    $thumbID = str_replace($this->config->getSchema()->drupal->uuidNamespace, 'uuid/', $this->properties["acdh:hasTitleImage"][0]->acdhid);
                    $result = $baseurl.$thumbID.$thumbOptions;
                }
            }
        }else if( !empty($this->getAcdhType()) && strtolower($this->getAcdhType() == "image") ) {
            (!empty($this->getUUID())) ? $result = $this->getUUID() : "";
        }
        return $result;
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

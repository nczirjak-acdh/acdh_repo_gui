<?php


namespace Drupal\acdh_repo_gui\Helper;

use acdhOeaw\acdhRepoLib\Repo;
use acdhOeaw\acdhRepoDisserv\RepoResource;
use Drupal\acdh_repo_gui\Helper\ArcheHelper;
/**
 * Description of DisseminationServicesHelper
 *
 * @author norbertczirjak
 */
class DisseminationServicesHelper extends ArcheHelper {
    
    private $data;
    
    public function createView(array $data = array(), string $dissemination = '', string $identifier = ''): array {
        $this->data = $data;
        
        $this->modifyDataStructure();
      
        $result = array();
        $result = $this->createTreeData($this->data, $identifier);
        
        if(count($result) > 0) {
            return $result;
        }
        return array();
    }
    
    private function modifyDataStructure() {
        foreach($this->data as $k => $v) {
            $v['uri'] = $v['mainid'];
            $v['uri_dl'] = $this->config->getBaseUrl().$v['mainid'];
            $v['text'] = $v['title'];
            $v['resShortId'] = $v['mainid'];
            if($v['accesres'] == 'public'){
                $v['userAllowedToDL'] = true;
            }else {
                $v['userAllowedToDL'] = false;
            }
            if(empty($v['filename'])){
                $v['dir'] = true;
            }else {
                $v['dir'] = false;
            }
            $v['accessRestriction'] = $v['accesres'];
            $v['encodedUri'] = $this->config->getBaseUrl().$v['mainid'];
            $this->data[$k] = $v;
        }
        
    }
    
    private function createTreeData(array $data, string $identifier): array {
        $tree = array();
        
        $first = array(
            "mainid" => $identifier,
            "uri" => $identifier,
            "uri_dl" => $this->config->getBaseUrl().$identifier,
            "filename" => "main",
            "resShortId" => $identifier,
            "title" => 'main',
            "text" => 'main',
            "parentid" => '',
            "userAllowedToDL" => true,
            "dir" => true,
            "accessRestriction" => 'public',
            "encodedUri" => $this->config->getBaseUrl().$identifier
        );
        
        $new = array();
        foreach ($data as $a){
            $a = (array)$a;
            $new[$a['parentid']][] = $a;
        }
        $tree = $this->convertToTreeById($new, array($first));
        return $tree;
    }


    /**
     * This func is generating a child based array from a single array by ID
     *
     * @param type $list
     * @param type $parent
     * @return type
     */
    public function convertToTreeById(&$list, $parent)
    {
        $tree = array();
        foreach ($parent as $k=>$l){
        if(isset($list[$l['mainid']])){
            $l['children'] = $this->convertToTreeById($list, $list[$l['mainid']]);
        }
        $tree[] = $l;
    } 
    return $tree;
    }
}

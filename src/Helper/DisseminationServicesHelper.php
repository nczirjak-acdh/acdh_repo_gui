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
    
    public function createView(array $data = array(), string $dissemination = '', string $identifier = ''): array {
        $result = array();
        $result = $this->createTreeData($data, $identifier);
        if(count($result) > 0) {
            return $result;
        }
        return array();
    }
    
    private function createTreeData(array $data, string $identifier): array {
        $tree = array();
        
        $first = array(
            "mainid" => $identifier,
            "title" => 'main',
            "text" => 'main',
            "parentid" => ''
        );
        
        $new = array();
        $new[0] = $first;
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

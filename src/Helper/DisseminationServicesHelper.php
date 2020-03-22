<?php


namespace Drupal\acdh_repo_gui\Helper;

use acdhOeaw\acdhRepoLib\Repo;
use acdhOeaw\acdhRepoDisserv\RepoResource;
use Drupal\acdh_repo_gui\Helper\ArcheHelper;

use EasyRdf\Graph;
use EasyRdf\Resource;

/**
 * Description of DisseminationServicesHelper
 *
 * @author norbertczirjak
 */
class DisseminationServicesHelper extends ArcheHelper {
    
    private $data;
    private $repoid;
    private $result = array();
    
    public function createView(array $data = array(), string $dissemination = '', string $identifier = ''): array {
        
        $this->repoid = $identifier;
        
        switch ($dissemination) {
            case 'collection':
                $this->data = $data;
                $this->createCollection();
                break;
            case 'turtle_api':
                $this->result = array($this->turtleDissService($this->repoid));
                break;
            default:
                break;
        }
        return $this->result;
    }
    
    
    /////// Collection data functions Start ///////
    /**
     * function for the collection data steps
     */
    private function createCollection() {
        $this->modifyCollectionDataStructure();
        $this->result = $this->createTreeData($this->data, $this->repoid);
    }
    
    
    /**
     * Modify the collection data structure for the tree view
     * 
     */
    private function modifyCollectionDataStructure() {
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
    
    /**
     * Creates the tree data for the collection download views
     * @param array $data
     * @param string $identifier
     * @return array
     */
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
    
    /////// Collection data functions end ///////
    
    
    /**
      *
      * Create turtle file from the resource
      *
      * @param string $fedoraUrl
      * @return type
      */
    public function turtleDissService(string $repoid)
    {
        
        $result = array();
        $client = new \GuzzleHttp\Client();
        $repoid = $this->config->getBaseUrl().$repoid;
        try {
            $request = $client->request('GET', $repoid.'/metadata', ['Accept' => ['application/n-triples']]);
            if ($request->getStatusCode() == 200) {
                $body = "";
                $body = $request->getBody()->getContents();
                if (!empty($body)) {
                    $graph = new \EasyRdf_Graph();
                    $graph->parse($body);
                    return $graph->serialise('turtle');
                }
            }
        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            return "";
        } catch (\Exception $ex) {
            return "";
        }
    }
}

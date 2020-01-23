<?php

namespace Drupal\acdh_repo_gui\Model;

use Drupal\acdh_repo_gui\Model\ArcheModel;
/**
 * Description of ApiModel
 *
 * @author nczirjak
 */
class ChildApiModel extends ArcheModel {
    
    private $repodb;
    
    public function __construct() {
        //set up the DB connections
        \Drupal\Core\Database\Database::setActiveConnection('repo');
        $this->repodb = \Drupal\Core\Database\Database::getConnection('repo');
    }
    
    public function getViewData(string $identifier = "", int $limit = 10, int $page = 0, string $orderby = "titleasc" ): array {
        $result = array();
        $idResult = array();
        
        $order = $this->ordering($orderby);
        $prop = $order->property;
        $ord = $order->order;
 

            
        //get the requested sorting
        try {
            $query = $this->repodb->query(
                    "select * from child_view_func(:id, :limit, :offset, :order, :property)", 
                    array(':id' => $identifier,  ':limit' => intval($limit), ':offset' => intval($page), ':order' => $ord, ':property' => $prop)
            );
            
            $result = $query->fetchAll();
        
            echo "<pre>";
            var_dump($result);
            echo "</pre>";
           
                } catch (Exception $ex) {
            $result = array();
            echo "<pre>";
            var_dump($ex->getMessage());
            echo "</pre>";
        } catch(\Drupal\Core\Database\DatabaseExceptionWrapper $ex ) {
            echo "<pre>";
            var_dump($ex->getMessage());
            echo "</pre>";
            $result = array();
        }
        
        $this->changeBackDBConnection();
        return $result;
    }
    
    /**
     * Create the order values for the sql
     * 
     * @param string $orderby
     * @return object
     */
    private function ordering(string $orderby = "titleasc"): object {
        $result = new \stdClass();
        $result->property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle';
        $result->order = 'value asc';
        
        if($orderby == "titleasc") {
            $result->property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle';
            $result->order = 'value asc';
        }else if ($orderby == "titledesc") {
            $result->property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle';
            $result->order = 'value desc';
        }else if ($orderby == "dateasc") {
            $result->property = 'http://fedora.info/definitions/v4/repository#lastModified';
            $result->order = 'value asc';            
        }else if ($orderby == "datedesc") {
            $result->property = 'http://fedora.info/definitions/v4/repository#lastModified';
            $result->order = 'value desc';
        }
        return $result;
    }
    
    /**
     * Get the number of the child resources for the pagination
     * 
     * @param string $identifier
     */
    public function getCount(string $identifier): int {
        
        try {
            $query = $this->repodb->query("select num from child_view_sum_func(:id)", array(':id' => $identifier));
            $result = $query->fetch();
            if(isset($result->num)) {
                return (int)$result->num;
            }
        } catch (Exception $ex) {
            return 0;
        } catch(\Drupal\Core\Database\DatabaseExceptionWrapper $ex ) {
            return 0;
        }
        $this->changeBackDBConnection();
        return 0;
    }
}

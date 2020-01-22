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
        $return = array();
        //run the actual query
        //$identifier = 'https://id.acdh.oeaw.ac.at/uuid/57777494-57e5-6f8f-c170-461cecbb44b3';
        $order = $this->ordering($orderby);
        
        $prop = $order->property;
        $ord = $order->order;
        $direction = $order->direction;
        //get the requested sorting
        try {
            $query = $this->repodb->query("select id from child_view_func(:id) where property = :property  order by $ord $direction limit :limit offset :offset", array(':id' => $identifier, ':property' => $prop, ':limit' => $limit, ':offset' => $page));
            $idResult = $query->fetchAllAssoc('id');
        } catch (Exception $ex) {
            $result = array();
        } catch(\Drupal\Core\Database\DatabaseExceptionWrapper $ex ) {
            $result = array();
        }
                
        //get the actual view resources by the sorting
        if(count($idResult) > 0) {
            $idOrder = array_keys($idResult);
            $ids = implode(", ", $idOrder);
            try {
                $query = $this->repodb->query("select * from child_view_func(:id) where id IN ($ids)", array(':id' => $identifier));
                $result = $query->fetchAll();
                $result['order'] = $idOrder;
            } catch (Exception $ex) {
                $result = array();
            } catch(\Drupal\Core\Database\DatabaseExceptionWrapper $ex ) {
                $result = array();
            }
        }
        $this->changeBackDBConnection();
        return $result;
    }
    
    private function ordering(string $orderby = "titleasc"): object {
        $result = new \stdClass();
        $result->property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle';
        $result->order = 'value';
        $result->direction = 'asc';
        
        
        if($orderby == "titleasc") {
            $result->property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle';
            $result->order = 'value';
            $result->direction = 'asc';
        }else if ($orderby == "titledesc") {
            $result->property = 'https://vocabs.acdh.oeaw.ac.at/schema#hasTitle';
            $result->order = 'value';
            $result->direction = 'desc';
        }else if ($orderby == "dateasc") {
            $result->property = 'http://fedora.info/definitions/v4/repository#lastModified';
            $result->order = 'value ';
            $result->direction = 'asc';
        }else if ($orderby == "datedesc") {
            $result->property = 'http://fedora.info/definitions/v4/repository#lastModified';
            $result->order = 'value';
            $result->direction = 'desc';
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

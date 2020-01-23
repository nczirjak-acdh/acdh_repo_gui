<?php

namespace Drupal\acdh_repo_gui\Controller;

use Drupal\Core\Controller\ControllerBase;
use acdhOeaw\acdhRepoLib\Repo;
use acdhOeaw\acdhRepoLib\RepoResource;
use Drupal\acdh_repo_gui\Model\ChildApiModel;
use Drupal\acdh_repo_gui\Helper\ChildApiHelper;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

use GuzzleHttp\Client;
/**
 * Description of ChildApiController
 *
 * @author nczirjak
 */
class ChildApiController extends ControllerBase {
    
    private $config;
    private $model;
    private $helper;
    private $data;
    private $childNum;
    
    public function __construct() {
        $this->config = Repo::factory($_SERVER["DOCUMENT_ROOT"].'/modules/custom/acdh_repo_gui/config.yaml');
        (isset($_SESSION['language'])) ? $this->siteLang = strtolower($_SESSION['language'])  : $this->siteLang = "en";
        $this->langConf = $this->config('acdh_repo_gui.settings');
        $this->model = new ChildApiModel();
        $this->helper = new ChildApiHelper();
        $this->data = new \stdClass();
    }
    
    /**
     * This API will generate the child html view.
     *
     * @param string $identifier - the UUID
     * @param string $page
     * @param string $limit
     */
    public function repo_child_api(string $identifier, string $limit, string $page, string $order): Response
    {
        if (strpos($identifier, $this->config->getSchema()->__get('drupal')->uuidNamespace) === false) {
            $identifier = $this->config->getSchema()->__get('drupal')->uuidNamespace.$identifier;
        }
        $this->childNum = $this->model->getCount($identifier);
        

        if($this->childNum < 1) {
            goto end;
        }
        
        $this->data->sum = $this->childNum;
        $this->data->limit = $limit;
        $this->data->page = $page;
        $this->data->order = $order;
        $this->data->identifier = $identifier;
        $this->data->numPage = ceil((int)$this->childNum / (int)$limit);
        
        ($page == 0) ? $offset = 0 : $offset = $page * $limit;
        $data = $this->model->getViewData($identifier, (int)$offset, (int)$page, $order);
        echo "<pre>";
        var_dump($data);
        echo "</pre>";

        die();
        
       
        $this->data->data = $this->helper->createView($data);
        
        if(count((array)$this->data->data) <= 0) {
            $this->data->errorMSG = $this->langConf->get('errmsg_no_child_resources') ? $this->langConf->get('errmsg_no_child_resources') : 'There are no Child resources';
        }
        
        end:
        $build = [
            '#theme' => 'acdh-repo-gui-child',
            '#data' => $this->data,
        ];
        
        return new Response(render($build));
    }
    
}

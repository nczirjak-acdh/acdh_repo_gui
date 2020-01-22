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
    private $data = array();
    private $childNum;
    
    public function __construct() {
        $this->config = Repo::factory($_SERVER["DOCUMENT_ROOT"].'/modules/custom/acdh_repo_gui/config.yaml');
        (isset($_SESSION['language'])) ? $this->siteLang = strtolower($_SESSION['language'])  : $this->siteLang = "en";
        $this->langConf = $this->config('acdh_repo_gui.settings');
        $this->model = new ChildApiModel();
        $this->helper = new ChildApiHelper();
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
        $this->childNum = 0 ;
        if($this->childNum < 1) {
            goto end;
        }
        
        $data = $this->model->getViewData($identifier, (int)$limit, (int)$page, $order);
        $this->data = $this->helper->createView($data);
        
        if(count((array)$this->data) <= 0) {
            $this->data['errorMSG'] = $this->langConf->get('errmsg_no_child_resources') ? $this->langConf->get('errmsg_no_child_resources') : 'There are no Child resources';
        }
        
        end:
        $build = [
            '#theme' => 'acdh-repo-gui-child',
            '#data' => $this->data,
        ];
        
        return new Response(render($build));
    }
    
}

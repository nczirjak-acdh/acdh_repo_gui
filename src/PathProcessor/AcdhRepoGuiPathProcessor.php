<?php

namespace Drupal\acdh_repo_gui\PathProcessor;

use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Symfony\Component\HttpFoundation\Request;


class AcdhRepoGuiPathProcessor implements InboundPathProcessorInterface
{
    public function processInbound($path, Request $request)
    {   
        if (strpos($path, '/repo_detail/') === 0) {
            $names = preg_replace('|^\/repo_detail\/|', '', $path);
            $names = str_replace('/', ':', $names);
            return "/repo_detail/$names";
        }
        
        return $path;
    }
}

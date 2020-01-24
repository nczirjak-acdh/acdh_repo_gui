<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\acdh_repo_gui\Helper;

/**
 * Description of PagingHelper
 *
 * @author nczirjak
 */
class PagingHelper {
    
    private $data;
    
    public function createView(array $data): array {
        $this->data = new \stdClass();
        $this->data->limit = (int)$data['limit'];
        $this->data->page = (int)$data['page'];
        $this->data->order = $data['order'];
        $this->data->numPage = (int)$data['numPage'];
        $this->data->sum = (int)$data['sum'];
        
        $this->data->pager = $this->createPaginationHTML();
        return array($this->data);
    }
    
    public function createPaginationHTML()
    {
        $out = "";
        $page = $this->data->page;
        if (ceil($this->data->sum / $this->data->limit) > 0){ 
            $out .= '<ul class="pagination">';
            $out .= '<li class="pagination-item"><a data-pagination=1"><i class="material-icons">first_page</i></a></li>';
            if ($page > 1) {
                $np = $page - 1;
                $out .= '<li class="pagination-item"><a data-pagination='.$np.'"><i class="material-icons">chevron_left</i></a></li>';
            }
            if ($page > 3) {
                $out .= '<li class="pagination-item active"><a data-pagination=1">1</a></li>';
                $out .= '<li class="pagination-item">...</li>';
            }

            if ($page-2 > 0) {
                $np = $page - 2;
                $out .= '<li class="pagination-item"><a data-pagination='.$np.'">'.$np.'</a></li>';
            }

            if ($page-1 > 0) {
                $np = $page - 1;
                $out .= '<li class="pagination-item"><a data-pagination='.$np.'">'.$np.'</a></li>';
            }

            $out .= '<li class="pagination-item active"><a data-pagination='.$page.'">'.$page.'</a></li>';

            if ($page+1 < ceil($this->data->sum / $this->data->limit)+1) { 
                $np = $page + 1;
                $out .= '<li class="pagination-item"><a data-pagination='.$np.'">'.$np.'</a></li>'; 
            }
            if ($page+2 < ceil($this->data->sum  / $this->data->limit)+1) { 
                $np = $page + 2;
                $out .= '<li class="pagination-item"><a data-pagination='.$np.'">'.$np.'</a></li>'; 
            }

            if ($page < ceil($this->data->sum / $this->data->limit)-2) {
                $out .= '<li class="dots">...</li>';
                $np = ceil($this->data->sum / $this->data->limit);
                $out .= '<li class="end"><a data-pagination='.$np.'">'.$np.'</a></li>';
            }

            if ($page < ceil($this->data->sum / $this->data->limit)) {
                $np = $page+1;
                $out .= '<li class="next"><a data-pagination='.$np.'"><i class="material-icons">chevron_right</i></a></li>';
            }
            $out .= '<li class="pagination-item"><a data-pagination='.$this->data->numPage.'"><i class="material-icons">last_page</i></a></li>';
            $out .= '</ul>';
        }
        /*
        $prevlabel = "";
        $nextlabel = "";
        
        
        
        // previous
        if ($page == 0) {
            //Don't show prev if we are on the first page
            //$out.= "<li class='pagination-item'><span>" . $prevlabel . "</span></li>";
        } else {
            $out.= "<li class='pagination-item'><a data-pagination='" . $page . "'>" . $prevlabel . "</a>\n</li>";
        }

        $pmin = ($page > $adjacents) ? ($page - $adjacents) : 1;
        $pmax = ($page < ($tpages - $adjacents)) ? ($page + $adjacents) : $tpages;
        
        for ($i = $pmin; $i <= $pmax; $i++) {
            if ($i-1 == $page) {
                $out.= "<li class='pagination-item active'><a data-pagination='".$i."'>" . $i . "</a></li>\n";
            } else {
                $out.= "<li class='pagination-item'><a data-pagination='" . $i . "'>" . $i . "</a>\n</li>";
            }
        }

        // next
        if ($page < $tpages-1) {
            $out.= "<li class='pagination-item'><a data-pagination='" . ($page + 2) . "'>" . $nextlabel . "</a>\n</li>";
        } else {
            //Don't show next if we are on the last page
            //$out.= "<li class='pagination-item'><span style=''>" . $nextlabel . "</span></li>";
        }
        
        if ($page < ($tpages - $adjacents)) {
            $out.= "<li class='pagination-item'><a data-pagination='" . $tpages . "'><i class='material-icons'>&#xE5DD;</i></a></li>";
        }
        $out.= "";
        */
        return $out;
    }
}

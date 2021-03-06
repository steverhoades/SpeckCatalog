<?php

namespace Catalog\Adapter;

use Zend\Paginator\Adapter\DbSelect;

class PaginatorDbSelect extends DbSelect
{
    public function getItems($a, $b)
    {
        $items=parent::getItems($a, $b);
        $return = array();
        foreach($items as $item){
            $return[] = $item;
        }
        return $return;
    }
}

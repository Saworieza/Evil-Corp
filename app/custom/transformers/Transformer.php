<?php

namespace custom\transformers;


abstract class Transformer {

    protected  $param;

    function __construct($param = null)
    {
        $this->param = $param;
    }

    public function transformCollection(array $items)
    {
        return array_filter(array_map([$this, 'transform'], $items));

    }

    protected function removePivot(&$node)
    {
        foreach($node as &$elem)
        {
            unset($elem['pivot']);
        }
    }

    protected function renameKey(&$node, $oldKey, $newKey)
    {
        foreach($node as &$elem)
        {
            $elem[$newKey] = $elem[$oldKey];
            unset($elem[$oldKey]);
        }
    }

    protected function countIf($node, $field, $value){
        $ret = 0;

        foreach($node as $item)
        {
            if(is_array($value))
            {
                if(in_array($item[$field], $value)) $ret++;
            } else {
                if($item[$field] == $value) $ret++;
            }
        }
        return $ret;

    }

    protected function groupBy($node, $field, $descriptions=null)
    {
        $ret = [];
        $arr = array_count_values(array_pluck($node, $field));
        foreach($arr as $k=>$v)
        {
            if(isset($descriptions))
            {
                $ret[] = ["id"=>$k, "group"=>$descriptions[$k], 'count'=>$v];
            } else {
                $ret["_$k"] =$v;
            }
        }
        return $ret;
    }

    public abstract function transform($item);


}
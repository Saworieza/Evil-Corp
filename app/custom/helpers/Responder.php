<?php

namespace custom\helpers;

class Responder {

    private $succees;
    private $msg = null;
    private $data = null;
    private $extra = null;
    private static $sql = [];
    private $errors = null;
    private $alert = null;
    private static $dbBag =[];
    private static $timers=[];


    public static function dd($var = null)
    {
        echo \Response::json($var);
        die;
    }

    public static function DBListen()
    {
        \DB::listen(function($sql){
            static::$sql[] = $sql;

        });
    }


    public static function startTimer($name)
    {
        static::$timers[$name] = ['start'=>microtime_float(), 'end'=>0];
    }

    public static function stopTimer($name)
    {
        static::$timers[$name]['end'] = microtime_float();
    }

    public static function addDebugInfo($key, $data)
    {
        if(! isset($key))
        {
            static::$dbBag[] = $data;
        } else {
            static::$dbBag[$key] = $data;
        }
    }

    public static function addDebugMark($mark, $where='')
    {
        static::$dbBag['_marks'][] = $mark. ($where!=='' ? ' ('.$where.')' : '');
    }

    public static function json($success)
    {
        $instance = new self;
        $instance->succees = $success;

        return $instance;
    }

    public function dataTake($num)
    {
        $this->data = array_slice($this->data, 0, $num);

        return $this;
    }

    public function dataSort($field, $orderDesc = false)
    {

        if(! $orderDesc)
        {
            usort($this->data, function($a, $b ) use($field){
                return $a[$field] > $b[$field];
            });
        } else {
            usort($this->data, function($a, $b ) use($field){
                return $a[$field] < $b[$field];
            });
        }

        return $this;
    }

    public function withMessage($msg, array $params = null)
    {

       if($params)
       {
            $val = trans('messages.'.$msg, $params);

       } else {
            $val = trans('messages.'.$msg);

       }
        $this->msg [] = $val;

        return $this;
    }

    public function withValidator(\Illuminate\Validation\Validator $validator )
    {
        $msgs = $validator->messages()->toArray();
        foreach($msgs as $key=>$val)
        {
            $msgs[$key] = implode(' ', $val);
        }
        $this->errors = $msgs;
        return $this;
    }


    public function withDataTransform($data, $tr, $key = null )
    {

        $tr = explode(':', $tr);

        $pr = isset($tr[1]) ? $tr[1] : null;

        $transformer = "\\custom\\transformers\\".$tr[0];
        $transformer = new $transformer($pr);


        if(is_array($data)){
            $trData = $transformer->transformCollection($data);

        } elseif(get_class($data)=='Illuminate\Database\Eloquent\Collection')
        {
            $trData = $transformer->transformCollection($data->toArray());

        } elseif(is_subclass_of($data,'Eloquent'))
        {
            $trData = $transformer->transform($data->toArray());

        } else {
            $trData = $transformer->transform($data);
        }


        if( isset($key) )
        {
            $this->data[$key] = $trData;
        } else {

            $this->data = $trData;

        }


        return $this;
    }

    public function withExtraData($data)
    {
        $this->extra = $data;
        return $this;
    }

    public function withData($data, $options = null)
    {
        if(isset($options['forget']))
            arrayFilterByPath($data, 'forget', $options['forget']);

        if(isset($options['only']))
            arrayFilterByPath($data, 'only', $options['only']);

        if(isset($options['indexBy']))
        {
            $result = null;
            foreach($data as $item)
            {
                $result[ $item[$options['indexBy']]] = $item;
            }
            $data = $result;
        }

        if(isset($options['key']))
        {
            $this->data[$options['key']] = $data;
        } else {
            $this->data = $data;
        }

        return $this;
    }

    public function withAlert($alert)
    {
        $this->alert = $alert;
        return $this;
    }


    public function send()
    {
        $payLoad['success'] = $this->succees;

        if(isset($this->msg))
        {
            $payLoad['message'] = implode(' ', $this->msg);
        }

        if(isset($this->alert)) $payLoad['alert'] = $this->alert;
        if(isset($this->errors)) $payLoad['errors'] = $this->errors;
        if(isset($this->data)) $payLoad['data'] = $this->data;
        if(isset($this->extra)) $payLoad['extra'] = $this->extra;

        if(\App::environment()=='local')
        {

            $payLoad['_debug'] = static::$dbBag;
            $payLoad['_debug']['sql'] = static::$sql;
            $payLoad['_debug']['_midredpo'] = Midrepo::all();
            $payLoad['_debug']['_input'] = \Input::all();
            $payLoad['_debug']['_SERVER'] = $_SERVER;
            $payLoad['_debug']['_route'] = [
                'action'=> \Route::currentRouteAction(),
                'uri'=>\Route::getCurrentRoute()->getUri(),
                'method'=> \Route::current()->methods(),
                'before_filters'=> \Route::current()->beforeFilters(),
                'after_filters'=> \Route::current()->afterFilters()
            ];

            if(count(static::$timers) > 0)
            {
                foreach(static::$timers as $k=>$v)
                {
                    if($v['end']==0) $v['end'] = microtime_float();
                    $payLoad['_debug']['_timers'][$k] = $v['end']-$v['start'];
                }
            }
        }

        return \Response::json($payLoad, 200, ['application/json; charset=utf-8']);
    }
} 
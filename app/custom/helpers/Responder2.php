<?php

namespace custom\helpers;

class Responder2 {

    private static $sql = [];
    private static $dbBag =[];
    private static $timers=[];

    private $succees;
    private $msg = null;
    private $data = null;
    private $errors = null;


    /**
     * Creator
     *
     * @param $success
     * @return Responder
     */
    public static function success($success)
    {
        $instance = new self;
        $instance->succees = $success;

        return $instance;
    }

    /**
     * Start listening and stores SQLs
     */
    public static function DBListen()
    {
        \DB::listen(function($sql){
            static::$sql[] = $sql;
        });
    }

    /**
     * Start a timer
     *
     * @param $name
     */
    public static function startTimer($name)
    {
        static::$timers[$name] = ['start'=>microtime_float(), 'end'=>0];
    }

    /**
     * Stop a timer
     *
     * @param $name
     */
    public static function stopTimer($name)
    {
        static::$timers[$name]['end'] = microtime_float();
    }

    /**
     * Add a variable to debug output
     *
     * @param $key
     * @param $data
     */
    public static function addDebugInfo($key, $data)
    {
        static::$dbBag[$key] = $data;
    }

    /**
     * Output a message
     *
     * @param $msg Message text
     * @param array $msgParams Message params to send to the translation function
     * @param string $alert Alert type (info, warning, danger, success)
     * @return $this
     */
    public function withMessage($msg, array $msgParams = [], $alert = 'success' )
    {
        $val = trans('messages.'.$msg, $msgParams);

        $this->msg['text']  = $val;
        $this->msg['alert'] = $alert;

        return $this;
    }

    /**
     * Output a validator's message bag
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return $this
     */
    public function withValidator(\Illuminate\Validation\Validator $validator )
    {
        $msgs = $validator->messages()->toArray();

        foreach($msgs as $key=>$val)
            $msgs[$key] = implode(' ', $val);

        $this->errors = $msgs;

        return $this;
    }


    /**
     * Output data
     *
     * @param $data the data (array, object, eloquent object, eloquent collection)
     * @param null $transformerClass Transformer name if any
     * @param null $key output data key
     * @return $this
     */
    public function withData($data, $transformerClass = null, $key = null )
    {

        $key = isset($key) ? $key : count($this->data);

        if( isset($transformerClass) )
        {
            $transformer = "\\custom\\transformers\\".$transformerClass;
            $transformer = new $transformer();

            if(is_array($data)){
                $data = $transformer->transformCollection($data);

            } elseif(get_class($data)=='Illuminate\Database\Eloquent\Collection')
            {
                $data = $transformer->transformCollection($data->toArray());

            } elseif(is_subclass_of($data,'Eloquent'))
            {
                $data = $transformer->transform($data->toArray());

            } else {
                $data = $transformer->transform($data);
            }
        }

         $this->data[$key] = $data;

        return $this;
    }

    /**
     * Send the response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function send()
    {
        $payLoad['success'] = $this->succees;

        if(isset($this->msg))
            $payLoad['message'] = $this->msg;

        if(isset($this->errors))
            $payLoad['errors'] = $this->errors;

        if(count($this->data)==1)
            $payLoad['data'] = array_values($this->data)[0];
        else
            $payLoad['data'] = $this->data;


        if( \Config::get('app.debug') )
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

            // stop pending timers
            foreach(static::$timers as $k=>$v)
            {
                if($v['end']==0) $v['end'] = microtime_float();
                $payLoad['_debug']['_timers'][$k] = $v['end']-$v['start'];
            }
        }

        return \Response::json($payLoad, 200, ['application/json; charset=utf-8']);
    }
} 
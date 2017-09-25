<?php

namespace custom\exceptions;
use Exception;

class ApiException extends Exception {

    public $msg;
    public $params =[];

    public function __construct($msg, $params=[]) {

        parent::__construct('Api Exception', 0, null);

        $this->params = $params;
        $this->msg = $msg;

    }


}
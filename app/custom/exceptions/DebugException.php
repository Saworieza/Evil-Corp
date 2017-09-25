<?php

namespace custom\exceptions;
use Exception;

class DebugException extends Exception {

    public $var = null;

    public function __construct($var) {

        parent::__construct('Debug Exception', 0, null);
        $this->var = $var;


    }


}
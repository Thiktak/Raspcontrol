<?php

namespace lib;

Class FatalError extends InternalError {

    public function __construct($title, $message = null)
    {
        parent::__construct($title, $message, 'error');
    }
}

?>
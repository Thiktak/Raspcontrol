<?php

namespace lib;

Class InternalError extends \Exception {
    private $title;
    private $msgtype;

    public function __construct($title, $message, $type = null)
    {
        parent::__construct($message);
        $this->title = $title;
        $this->setMessageType($type);
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setMessageType($type)
    {
        $this->msgtype = $type;
    }

    public function getMessageType()
    {
        return $this->msgtype;
    }
}

?>
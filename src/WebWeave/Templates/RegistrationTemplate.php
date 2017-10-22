<?php

namespace WebWeave\Templates;


class RegistrationTemplate
{

    public $currentTemplate;

    public function __construct()
    {
        $this->currentTemplate = $this->getTemplate();
    }

    public function setVars($value, $target)
    {
        $this->currentTemplate = str_replace('@'.$target, $value,$this->currentTemplate);
    }

    public function getTemplate()
    {
        $template = file_get_contents(__DIR__.'/registration.php.html');

        return $template;
    }

}
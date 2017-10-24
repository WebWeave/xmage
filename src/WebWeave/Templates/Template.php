<?php

namespace WebWeave\Templates;


class Template
{

    public $currentTemplate;

    public $template;


    public function setVars($value, $target)
    {
        $this->currentTemplate = str_replace('@'.$target, $value, $this->currentTemplate);
    }

    public function setTemplate($template)
    {
        $this->currentTemplate = file_get_contents(__DIR__.'/'.$template);
    }

}
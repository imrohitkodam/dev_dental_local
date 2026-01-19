<?php
/* This file has been prefixed by <PHP-Prefixer> for "XT Platform" */

namespace XTP_BUILD\Illuminate\Support;

class ServiceProvider
{
    /**
     * @var \App
     */
    public $app;

    public function __construct()
    {
        include_once __DIR__.'/App.php';
        $this->app = new \App();
    }
}

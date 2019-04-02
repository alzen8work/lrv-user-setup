<?php

namespace alzen8work\DockerAssistant\Facades;

use Illuminate\Support\Facades\Facade;

class DockerAssistant extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dockerassistant';
    }
}

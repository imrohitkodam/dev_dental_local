<?php

include_once __DIR__.DIRECTORY_SEPARATOR.'Router'.DIRECTORY_SEPARATOR.'AcymRouter.php';

function AcymBuildRoute(&$query)
{
    $router = new AcyMailing\Router\AcymRouter();

    return $router->build($query);
}

function AcymParseRoute(&$segments)
{
    $router = new AcyMailing\Router\AcymRouter();

    return $router->parse($segments);
}

<?php

namespace YOOtheme\Theme\Joomla;

use YOOtheme\Http\Request;
use YOOtheme\Http\Response;

class MenuController
{
    public static function getItems(Request $request, Response $response, MenuConfig $menu)
    {
        return $response->withJson($menu->items);
    }
}

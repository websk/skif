<?php

namespace WebSK\Skif;

use Slim\Http\Request;
use Slim\Http\Response;

class AdminHandler
{
    public function __invoke(Request $request, Response $response)
    {
        \Skif\AdminRouter::route();
    }
}

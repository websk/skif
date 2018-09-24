<?php

namespace WebSK\Skif;

use Slim\Http\Request;
use Slim\Http\Response;

class AdminHandler
{
    /**
     * @param Request $request
     * @param Response $response
     */
    public function __invoke(Request $request, Response $response)
    {
        \Skif\AdminRouter::route();
    }
}

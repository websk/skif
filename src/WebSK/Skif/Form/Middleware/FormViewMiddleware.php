<?php

namespace WebSK\Skif\Form\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use WebSK\Skif\Form\FormService;
use WebSK\Utils\Url;

class FormViewMiddleware
{

    protected FormService $form_service;

    public function __construct(FormService $form_service) {
        $this->form_service = $form_service;
    }
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $form_url = $route->getArgument('form_url') ?? null;

        if (!isset($form_url)) {
            return $handler->handle($request);
        }

        $form_url = Url::appendLeadingSlash($form_url);

        $form_id = $this->form_service->getIdByUrl($form_url);

        if (!$form_id) {
            return new Response();
        }

        return $handler->handle($request);
    }
}
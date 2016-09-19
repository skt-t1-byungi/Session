<?php
namespace SktT1Byungi\Session;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SktT1Byungi\Session\Session;

class Middleware
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        Session::manager()->start();

        return $next($request, $response);
    }
}

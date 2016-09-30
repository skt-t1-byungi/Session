<?php
namespace SktT1Byungi\Session;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SktT1Byungi\Session\Manager;

class Middleware
{
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $this->manager->start();

        return $next($request, $response);
    }
}

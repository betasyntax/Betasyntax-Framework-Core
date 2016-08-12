<?php Betasyntax\Router;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

Class Middleware implements MiddlewareInteface
{
    public function __invoke(Request $request, Response $response, callable $next) {
        // ...
    }
}
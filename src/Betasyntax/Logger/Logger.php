<?php namespace Betasyntax\Logger;

use Closure;
use Betasyntax\Core\Interfaces\Router\Middleware;

class Logger implements Middleware
{
    public function __invoke($request,$reponse, Closure $next)
    {
        echo 'logger';

        return $next($request);
    }


}
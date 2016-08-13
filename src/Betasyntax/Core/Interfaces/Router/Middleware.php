<?php namespace Betasyntax\Core\Interfaces\Router;

use Closure;

interface Middleware {

  /**
   * Handle an incoming request.
   *
   * @param  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function __invoke(Request $request, Response $response, callable $next);

}

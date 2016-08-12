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
  public function __invoke($request, $response, Closure $next);

}

<?php namespace Betasyntax\Core\Services\ServiceProvider;

// use League\Container\ServiceProvider\AbstractServiceProvider;

// class ServiceProvider extends AbstractServiceProvider
// {
//     /**
//      * The provides array is a way to let the container
//      * know that a service is provided by this service
//      * provider. Every service that is registered via
//      * this service provider must have an alias added
//      * to this array or it will be ignored.
//      *
//      * @var array
//      */
//     protected $provides = [
//         'Betasyntax\Core\Application',
//         'Betasyntax\Router',
//         'Betasyntax\Config',
//         'Betasyntax\View\View'
//     ];

//     *
//      * This is where the magic happens, within the method you can
//      * access the container and register or retrieve anything
//      * that you need to, but remember, every alias registered
//      * within this method must be declared in the `$provides` array.
     
//     public function register()
//     {
//         // $this->getContainer()->add('key', 'value');
        
//         $this->getContainer()->add('Betasyntax\Core\Application');

//         $this->getContainer()->add('Betasyntax\Router')
//              ->withArgument('Betasyntax\Core\Application');

//         $this->getContainer()->add('Betasyntax\Config');
//         $this->getContainer()->add('Betasyntax\View\View')
//              ->withArgument('Betasyntax\Core\Application'); 

//     }
// }


use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

class SomeServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * The provides array is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @var array
     */
    protected $provides = [
        'Betasyntax\Core\Application',
        'Betasyntax\Router',
        'Betasyntax\Config',
        'Betasyntax\View\View'
    ];
    
    /**
     * In much the same way, this method has access to the container
     * itself and can interact with it however you wish, the difference
     * is that the boot method is invoked as soon as you register
     * the service provider with the container meaning that everything
     * in this method is eagerly loaded.
     *
     * If you wish to apply inflectors or register further service providers
     * from this one, it must be from a bootable service provider like
     * this one, otherwise they will be ignored.
     */
    public function boot()
    {
        $this->getContainer()
             ->inflector('SomeType')
             ->invokeMethod('someMethod', ['some_arg']);
            
        $this->getContainer()->add('Betasyntax\Core\Application');
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {

        $this->getContainer()->add('Betasyntax\Router')
             ->withArgument('Betasyntax\Core\Application');

        $this->getContainer()->add('Betasyntax\Config');
        $this->getContainer()->add('Betasyntax\View\View')
             ->withArgument('Betasyntax\Core\Application'); 
    }
}
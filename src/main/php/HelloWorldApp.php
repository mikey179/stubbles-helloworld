<?php
/**
 * This file is part of bit/helloworld.
 */
namespace stubbles\helloworld;
use stubbles\webapp\RoutingConfigurator;
use stubbles\webapp\WebApp;

class HelloWorldApp extends WebApp
{
    /**
     * configures routing for this web app
     *
     * @param  \stubbles\webapp\routing\RoutingConfigurator  $routing
     */
    protected function configureRouting(RoutingConfigurator $routing)
    {
        $routing->apiIndexOnGet('/api$')
                ->supportsMimeType(
                        'text/html',
                        'stubbles\helloworld\ApiHtml'
                )->excludeFromApiIndex();
        $routing->onGet('/{greeted}?', 'stubbles\helloworld\Hello');
        $routing->onPost('/$', 'stubbles\helloworld\CreateGreeted');
        $routing->onDelete('/{greeted}', 'stubbles\helloworld\DeleteGreeted');
    }
}

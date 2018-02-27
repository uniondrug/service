<?php

namespace Uniondrug\Service;

use Phalcon\Di\ServiceProviderInterface;

class ServiceServiceProvider implements ServiceProviderInterface
{
    public function register(\Phalcon\DiInterface $di)
    {
        $di->setShared(
            'serviceServer',
            function () {
                return new Server();
            }
        );

        $di->setShared(
            'serviceClient',
            function () {
                return new Client();
            }
        );
    }
}

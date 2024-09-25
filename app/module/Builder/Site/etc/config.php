<?php declare(strict_types=1);

use Builder\Site\Event\Configuration;

return [
    'events' => [
        'application.manager.viewConfig' => [
            [Configuration::class => 'viewConfig'],
        ],
    ],
    'additionalConfigurations' => [
        'footer.signature' => [
            'namespace' => 'Footer',
            'label' => 'Footer Additional Subscription',
        ],
    ]
];
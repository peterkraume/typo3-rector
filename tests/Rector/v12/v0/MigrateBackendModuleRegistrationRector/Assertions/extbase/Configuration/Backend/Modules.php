<?php

return [
    'web_ExtkeyExample' => [
        'parent' => 'web',
        'position' => [
            'after' => 'web_info',
        ],
        'access' => 'admin',
        'workspaces' => 'live',
        'iconIdentifier' => 'module-example',
        'labels' => 'LLL:EXT:extkey/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'Extkey',
        'controllerActions' => [
            'Vendor\Extension\Controller\MyExtbaseExampleModuleController::class' => [
                'list',
                'detail',
            ],
        ],
    ],
];
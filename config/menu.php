<?php

return [
    'admin' => [
        [
            'name' => 'ITI Database',
            'route' => 'languages.index',
            'icon' => 'fas fa-tachometer-alt',
            'related' => ['admin.dashboard']
        ],
        [
            'name' => 'Quiz',
            'route' => 'makers.index',
            'icon' => 'fa fa-circle text-warning',
            'related' => [
                'makers.create',
                'makers.edit',
                'makers.index',
            ]
        ],
        [
            'name' => 'CBT',
            'route' => 'carmodels.index',
            'icon' => 'fa fa-circle text-warning',
            'related' => [
                'carmodels.create',
                'carmodels.edit',
                'carmodels.index',
            ]
        ],
        [
            'name' => 'Question Bank',
            'route' => 'caryears.index',
            'icon' => 'fa fa-circle text-warning',
            'related' => [
                'caryears.create',
                'caryears.edit',
                'caryears.index',
            ]
        ],
        [
            'name' => 'User List',
            'route' => 'carvariants.index',
            'icon' => 'fa fa-circle text-warning',
            'related' => [
                'carvariants.create',
                'carvariants.edit',
                'carvariants.index',
            ]
        ],
        [
            'name' => 'Admin',
            'route' => 'carvariants.index',
            'icon' => 'fa fa-circle text-warning',
            'related' => [
                'carvariants.create',
                'carvariants.edit',
                'carvariants.index',
            ]
        ]
    ]
];

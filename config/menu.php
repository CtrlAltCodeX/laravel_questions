<?php

return [
    'admin' => [
        [
            'name' => 'ITI Database',
            'route' => 'languages.index',
            'icon' => 'fas fa-tachometer-alt',
            'related' => ['admin.dashboard'],
            'sub-menus' => [
                [
                    'name' => 'Language',
                    'route' => 'languages.index',
                    'icon' => 'fas fa-tachometer-alt',
                ],
                [
                    'name' => 'Main Category',
                    'route' => 'category.index',
                    'icon' => 'fas fa-tachometer-alt',
                ],
                [
                    'name' => 'Sub-Category',
                    'route' => 'sub-category.index',
                    'icon' => 'fas fa-tachometer-alt',
                ],
            ]
        ],
        [
            'name' => 'Quiz',
            'route' => 'languages.index',
            'icon' => 'fa fa-circle text-warning',
            'related' => [
                'makers.create',
                'makers.edit',
                'makers.index',
            ]
        ],
        [
            'name' => 'CBT',
            'route' => 'languages.index',
            'icon' => 'fa fa-circle text-warning',
            'related' => [
                'carmodels.create',
                'carmodels.edit',
                'carmodels.index',
            ]
        ],
        [
            'name' => 'Question Bank',
            'route' => 'languages.index',
            'icon' => 'fa fa-circle text-warning',
            'related' => [
                'caryears.create',
                'caryears.edit',
                'caryears.index',
            ]
        ],
        [
            'name' => 'User List',
            'route' => 'users.index',
            'icon' => 'fa fa-circle text-warning',
            'related' => [
                'carvariants.create',
                'carvariants.edit',
                'carvariants.index',
            ]
        ],
        [
            'name' => 'Admin',
            'route' => 'languages.index',
            'icon' => 'fa fa-circle text-warning',
            'related' => [
                'carvariants.create',
                'carvariants.edit',
                'carvariants.index',
            ]
        ]
    ]
];

<?php

return [
    'admin' => [
        [
            'name' => 'Dashboard',
            'route' => 'dashboard.total.count',
            'icon' => 'fa fa-home',
        ],
        [
            'name' => 'ITI Database',
            'route' => 'languages.index',
            'icon' => 'fa fa-database',
            'related' => ['admin.dashboard'],
            'sub-menus' => [
                [
                    'name' => 'Language',
                    'route' => 'languages.index',
                    'icon' => 'fa fa-language',
                ],
                [
                    'name' => 'Main Category',
                    'route' => 'category.index',
                    'icon' => 'fa fa-list-alt',
                ],
                [
                    'name' => 'Sub-Category',
                    'route' => 'sub-category.index',
                    'icon' => 'fa fa-indent',
                ],
                [
                    'name' => 'Subject',
                    'route' => 'subject.index',
                    'icon' => 'fa fa-book',
                ],
                [
                    'name' => 'Topic',
                    'route' => 'topic.index',
                    'icon' => 'fa fa-tags',
                ],
                [
                    'name' => 'Question',
                    'route' => 'question.index',
                    'icon' => 'fa fa-question-circle text-warning',
                ]
            ]
        ],
        [
            'name' => 'Question Bank',
            'route' => 'bank-question.index',
            'icon' => 'fa fa-folder-open',
        ],
        [
            'name' => 'Quiz',
            'route' => 'quiz.index',
            'icon' => 'fa fa-puzzle-piece text-warning',
        ],
        [
            'name' => 'CBT',
            'route' => 'cbt.index',
            'icon' => 'fa fa-laptop text-warning',
        ],
        [
            'name' => 'User List',
            'route' => 'users.index',
            'icon' => 'fa fa-users',
        ],
        [
            'name' => 'Admin List',
            'route' => 'super-admin.index',
            'icon' => 'fa fa-shield',
        ],
        // [
        //     'name' => 'Admin',
        //     'route' => 'languages.index',
        //     'icon' => 'fa fa-user-cog',
        // ]
    ]
];

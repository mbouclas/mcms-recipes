<?php

return [
    'recipe' => \Mcms\Recipes\Models\Recipe::class,
    'related' => \Mcms\Recipes\Models\Related::class,
    'featured' => \Mcms\Recipes\Models\Featured::class,
    'items' => [
        'slug_pattern' => '/recipe/%slug$s',
        'previewController' => '\FrontEnd\Http\Controllers\HomeController@preview',
        'images' => [
            'keepOriginals' => true,
            'optimize' => true,
            'dirPattern' => 'recipes/recipe_%id$s',
            'recommendedSize' => '500x500',
            'filePattern' => '',
            'types' => [
                [
                    'uploadAs' => 'image',
                    'name' => 'images',
                    'title' => 'Images',
                    'settings' => [
                        'default' => true
                    ]
                ],
                [
                    'name' => 'floor_plans',
                    'title' => 'Floor Plans',
                    'uploadAs' => 'image',
                    'settings' => [
                        'default' => false
                    ]
                ]
            ],
            'copies' => [
                'thumb' => [
                    'width' => 70,
                    'height' => 70,
                    'quality' => 100,
                    'prefix' => 't_',
                    'resizeType' => 'fit',
                    'dir' => 'thumbs/',
                ],
                'big_thumb' => [
                    'width' => 170,
                    'height' => 170,
                    'quality' => 100,
                    'prefix' => 't1_',
                    'resizeType' => 'fit',
                    'dir' => 'big_thumbs/',
                    'useOnAdmin' => true,
                ],
                'main' => [
                    'width' => 500,
                    'height' => 500,
                    'quality' => 100,
                    'prefix' => 'm_',
                    'resizeType' => 'fit',
                    'dir' => '/',
                ],
            ]
        ],
        'files' => [
            'dirPattern' => 'recipes/recipe_%id$s',
            'filePattern' => '',
        ]
    ],
    'categories' => [
        'slug_pattern' => '/recipes/%slug$s',
        'images' => [
            'keepOriginals' => true,
            'optimize' => true,
            'dirPattern' => 'recipes/category_%id$s',
            'filePattern' => '',
            'types' => [
                [
                    'uploadAs' => 'image',
                    'name' => 'images',
                    'title' => 'Images',
                    'settings' => [
                        'default' => true
                    ]
                ]
            ],
            'copies' => [
                'thumb' => [
                    'width' => 70,
                    'height' => 70,
                    'quality' => 100,
                    'prefix' => 't_',
                    'resizeType' => 'fit',
                    'dir' => 'thumbs/',
                ],
                'big_thumb' => [
                    'width' => 170,
                    'height' => 170,
                    'quality' => 100,
                    'prefix' => 't1_',
                    'resizeType' => 'fit',
                    'dir' => 'big_thumbs/',
                ],
                'main' => [
                    'width' => 500,
                    'height' => 500,
                    'quality' => 100,
                    'prefix' => 'm_',
                    'resizeType' => 'fit',
                    'dir' => '/',
                ],
            ]
        ]
    ]
];
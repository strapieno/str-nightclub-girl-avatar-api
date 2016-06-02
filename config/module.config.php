<?php
return [
    'service_manager' => [
        'factories' => [
            'Strapieno\Utils\Listener\ListenerManager' => 'Strapieno\Utils\Listener\ListenerManagerFactory'
        ],
        'invokables' => [
            'Strapieno\Utils\Delegator\AttachRestResourceListenerDelegator' => 'Strapieno\Utils\Delegator\AttachRestResourceListenerDelegator'
        ],
        'aliases' => [
            'listenerManager' => 'Strapieno\Utils\Listener\ListenerManager'
        ]
    ],
    // Config of nightclub_id in route exist
    'nightclub-not-found' => [
        'api-rest/nightclub/girl/avatar'
    ],
    // Register listener to listener manager
    'service-listeners' => [
        'initializers' => [
            'Strapieno\NightClub\Model\NightClubModelInitializer'
        ],
        'invokables' => [
            'Strapieno\NightClubGirlAvatar\Api\Listener\NightClubGirlRestListener'
                => 'Strapieno\NightClubGirlAvatar\Api\Listener\NightClubGirlRestListener'
        ]
    ],
    'attach-resource-listeners' => [
        'Strapieno\NightClubCover\Api\V1\Rest\Controller' => [
            'Strapieno\NightClubGirlAvatar\Api\Listener\NightClubGirlRestListener'
        ]
    ],
    'controllers' => [
        'delegators' => [
            'Strapieno\NightClubCover\Api\V1\Rest\Controller' => [
                'Strapieno\Utils\Delegator\AttachRestResourceListenerDelegator',
            ]
        ],
    ],
    'router' => [
        'routes' => [
            'api-rest' => [
                'child_routes' => [
                    'nightclub' => [
                        'child_routes' => [
                            'girl' => [
                                'child_routes' => [
                                    'avatar' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/avatar',
                                            'defaults' => [
                                                'controller' => 'Strapieno\NightClubGirlAvatar\Api\V1\Rest\Controller'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]

                    ]
                ]
            ]
        ]
    ],
    'imgman-apigility' => [
        'imgman-connected' => [
            'Strapieno\NightClubGirlAvatar\Api\V1\Rest\ConnectedResource' => [
                'service' => 'ImgMan\Service\NightClubGirlAvatar'
            ],
        ],
    ],
    'zf-rest' => [
        'Strapieno\NightClubGirlAvatar\Api\V1\Rest\Controller' => [
            'service_name' => 'nightclub-girl-avatar',
            'listener' => 'Strapieno\NightClubGirlAvatar\Api\V1\Rest\ConnectedResource',
            'route_name' => 'api-rest/nightclub/girl/avatar',
            'route_identifier_name' => 'girl_id',
            'entity_http_methods' => [
                0 => 'GET',
                2 => 'PUT',
                3 => 'DELETE'
            ],
            'page_size' => 10,
            'page_size_param' => 'page_size',
            'collection_class' => 'Zend\Paginator\Paginator',
            'entity_class' => 'Strapieno\NightClubGirlAvatar\Model\Entity\NightClubGirlAvatarEntity'
        ]
    ],
    'zf-content-negotiation' => [
        'accept_whitelist' => [
            'Strapieno\NightClubGirlAvatar\Api\V1\Rest\Controller' => [
                'application/hal+json',
                'application/json'
            ],
        ],
        'content_type_whitelist' => [
            'Strapieno\NightClubGirlAvatar\Api\V1\Rest\Controller' => [
                'application/json',
                'multipart/form-data',
            ],
        ],
    ],
    'zf-hal' => [
        // map each class (by name) to their metadata mappings
        'metadata_map' => [
            'Strapieno\NightClubGirlAvatar\Model\Entity\AvatarEntity' => [
                'entity_identifier_name' => 'id',
                'route_name' => 'api-rest/nightclub/girl/avatar',
                'route_identifier_name' => 'girl_id',
                'hydrator' => 'Zend\Stdlib\Hydrator\ClassMethods',
            ],
        ],
    ],
    'zf-content-validation' => [
        'Strapieno\NightClubGirlAvatar\Api\V1\Rest\Controller' => [
            'input_filter' => 'NightClubGirlAvatarInputFilter',
        ],
    ],
    'strapieno_input_filter_specs' => [
        'NightClubGirlAvatarInputFilter' => [
            [
                'name' => 'blob',
                'required' => true,
                'allow_empty' => false,
                'continue_if_empty' => false,
                'validators' => [
                    0 => [
                        'name' => 'fileuploadfile',
                        'break_chain_on_failure' => true,
                    ],
                    1 => [
                        'name' => 'filesize',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'min' => '20KB',
                            'max' => '8MB',
                        ],
                    ],
                    2 => [
                        'name' => 'filemimetype',
                        'options' => [
                            'mimeType' => [
                                'image/png',
                                'image/jpeg',
                            ],
                            'magicFile' => false,
                        ],
                    ],
                ],
            ],
        ],
    ],
];

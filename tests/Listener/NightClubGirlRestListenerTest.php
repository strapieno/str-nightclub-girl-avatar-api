<?php
namespace Strapieno\NightClubGirlAvatar\ApiTest\Listener;

use ImgMan\Apigility\Entity\ImageEntity;
use Strapieno\NightClubCover\Api\Listener\NightClubRestListener;
use Strapieno\NightClubGirlAvatar\Api\Listener\NightClubGirlRestListener;
use Strapieno\UserAvatar\Api\Listener\UserRestListener;
use Zend\EventManager\EventManager;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\Application;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceManager;
use Zend\Uri\Http;
use ZF\Rest\ResourceEvent;

/**
 * Class NightClubGirlRestListenerTest
 */
class NightClubGirlRestListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $routeConfig = [
        'routes' => [
            'api-rest' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/api-rest',
                ],
                'child_routes' => [
                    'nightclub' => [
                        'type' => 'Segment',
                        'options' => [
                            'route' => '/nightclub[/:nightclub_id]',
                        ],
                        'child_routes' => [
                            'girl' => [
                                'type' => 'Segment',
                                'options' => [
                                    'route' => '/girl[/:girl_id]',
                                ],
                                'child_routes' => [
                                    'avatar' => [
                                        'type' => 'Segment',
                                        'options' => [
                                            'route' => '/avatar'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    public function testAttach()
    {
        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $listener = new NightClubGirlRestListener();
        $this->assertNull($listener->attach($eventManager));
    }

    public function testOnPostUpdate()
    {
        $listener = new NightClubGirlRestListener();

        $resource = new ResourceEvent();
        $resource->setParam('id', 'test');
        $imageService = new  ImageEntity();
        $resource->setParam('image', $imageService);

        /** @var $route TreeRouteStack */
        $route = TreeRouteStack::factory($this->routeConfig);
        $route->setRequestUri(new Http('www.test.com'));

        $routerMatch = new RouteMatch(['nightclub_id' => 'test']);

        $mvcEvent = $this->getMockBuilder('Zend\Mvc\MvcEvent')
            ->disableOriginalConstructor()
            ->setMethods(['getRouteMatch'])
            ->getMock();

        $mvcEvent->method('getRouteMatch')
            ->willReturn($routerMatch);

        $application = $this->getMockBuilder('Zend\Mvc\Application')
            ->disableOriginalConstructor()
            ->setMethods(['getMvcEvent'])
            ->getMock();

        $application->method('getMvcEvent')
            ->willReturn($mvcEvent);

        $sm = new ServiceManager();
        $sm->setService('Router', $route);
        $sm->setService('Application', $application);

        $abstractLocator = new PluginManager();
        $abstractLocator->setServiceLocator($sm);

        $image = $this->getMockBuilder('Strapieno\NightClubGirlAvatar\ApiTest\Asset\Image')
            ->getMock();

        $resultSet = $this->getMockBuilder('Matryoshka\Model\ResultSet\HydratingResultSet')
            ->setMethods(['current'])
            ->getMock();

        $resultSet->method('current')
            ->willReturn($image);

        $model = $this->getMockBuilder('Strapieno\NightClubGirl\Model\GirlModelService')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $model->method('find')
            ->willReturn($resultSet);

        $listener->setNightClubGirlModelService($model);
        $listener->setServiceLocator($abstractLocator);
        $this->assertSame($listener->onPostUpdate($resource), $imageService);

        $imageService->setSrc('test');
        $this->assertSame($listener->onPostUpdate($resource), $imageService);
    }

    public function testOnDeleteUpdate()
    {
        $listener = new NightClubGirlRestListener();

        $resource = new ResourceEvent();
        $resource->setParam('id', 'test');
        $imageService = new  ImageEntity();
        $imageService->setId('test');

        $sm = new ServiceManager();
        $abstractLocator = new PluginManager();
        $abstractLocator->setServiceLocator($sm);


        $image = $this->getMockBuilder('Strapieno\NightClubGirlAvatar\ApiTest\Asset\Image')
            ->getMock();

        $resultSet = $this->getMockBuilder('Matryoshka\Model\ResultSet\HydratingResultSet')
            ->setMethods(['current'])
            ->getMock();

        $resultSet->method('current')
            ->willReturn($image);

        $model = $this->getMockBuilder('Strapieno\NightClubGirl\Model\GirlModelService')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $model->method('find')
            ->willReturn($resultSet);

        $listener->setNightClubGirlModelService($model);
        $listener->setServiceLocator($abstractLocator);
        $this->assertTrue($listener->onPostDelete($resource));
    }
}
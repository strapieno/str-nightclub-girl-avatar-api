<?php
namespace Strapieno\NightClubGirlAvatar\Api\Listener;

use ImgMan\Image\SrcAwareInterface;
use Matryoshka\Model\Object\ActiveRecord\ActiveRecordInterface;
use Matryoshka\Model\Object\IdentityAwareInterface;
use Matryoshka\Model\Wrapper\Mongo\Criteria\ActiveRecord\ActiveRecordCriteria;
use Strapieno\NightClubGirl\Model\GirlModelAwareInterface;
use Strapieno\NightClubGirl\Model\GirlModelAwareTrait;
use Strapieno\NightClubGirlAvatar\Model\Entity\AvatarAwareInterface;
use Strapieno\User\Model\Entity\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\Router\Http\RouteInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class NightClubRestListener
 */
class NightClubGirlRestListener implements ListenerAggregateInterface,
    ServiceLocatorAwareInterface,
    GirlModelAwareInterface
{
    use ListenerAggregateTrait;
    use ServiceLocatorAwareTrait;
    use GirlModelAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('update', [$this, 'onPostUpdate']);
        $this->listeners[] = $events->attach('delete', [$this, 'onPostDelete']);
    }

    /**
     * @param Event $e
     * @return mixed
     */
    public function onPostUpdate(Event $e)
    {
        $serviceLocator = $this->getServiceLocator();
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        $id  = $e->getParam('id');
        $girl = $this->getGirlFromId($id);
        $image = $e->getParam('image');

        if ($girl instanceof AvatarAwareInterface && $girl instanceof ActiveRecordInterface) {

            $girl->setAvatar($this->getUrlFromImage($image, $serviceLocator));
            $girl->save();
        }
        return $image;
    }

    /**
     * @param Event $e
     * @return bool
     */
    public function onPostDelete(Event $e)
    {

        $id  = $e->getParam('id');
        $girl = $this->getGirlFromId($id);

        if ($girl instanceof AvatarAwareInterface && $girl instanceof ActiveRecordInterface) {

            $girl->setAvatar(null);
            $girl->save();
        }
        return true;
    }

    /**
     * @param $id
     * @return UserInterface|null
     */
    protected function getGirlFromId($id)
    {
        return $this->getNightClubGirlModelService()->find((new ActiveRecordCriteria())->setId($id))->current();

    }

    /**
     * @param IdentityAwareInterface $image
     * @param $serviceLocator
     * @return string
     */
    protected function getUrlFromImage(IdentityAwareInterface $image, ServiceLocatorInterface $serviceLocator)
    {
        $now = new \DateTime();
        if ($image instanceof SrcAwareInterface && $image->getSrc()) {

            return $image->getSrc(). '?lastUpdate=' . $now->getTimestamp();
        }

        /** @var $router RouteInterface */
        $router = $serviceLocator->get('Router');
        $url = $router->assemble(
            [
                'nightclub_id' => $serviceLocator->get('Application')->getMvcEvent()->getRouteMatch()->getParam('nightclub_id'),
                'girl_id' => $image->getId()
            ],
            ['name' => 'api-rest/nightclub/girl/avatar', 'force_canonical' => true]
        );


        return $url . '?lastUpdate=' . $now->getTimestamp();
    }
}
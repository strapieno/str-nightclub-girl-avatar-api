<?php
namespace Strapieno\NightClubGirlAvatar\Api\Listener;

use Matryoshka\Model\Object\ActiveRecord\ActiveRecordInterface;
use Matryoshka\Model\Wrapper\Mongo\Criteria\ActiveRecord\ActiveRecordCriteria;
use Strapieno\NightClub\Model\NightClubModelAwareInterface;
use Strapieno\NightClub\Model\NightClubModelAwareTrait;
use Strapieno\NightClubCover\Model\Entity\NightClubCoverAwareInterface;
use Strapieno\NightClubGirl\Model\GirlModelAwareInterface;
use Strapieno\NightClubGirl\Model\GirlModelAwareTrait;
use Strapieno\NightClubGirlAvatar\Model\AvatarAwareInterface;
use Strapieno\NightClubGirlAvatar\Model\AvatarAwareTrait;
use Strapieno\User\Model\Entity\UserInterface;
use Strapieno\User\Model\UserModelAwareInterface;
use Strapieno\User\Model\UserModelAwareTrait;
use Strapieno\UserAvatar\Model\Entity\UserAvatarAwareInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class NightClubRestListener
 */
class NightClubRestListener implements ListenerAggregateInterface,
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
        $this->listeners[] = $events->attach('update.post', [$this, 'onPostUpdate']);
        $this->listeners[] = $events->attach('delete.post', [$this, 'onPostDelete']);
    }

    /**
     * @param Event $e
     */
    public function onPostUpdate(Event $e)
    {
        $serviceLocator = $this->getServiceLocator();
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        $id  = $e->getParam('id');
        $girl = $this->getGirlFromId($id);

        if ($girl instanceof AvatarAwareInterface && $girl instanceof ActiveRecordInterface) {

            /** @var $router RouteInterface */
            $router = $serviceLocator->get('Router');
            $url = $router->assemble(
                ['nightclub_id' => $id],
                ['name' => 'api-rest/nightclub/cover', 'force_canonical' => true]
            );

            $now = new \DateTime();
            $girl->setAvatar($url . '?lastUpdate=' . $now->getTimestamp());
            $girl->save();
        }
    }

    /**
     * @param Event $e
     */
    public function onPostDelete(Event $e)
    {

        $id  = $e->getParam('id');
        $girl = $this->getGirlFromId($id);

        if ($girl instanceof AvatarAwareInterface && $girl instanceof ActiveRecordInterface) {

            $girl->setAvatar(null);
            $girl->save();
        }
    }

    /**
     * @param $id
     * @return UserInterface|null
     */
    protected function getGirlFromId($id)
    {
        return $this->getNightClubGirlModelService()->find((new ActiveRecordCriteria())->setId($id))->current();

    }
}
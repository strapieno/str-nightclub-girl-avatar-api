<?php
namespace Strapieno\NightClubGirlAvatar\ApiTest\Asset;

use Matryoshka\Model\Object\ActiveRecord\ActiveRecordInterface;
use Strapieno\NightClubGirlAvatar\Model\Entity\AvatarAwareInterface;
use Strapieno\NightClubGirlAvatar\Model\Entity\AvatarAwareTrait;

/**
 * Class Image
 */
class Image implements AvatarAwareInterface, ActiveRecordInterface
{
    use AvatarAwareTrait;

    public function save()
    {
        // TODO: Implement save() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function setId($id)
    {
        // TODO: Implement setId() method.
    }

    public function getId()
    {
        // TODO: Implement getId() method.
    }
}
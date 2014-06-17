<?php
namespace Barany\Plaid\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Account implements Exportable {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Institution")
     * @var Institution
     */
    private $institution;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="accounts")
     * @var User
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $accessToken;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return Institution
     */
    public function getInstitution() {
        return $this->institution;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getAccessToken() {
        return $this->accessToken;
    }

    /**
     * @return array
     */
    public function toApi()
    {
        return [
            'id' => $this->getId(),
            'institution' => $this->getInstitution()->toApi(),
        ];
    }
}
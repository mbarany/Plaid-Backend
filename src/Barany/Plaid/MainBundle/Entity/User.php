<?php
namespace Barany\Plaid\MainBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity @ORM\Table(indexes={
 *  @ORM\Index(columns={"email"})
 * })
 */
class User implements Exportable {
    /**
     * @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="Account", mappedBy="user")
     * @var ArrayCollection
     */
    private $accounts;

    /**
     * @ORM\OneToMany(targetEntity="ApiToken", mappedBy="user")
     * @var ArrayCollection
     */
    private $apiTokens;

    public function __construct() {
        $this->accounts = new ArrayCollection();
        $this->apiTokens = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return ArrayCollection
     */
    public function getAccounts() {
        return $this->accounts;
    }

    /**
     * @return ArrayCollection
     */
    public function getApiTokens() {
        return $this->apiTokens;
    }

    /**
     * @return array
     */
    public function toApi()
    {
        return [
            'id' => $this->id,
        ];
    }
}
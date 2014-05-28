<?php
namespace Barany\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity @Table
 */
class User implements Exportable {
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="string", length=255)
     * @var string
     */
    private $email;

    /**
     * @OneToMany(targetEntity="Account", mappedBy="user")
     * @var ArrayCollection
     */
    private $accounts;

    public function __construct() {
        $this->accounts = new ArrayCollection();
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
     * @return array
     */
    public function toApi()
    {
        return [
            'id' => $this->id,
        ];
    }
}
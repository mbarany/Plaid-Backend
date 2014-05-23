<?php
namespace Barany\Model;

/**
 * @Entity @Table
 */
class Account implements Exportable {
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Institution")
     * @var Institution
     */
    private $institution;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="accounts")
     * @var User
     */
    private $user;

    /**
     * @Column(type="text")
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
            'name' => $this->getInstitution()->getName(),
            'code' => $this->getInstitution()->getCode(),
        ];
    }
}
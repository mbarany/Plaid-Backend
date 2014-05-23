<?php
namespace Barany\Model;

/**
 * @Entity @Table(indexes={
 *  @index(name="name_idx", columns={"name"})
 * })
 */
class Institution implements Exportable {
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * @Column(type="string", length=255)
     * @var string
     */
    private $code;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return array
     */
    public function toApi() {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'code' => $this->getCode(),
        ];
    }
} 
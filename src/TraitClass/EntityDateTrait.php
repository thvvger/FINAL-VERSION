<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN Rogelio
 * Date: 02/01/2020
 * Time: 09:31
 */

namespace App\TraitClass;


trait EntityDateTrait
{

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt ;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=true)
     */
    private $deleted;


    public function __construct()
    {
        $this->createdAt  =  new \DateTime();
        $this->deleted  =  false;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     *
     * @return EntityDateTrait
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     *
     * @return EntityDateTrait
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @return bool
     */
    public function isUpdatedAt()
    {
        return !!$this->updatedAt;
    }

    /**
     * @param bool $updatedAt
     */
    public function setUpdatedAt(bool $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
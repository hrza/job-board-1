<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Token
{
    /**
     * @ORM\Column(type="guid")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     *
     * @var array
     */
    private $roles = ['ROLE_MODERATOR'];

    /**
     * @ORM\Column(type="boolean", nullable=false)
     *
     * @var string
     */
    private $used = false;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Job")
     *
     * @var Job
     */
    private $job;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param Job $job
     */
    public function setJob($job)
    {
        $this->job = $job;
    }

    public function __toString()
    {
        return $this->getId();
    }

    /**
     * @return $this
     */
    public function expire()
    {
        $this->used = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function renew()
    {
        $this->used = false;

        return $this;
    }
}

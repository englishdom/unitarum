<?php

namespace UnitarumExample\Entity;

/**
 * Class User
 * @package Example\Entity
 */
class User
{
    protected $id;
    protected $name;
    protected $email;
    protected $md5Hash;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getMd5Hash()
    {
        return $this->md5Hash;
    }

    /**
     * @param mixed $md5Hash
     */
    public function setMd5Hash($md5Hash): void
    {
        $this->md5Hash = $md5Hash;
    }
}

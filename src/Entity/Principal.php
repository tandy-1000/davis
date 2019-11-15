<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="principals")
 * @ORM\Entity(repositoryClass="App\Repository\PrincipalRepository")
 * @UniqueEntity("uri")
 */
class Principal
{
    const PREFIX = 'principals/';

    const READ_PROXY_SUFFIX = '/calendar-proxy-read';
    const WRITE_PROXY_SUFFIX = '/calendar-proxy-write';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="binary", length=255, unique=true)
     * @Assert\NotBlank
     * @Assert\Unique
     */
    private $uri;

    /**
     * @ORM\Column(type="binary", length=255, nullable=true)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(name="displayname", type="string", length=255, nullable=true)
     */
    private $displayName;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotBlank
     */
    private $isMain;

    /**
     * @ORM\ManyToMany(targetEntity="Principal")
     * @ORM\JoinTable(
     *  name="groupmembers",
     *  joinColumns={
     *      @ORM\JoinColumn(name="principal_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     *  }
     * )
     */
    private $delegees;
    // TODO add inverse correctly for delegees ?

    public function __construct()
    {
        $this->delegees = new ArrayCollection();
        $this->isMain = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUri(): ?string
    {
        if (is_resource($this->uri)) {
            $this->uri = stream_get_contents($this->uri);
        }

        return $this->uri;
    }

    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getUsername(): ?string
    {
        return str_replace(self::PREFIX, '', $this->getUri());
    }

    public function getEmail(): ?string
    {
        if (is_resource($this->email)) {
            $this->email = stream_get_contents($this->email);
        }

        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return Collection|Principal[]
     */
    public function getDelegees(): Collection
    {
        return $this->delegees;
    }

    public function addDelegee(Principal $delegee): self
    {
        if (!$this->delegees->contains($delegee)) {
            $this->delegees[] = $delegee;
        }

        return $this;
    }

    public function removeDelegee(Principal $delegee): self
    {
        if ($this->delegees->contains($delegee)) {
            $this->delegees->removeElement($delegee);
        }

        return $this;
    }

    public function removeAllDelegees(): self
    {
        $this->delegees->clear();

        return $this;
    }

    public function getIsMain(): ?bool
    {
        return $this->isMain;
    }

    public function setIsMain(bool $isMain): self
    {
        $this->isMain = $isMain;

        return $this;
    }
}

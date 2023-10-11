<?php

namespace Onisep\IbexaCustomSettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Onisep\IbexaCustomSettingsBundle\Repository\LocationSettingRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LocationSettingRepository::class)
 * @ORM\Table(name="ibexa_custom_settings")
 */
class LocationSetting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="location_id", type="integer", nullable=false)
     */
    private ?int $locationId = null;

    /**
     * @var string
     *
     * @ORM\Column(name="setting_key", type="string", length=300, nullable=false)
     *
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    private string $key;

    /**
     * @var string|null
     *
     * @ORM\Column(name="setting_value", type="text")
     *
     * @Assert\NotBlank
     * @Assert\Length(min=3)
     */
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocationId(): ?int
    {
        return $this->locationId;
    }

    public function setLocationId(int $locationId): self
    {
        $this->locationId = $locationId;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $slugger = new AsciiSlugger('fr');

        $this->key = $slugger->slug($key)->toString();

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}

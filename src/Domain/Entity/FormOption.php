<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Letkode\FormSchemaBundle\Infrastructure\Doctrine\Repository\FormOptionRepository;
use Letkode\FormSchemaBundle\Infrastructure\Doctrine\Trait\HasParametersTrait;
use Letkode\FormSchemaBundle\Infrastructure\Doctrine\Trait\HasTimestampsTrait;
use Letkode\FormSchemaBundle\Infrastructure\Doctrine\Trait\HasTranslationsTrait;
use Letkode\FormSchemaBundle\Infrastructure\Doctrine\Trait\HasUuidTrait;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: FormOptionRepository::class)]
#[ORM\Table(name: 'form_option')]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false)]
class FormOption
{
    use HasParametersTrait;
    use HasTimestampsTrait;
    use HasTranslationsTrait;
    use HasUuidTrait;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int|null $id = null;

    #[ORM\Column(type: Types::STRING, length: 100, unique: true)]
    public private(set) string $tag;

    #[ORM\Column(type: Types::STRING)]
    private string $rawName;

    public string $name {
        get => $this->rawName;
        set(string $value) => $this->rawName = trim($value);
    }

    #[ORM\OneToMany(targetEntity: FormOptionValue::class, mappedBy: 'group', cascade: ['persist'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    public private(set) Collection $values;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    public private(set) string|null $seedChecksum = null;

    public function __construct()
    {
        $this->uuid = new UuidV7();
        $this->values = new ArrayCollection();
    }

    public function setTag(string $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    public function updateSeedChecksum(string $checksum): static
    {
        $this->seedChecksum = $checksum;

        return $this;
    }

    public function addValue(FormOptionValue $value): static
    {
        if (!$this->values->contains($value)) {
            $this->values->add($value);
            $value->setGroup($this);
        }

        return $this;
    }
}

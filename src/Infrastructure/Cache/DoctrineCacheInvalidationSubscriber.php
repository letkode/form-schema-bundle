<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Infrastructure\Cache;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Letkode\FormSchemaBundle\Domain\Entity\Form;
use Letkode\FormSchemaBundle\Domain\Entity\FormField;
use Letkode\FormSchemaBundle\Domain\Entity\FormGroup;
use Letkode\FormSchemaBundle\Domain\Entity\FormOption;
use Letkode\FormSchemaBundle\Domain\Entity\FormOptionValue;
use Letkode\FormSchemaBundle\Domain\Entity\FormSection;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Events::postUpdate, method: 'postUpdate')]
#[AsEventListener(event: Events::postPersist, method: 'postPersist')]
#[AsEventListener(event: Events::postRemove, method: 'postRemove')]
final class DoctrineCacheInvalidationSubscriber
{
    private static array $watchedEntities = [
        Form::class,
        FormSection::class,
        FormGroup::class,
        FormField::class,
        FormOption::class,
        FormOptionValue::class,
    ];

    public function __construct(
        private readonly FormSchemaCacheInvalidator $invalidator,
    ) {
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->handleEvent($args->getObject());
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->handleEvent($args->getObject());
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $this->handleEvent($args->getObject());
    }

    private function handleEvent(object $entity): void
    {
        if (!array_any(self::$watchedEntities, static fn ($class) => $entity instanceof $class)) {
            return;
        }

        $tag = $this->resolveFormTag($entity);
        if (null !== $tag) {
            $this->invalidator->invalidate($tag);
        }
    }

    private function resolveFormTag(object $entity): string|null
    {
        return match (true) {
            $entity instanceof Form => $entity->tag,
            $entity instanceof FormSection => $entity->form?->tag,
            $entity instanceof FormGroup => $entity->section?->form?->tag,
            $entity instanceof FormField => $entity->group?->section?->form?->tag,
            $entity instanceof FormOption => $entity->tag,
            $entity instanceof FormOptionValue => $entity->group?->tag,
            default => null,
        };
    }
}

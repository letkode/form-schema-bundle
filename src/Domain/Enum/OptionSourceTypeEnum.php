<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Domain\Enum;

enum OptionSourceTypeEnum: string
{
    case General = 'general';
    case Entity = 'entity';
}

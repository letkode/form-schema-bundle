<?php

declare(strict_types=1);

namespace Letkode\FormSchemaBundle\Seeder\Enum;

enum SeedStatus: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Skipped = 'skipped';
    case Error   = 'error';
}

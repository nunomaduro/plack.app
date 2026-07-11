<?php

declare(strict_types=1);

namespace App\Enums;

enum WorkspaceType: string
{
    case Private = 'private';
    case Public = 'public';
}

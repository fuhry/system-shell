<?php
declare(strict_types=1);

namespace fuhry\System;

interface IShell
{
    public function execf(
        string $command,
        string $argTemplate = '',
        ...$args
    );
}
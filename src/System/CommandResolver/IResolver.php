<?php
declare(strict_types=1);
namespace fuhry\System\CommandResolver;

/**
 * Interface for resolving commands to complete filesystem paths.
 *
 * @author Dan Fuhry <dan@fuhry.com>
 */
interface IResolver
{
    /**
     * Resolve a command to its full filesystem path.
     *
     * @param string
     *   Command, possibly without path
     * @return string
     *   Absolute path
     */
    public function resolveCommand(string $command): string;
}
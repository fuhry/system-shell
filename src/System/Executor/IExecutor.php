<?php
declare(strict_types=1);

namespace fuhry\System\Executor;

/**
 * Interface for classes which carry out command execution and store and return
 * results.
 *
 * @author Dan Fuhry <dan@fuhry.com>
 */
interface IExecutor
{
    /**
     * Execute the command.
     *
     * @return void
     * @throws \LogicException
     *   Implementations should track whether the command has already been
     *   executed, and throw this exception if an attempt is made to run the
     *   command more than once.
     */
    public function execute(string $cmdline): ExecuteResult;
}
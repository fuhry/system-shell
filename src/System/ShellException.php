<?php
declare(strict_types=1);

namespace fuhry\System;

/**
 * Exception thrown when shell commands fail.
 */
class ShellException extends \RuntimeException
{
    private $executeResult;

    /**
     * Track the result of command execution, including exit status, standard
     * output, and standard error.
     *
     * @param Executor\ExecuteResult
     */
    public function setExecutionResult(Executor\ExecuteResult $result): void
    {
        $this->executeResult = $result;
    }

    /**
     * Get the execution result.
     *
     * @return Executor\ExecuteResult
     */
    public function getExecutionResult(): Executor\ExecuteResult
    {
        return $this->executeResult;
    }
}
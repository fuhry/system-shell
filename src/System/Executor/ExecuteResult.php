<?php
declare(strict_types=1);

namespace fuhry\System\Executor;

/**
 * Result of a command execution
 *
 * @author Dan Fuhry <dan@fuhry.com>
 */
class ExecuteResult
{
    /** @var int */
    private $exitStatus;

    /** @var string */
    private $stdout;

    /** @var string */
    private $stderr;

    /**
     * Constructor.
     *
     * @param int
     *   Exit status
     * @param string
     *   Standard output
     * @param string
     *   Standard error
     */
    public function __construct(
        int $exitStatus,
        string $stdout,
        string $stderr
    ) {
        $this->exitStatus = $exitStatus;
        $this->stdout = $stdout;
        $this->stderr = $stderr;
    }

    /**
     * Return the exit status of the command.
     *
     * @return int
     */
    public function getExitStatus(): int
    {
        return $this->exitStatus;
    }

    /**
     * Return the contents of standard output.
     *
     * @return string
     */
    public function getStandardOutput(): string
    {
        return $this->stdout;
    }

    /**
     * Return the contents of standard error.
     *
     * @return string
     */
    public function getStandardError(): string
    {
        return $this->stderr;
    }
}
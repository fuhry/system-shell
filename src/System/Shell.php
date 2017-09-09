<?php
declare(strict_types=1);
namespace fuhry\System;
use Psr\Log;

/**
 * Class for executing shell commands, wrapping string arguments in
 * escapeshellarg() automatically.
 *
 * @author Dan Fuhry <dan@fuhry.com>
 */
class Shell implements
    IShell,
    Log\LoggerAwareInterface
{
    use Log\LoggerAwareTrait;

    /** @const int */
    const EXIT_SUCCESS = 0;

    /** @var Resolver\IResolver */
    private $resolver;

    /** @var Executor\IExecutor */
    private $executor;

    /** @var bool */
    private $exceptionOnErrorExit = false;

    /**
     * Constructor.
     *
     * All arguments are optional with reasonable defaults.
     *
     * @param fuhry\System\CommandResolver\IResolver
     *   Resolver for commands. Resolvers convert a relative or PATH command
     *   into an absolute path.
     * @param fuhry\System\Executor\IExecutor
     *   Execution backend. Backends perform the actual command execution.
     */
    public function __construct(
        ?Log\LoggerInterface $logger = null,
        ?CommandResolver\IResolver $resolver = null,
        ?Executor\IExecutor $executor = null
    ) {
        $this->logger = $logger ?: new Log\NullLogger();
        $this->resolver = $resolver ?: new CommandResolver\PathEnvironmentResolver();
        $this->executor = $executor ?: new Executor\ProcOpenExecutor();

        if ($this->resolver instanceof Psr\LoggerAwareInterface) {
            $this->resolver->setLogger($this->logger);
        }

        if ($this->executor instanceof Psr\LoggerAwareInterface) {
            $this->executor->setLogger($this->executor);
        }
    }

    /**
     * Variadic version of vexecf().
     *
     * @param string
     *   Base command - no arguments
     * @param string
     *   printf-style format string for arguments. No user data should be
     *   present in this argument.
     * @param ...mixed
     *   printf arguments (variadic)
     */
    public function execf(
        string $command,
        string $argTemplate = '',
        ...$args
    ) {
        return $this->vexecf($command, $argTemplate, $args);
    }

    /**
     * Execute a command using printf-style formatting.
     *
     * String arguments are passed through escapeshellarg() prior to being
     * assembled into the final command.
     *
     * @param string
     *   Base command - no arguments
     * @param string
     *   printf-style format string for arguments. No user data should be
     *   present in this argument.
     * @param array
     *   printf arguments
     * @return Executor\ExecuteResult
     * @throws \RuntimeException
     *   If $command cannot be resolved to a usable executable, the resolver
     *   will throw this.
     * @throws \InvalidArgumentException
     *   This will be thrown if any of the values of $args are not scalar.
     * @throws ShellException
     *   If the command exits with a nonzero status and
     *   $this->exceptionOnErrorExit is set, this is thrown. Exit status and the
     *   contents of standard output and standard error will be available
     *   through the Executor\ExecuteResult returned by getExecutionResult.
     */
    public function vexecf(
        string $command,
        string $argTemplate = '',
        array $args
    ): Executor\ExecuteResult {
        $commandTemplate = "%s {$argTemplate}";
        $fullCommand = $this->resolver->resolveCommand($command);
        array_unshift($args, $fullCommand);

        foreach ($args as &$arg) {
            if (!is_scalar($arg)) {
                throw new \InvalidArgumentException(
                    "Arguments to execf() must be scalar"
                );
            }

            if (!is_string($arg)) {
                $arg = strval($arg);
            }

            $arg = escapeshellarg($arg);
        }
        unset($arg);

        $cmdline = vsprintf($commandTemplate, $args);

        $start = microtime(true);
        $result = $this->executor->execute($cmdline);
        $time = microtime(true) - $start;

        $this->logger->info(
            sprintf(
                "Command \"%s\" executed in %.3f seconds with status %d",
                $cmdline,
                $time,
                $result->getExitStatus()
            )
        );

        if ($this->exceptionOnErrorExit &&
            $result->getExitStatus() !== self::EXIT_SUCCESS
        ) {
            $exception = new ShellException(
                "Command \"$fullCommand\" failed with status {$result->getExitStatus()}."
            );
            $exception->setExecutionResult($result);

            throw $exception;
        }

        return $result;
    }

    /**
     * Set whether error exits result in automatic exception throwing.
     *
     * @param bool
     *   If true, any command that exits with a nonzero status will result in a
     *   ShellException being thrown by execf().
     * @return $this
     */
    public function throwsExceptionOnErrorExit(bool $flag): IShell
    {
        $this->exceptionOnErrorExit = $flag;

        return $this;
    }
}

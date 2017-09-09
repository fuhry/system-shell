<?php
declare(strict_types=1);

namespace fuhry\System\Executor;

use Psr\Log;

/**
 * Implementation of IExecutor using proc_open().
 *
 * @see https://www.php.net/manual/en/function.exec.php
 * @author Dan Fuhry <dan@fuhry.com>
 */
class ProcOpenExecutor implements
    IExecutor,
    Log\LoggerAwareInterface
{
    use Log\LoggerAwareTrait;

    /**
     * @inherit
     */
    public function execute(string $cmdline): ExecuteResult
    {
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmdline, $descriptorspec, $pipes);

        if (!is_resource($process)) {
            throw new \RuntimeException(
                "Error opening process \"{$this->cmdline}\" with proc_open()"
            );
        }

        // close standard input
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $statusCode = proc_close($process);

        return new ExecuteResult($statusCode, $stdout, $stderr);
    }
}

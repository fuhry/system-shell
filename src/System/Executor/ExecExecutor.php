<?php
declare(strict_types=1);

namespace fuhry\System\Executor;

use Psr\Log;

/**
 * Implementation of IExecutor using exec().
 *
 * @see https://www.php.net/manual/en/function.exec.php
 * @author Dan Fuhry <dan@fuhry.com>
 */
class ExecExecutor implements
    IExecutor,
    Log\LoggerAwareInterface
{
    use Log\LoggerAwareTrait;

    /**
     * @inherit
     */
    public function execute(string $cmdline): ExecuteResult
    {
        $stderrPath = tempnam(sys_get_temp_dir(), "exec");
        $stdoutPath = tempnam(sys_get_temp_dir(), "exec");
        $cmdline = "{$cmdline} 2>$stderrPath >$stdoutPath";

        exec($cmdline, $unused, $statusCode);

        $stdout = file_get_contents($stdoutPath);
        unlink($stdoutPath);

        $stderr = file_get_contents($stderrPath);
        unlink($stderrPath);

        $statusCode = $statusCode;

        return new ExecuteResult($statusCode, $stdout, $stderr);
    }
}
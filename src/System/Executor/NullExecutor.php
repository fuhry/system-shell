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
class NullExecutor implements
    IExecutor,
    Log\LoggerAwareInterface
{
    use Log\LoggerAwareTrait;

    /**
     * @inherit
     */
    public function execute(string $cmdline): ExecuteResult
    {
        $statusCode = $cmdline === 'false' ? 1 : 0;

        return new ExecuteResult($statusCode, '', '');
    }
}

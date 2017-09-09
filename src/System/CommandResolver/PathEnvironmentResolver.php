<?php
declare(strict_types=1);
namespace fuhry\System\CommandResolver;
use Psr\Log;

/**
 * Interface for resolving commands to complete filesystem paths.
 *
 * @author Dan Fuhry <dan@fuhry.com>
 */
class PathEnvironmentResolver implements IResolver
{
    use Log\LoggerAwareTrait;

    private static $osPath;

    /**
     * Resolve a command to its full filesystem path.
     *
     * @param string
     *   Command, possibly without path
     * @return string
     *   Absolute path
     * @throws \RuntimeException
     *   This exception is thrown if the command cannot be resolved to an
     *   executable.
     */
    public function resolveCommand(string $command): string
    {
        $osPath = self::getOsPath();

        if (strpos($command, DIRECTORY_SEPARATOR) === false) {
            // $command is a system executable
            foreach ($osPath as $directory) {
                $absolutePath = $directory . DIRECTORY_SEPARATOR . $command;
                // Verify that it's an executable file
                // XXX: Does is_executable() work on win32?
                if (is_file($absolutePath) && is_executable($absolutePath)) {
                    // We found it!
                    return $absolutePath;
                }
            }
        }
        else if (is_file($command) && is_executable($command)) {
            // $command is a relative or absolute path to an executable
            return realpath($command);
        }

        throw new \RuntimeException(
            "Unable to resolve the absolute path for the command \"$command\""
        );
    }

    /**
     * Determine the PATH.
     *
     * @return array
     */
    private static function getOsPath(): array
    {
        if (self::$osPath === null) {
            // Detect OS and use that to set the default PATH.
            switch (strtolower(PHP_OS)) {
                case 'win32':
                case 'winnt':
                    // For Windows, PATH directories are separated with a semicolon
                    $pathSeparator = ';';
                    $defaultPath = 'C:\\Windows\\System32;C:\\Windows';
                    $osPath = $defaultPath;
                    break;
                default:
                    // All other OSes separate using a colon
                    $pathSeparator = ':';
                    $defaultPath = '/usr/local/sbin:/usr/local/bin:' .
                                    '/usr/sbin:/usr/bin:' .
                                    '/sbin:/bin';
                    break;
            }

            // Retrieve the path from the system environment,
            $osPath = $defaultPath;
            foreach (array_merge($_SERVER, $_ENV) as $key => $value) {
                if (strtolower($key) === 'path') {
                    $osPath = $value;
                    break;
                }
            }
            self::$osPath = explode($pathSeparator, $osPath);
        }

        return self::$osPath;
    }
}
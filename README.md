# system-shell

OO interface to command execution in PHP. Encourages safer argument handling. This is a clean-room,
copyright-encumberment-free reimplementation of functionality I implemented in several projects with my employer.

## Features

### `execf()`/`vexecf()`: printf-style formatted commands

The original reason I wrote this class was to encourage myself and fellow engineers to obsessively escape any and all
command line arguments consisting of any sort of data - even semi-trusted data from elsewhere in the application.

To achieve this goal, the `execf()` method takes three arguments:

  * `$command`: The base command to execute, i.e. the executable to run. Example: `ls`
  * `$argTemplate`: printf-style template string for arguments. You can include switches in here, but should not include
    any user data. Placeholders for user data should not be quoted. Example: `-la %s`.
  * `...$args`: Arguments that will be passed to `sprintf()`. User data in here should not be quoted; all arguments will
    be cast to strings and quoted appropriately for the command line.

### PATH resolution

The `Shell` class uses an `IResolver` (defaulting to `PathEnvironmentResolver`) to calculate the full path of the
executable before it's run. If the system's `PATH` is empty, a reasonable default is used.

### Logging

The `Shell` class implements PSR-3 `LoggerAwareInterface`, so you can supply any logger you want to log command
execution and results.

### Separate stdout/stderr capture

The `ExecuteResult` instance returned from all commands has three methods:

  * `getExitStatus`: Returns the process's integer exit status
  * `getStandardOutput`: Returns the process's standard output as a string
  * `getStandardError`: Returns the process's standard error as a string

### Automatic exceptions

You can instruct `Shell` to automatically throw exceptions when commands fail:

```php
$shell->throwsExceptionOnErrorExit(true)->execf('false'); // throws ShellException
```

# TODO

  * Add support for decorators (sudo, timeout, others?)
  * Add ability to control logging behavior - enable/disable profiling, logging of arguments, etc.
  * Add support for supplying standard input

# License

```
THE MIT LICENSE

Copyright (c) 2017 Dan Fuhry

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

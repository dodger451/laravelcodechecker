<?php

namespace dodger451\LaravelCodeChecker;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class LaravelCodeChecker
{
    /**
     * PHPCS.
     */

    /**
     * @param array $targets
     * @return string
     */
    public function phpcsCheck(array $targets = []) : string
    {
        $config = config('laravelcodechecker');
        $command = $config['php-cli'].' '.$config['phpcs'].' '.$config['phpcs_standard'].' '.
            (count($targets) > 0 ? implode(' ', $targets) : $config['phpcs_target']);

        return $this->run($command);
    }

    /**
     * PHPCBF.
     * @param array $targets
     * @return string
     */
    public function phpcsFix(array $targets = []) : string
    {
        $config = config('laravelcodechecker');
        $command = $config['php-cli'].' '.$config['phpcbf']
            .' '.$config['phpcs_standard'].' '.(count($targets) > 0
                ? implode(' ', $targets) : $config['phpcs_target']);

        return $this->run($command);
    }

    /**
     * php -l.
     * @param array $targets
     * @return string
     */
    public function phpLint(array $targets = []) : string
    {
        $config = config('laravelcodechecker');
        $targets = count($targets) > 0 ? $targets : explode(' ', $config['phplint_target']);
        $count = 0;

        foreach ($targets as $target) {
            $this->runRecurseOnPhpFiles($target, function ($file) use ($config, &$count) {
                $command = $config['php-cli'].' -l '.' '.$file.' \;';
                $this->run($command);
                $count++;
            });
        }

        return sprintf('Checked %d files.', $count);
    }

    /**
     * phpmd.
     * @param array $targets
     * @return string
     */
    public function phpmd(array $targets = []) : string
    {
        $config = config('laravelcodechecker');
        $targets = count($targets) > 0 ? $targets : explode(' ', $config['phpmd_target']);
        $out = '';
        foreach ($targets as $target) {
            $command = $config['phpmd'].' '.$target.' text '.$config['phpmd_standard'].' \;';
            $out .= $this->run($command);
        }

        return $out;
    }

    /**
     * @param string $target
     * @param $callback
     */
    protected function runRecurseOnPhpFiles(string $target, callable $callback)
    {
        if (is_file($target) && preg_match('/^.*\.(php)$/i', $target)) {
            $callback($target);

            return;
        }
        if (is_dir($target)) {
            $it = new \RecursiveDirectoryIterator($target, \FilesystemIterator::SKIP_DOTS);
            foreach (new \RecursiveIteratorIterator($it) as $file) {
                if (is_file($file) && preg_match('/^.*\.(php)$/i', $file)) {
                    $callback($file);
                }
            }
        }
    }

    /**
     * @param string $command
     * @return string
     */
    protected function run(string $command) : string
    {
        $process = $this->newProcess($command);
        $process->run();

        if (! $process->isSuccessful()) {
            throw $this->newProcessFailedException($process);
        }

        return $process->getCommandLine().PHP_EOL.$process->getOutput();
    }

    /**
     * @param string $command
     * @return Process
     */
    protected function newProcess(string $command) : Process
    {
        return new Process($command);
    }

    /**
     * @param Process $process
     * @return ProcessFailedException
     */
    protected function newProcessFailedException(Process $process) : ProcessFailedException
    {
        return new ProcessFailedException($process);
    }
}

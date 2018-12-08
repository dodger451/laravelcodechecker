<?php

namespace dodger451\LaravelCodeChecker;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
/**
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
class LaravelCodeChecker
{
    /**
     * PHPCS.
     */
    public function phpcsCheck($targets)
    {
        $config = config('laravelcodechecker');
        $command = $config['php-cli'] . ' ' . $config['phpcs'] . ' ' . $config['phpcs_standard'] . ' ' .
            (count($targets) > 0 ? implode(' ', $targets) : $config['phpcs_target']);
        return $this->run($command);
    }

    /**
     * PHPCBF.
     */
    public function phpcsFix($targets)
    {
        $config = config('laravelcodechecker');
        $command = $config['php-cli'] . ' ' . $config['phpcbf']
            . ' ' . $config['phpcs_standard'] . ' ' . (count($targets) > 0
                ? implode(' ', $targets) : $config['phpcs_target']);
        return $this->run($command);
    }

    /**
     * php -l.
     */
    public function phpLint($targets)
    {
        $config = config('laravelcodechecker');
        $targets = count($targets) > 0 ? $targets : explode(' ', $config['phplint_target']);
        $count = 0;

        foreach ($targets as $target) {
            $this->runRecurseOnPhpFiles($target, function ($file) use ($config, &$count) {
                $command = $config['php-cli'] . ' -l ' . ' ' . $file . ' \;';
                $this->run($command);
                $count++;
            });
        }
        
        return sprintf('Checked %d files.', $count) ;
    }

    /**
     * phpmd.
     */
    public function phpmd($targets)
    {
        $config = config('laravelcodechecker');
        $targets = count($targets) > 0 ? $targets : explode(' ', $config['phpmd_target']);
        $out = '';
        foreach ($targets as $target) {
            $command = $config['phpmd'] . ' ' . $target . ' text ' . $config['phpmd_standard'] . ' \;';
            $out .= $this->run($command);
        }
        
        return $out;
    }

    protected function runRecurseOnPhpFiles($target, $callback)
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

    protected function run(string $command) : string
    {
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getCommandLine() . PHP_EOL . $process->getOutput();
    }
}

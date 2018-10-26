<?php

namespace dodger451\LaravelCodeChecker;

/**
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
class LaravelCodeChecker
{
    /**
     * PHPCS
     *
     */
    public function phpcsCheck($targets)
    {
        $config = config('laravelcodechecker');
        echo $config['php'] . ' ' . $config['phpcs'] . ' ' . $config['phpcs_standard'] . ' ' .
            (count($targets) > 0 ? implode(' ', $targets) : $config['phpcs_target']) . PHP_EOL;
        system(
            $config['php'] . ' ' . $config['phpcs'] . ' ' . $config['phpcs_standard'] . ' ' .
            (count($targets) > 0 ? implode(' ', $targets) : $config['phpcs_target']),
            $retval
        );
        if (0 != $retval) {
            exit(1);
        }
    }

    /**
     * PHPCBF
     *
     */
    public function phpcsFix($targets)
    {
        $config = config('laravelcodechecker');
        echo $config['php'] . ' ' . $config['phpcbf']
            . ' ' . $config['phpcs_standard'] . ' ' . (count($targets) > 0
                ? implode(' ', $targets) : $config['phpcs_target']) . PHP_EOL;
        system(
            $config['php'] . ' ' . $config['phpcbf']
            . ' ' . $config['phpcs_standard'] . ' ' . (count($targets) > 0
                ? implode(' ', $targets) : $config['phpcs_target']),
            $retval
        );
        if (0 != $retval) {
            exit(1);
        }
    }

    /**
     * php -l
     *
     */
    public function phpLint($targets)
    {
        $config = config('laravelcodechecker');
        $targets = count($targets) > 0 ? $targets : explode(' ', $config['phplint_target']);
        foreach ($targets as $target) {
            $this->runRecurseOnPhpFiles($target, function ($file) use ($config) {
                system($config['php'] . ' -l ' . ' ' . $file . ' \;', $retval);

                if (0 != $retval) {
                    exit(1);
                }
            });
        }
    }

    /**
     * phpmd
     *
     */
    public function phpmd($targets)
    {
        $config = config('laravelcodechecker');
        $targets = count($targets) > 0 ? $targets : explode(' ', $config['phpmd_target']);
        foreach ($targets as $target) {
            echo $config['phpmd'] . ' ' . $target . ' text ' . $config['phpmd_standard'] . ' \;' . PHP_EOL;
            system($config['phpmd'] . ' ' . $target . ' text ' . $config['phpmd_standard'] . ' \;', $retval);

            if (0 != $retval) {
                exit(1);
            }
        }
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
}

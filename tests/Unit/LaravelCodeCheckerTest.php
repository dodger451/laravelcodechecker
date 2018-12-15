<?php

namespace dodger451\LaravelCodeChecker;

use Orchestra\Testbench\TestCase;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


/**
 * @method run(\PHPUnit\Framework\TestResult $result = null): TestResult
 */
class LaravelCodeCheckerTest extends TestCase
{

    /**
     * PHPCS.
     */
    public function testPhpcsCheckRunsValidCommand()
    {
        $expectedCommand = 'php tools/phpcs --standard=config/phpcs/ dummy_target';
        $expectedReturn =  $expectedCommand.PHP_EOL.'dummy_output';

        app('config')->set(
            'laravelcodechecker',
            [
                'php-cli' => 'php',
                'phpcs' => 'tools/phpcs',
                'phpcs_standard' => '--standard=config/phpcs/'
            ]
        );

        $mockLaravelCodeChecker = $this->getSuccessfulMock($expectedCommand);

        $out = $mockLaravelCodeChecker->phpcsCheck(['dummy_target']);
        $this->assertEquals($expectedReturn, $out);
    }
    public function testPhpcsCheckRunsThrowsOnFail()
    {
        $mockLaravelCodeChecker = $this->getFailingMock();

        $this->expectException(\Symfony\Component\Process\Exception\ProcessFailedException::class);
        $mockLaravelCodeChecker->phpcsCheck(['dummy_target']);
    }

    /**
     * phpcsFix.
     */
    public function testPhpcsFixRunsValidCommand()
    {
        $expectedCommand = 'php tools/phpcbf --standard=config/phpcs/ dummy_target';
        $expectedReturn =  $expectedCommand.PHP_EOL.'dummy_output';

        app('config')->set(
            'laravelcodechecker',
            [
                'php-cli' => 'php',
                'phpcbf' => 'tools/phpcbf',
                'phpcs_standard' => '--standard=config/phpcs/'
            ]
        );

        $mockLaravelCodeChecker = $this->getSuccessfulMock($expectedCommand);

        $out = $mockLaravelCodeChecker->phpcsFix(['dummy_target']);
        $this->assertEquals($expectedReturn, $out);
    }

    public function testPhpcsFixRunsThrowsOnFail()
    {
        $mockLaravelCodeChecker = $this->getFailingMock();

        $this->expectException(\Symfony\Component\Process\Exception\ProcessFailedException::class);
        $mockLaravelCodeChecker->phpcsFix(['dummy_target']);
    }
    /**
     * php -l
     */
    public function testPhpLintRunsValidCommand()
    {
        $expectedCommand = 'php -l '.__FILE__.' \;';
        $expectedReturn =  'Checked 1 files.';

        app('config')->set(
            'laravelcodechecker',
            [
                'php-cli' => 'php',
                'phplint_target' => __FILE__
            ]
        );

        $mockProcess = \Mockery::mock('Symfony\Component\Process\Process')
            ->makePartial();
        $mockProcess->shouldReceive('run');
        $mockProcess->shouldReceive('isSuccessful')->andReturn(true);
        $mockProcess->shouldReceive('getCommandLine')->andReturn($expectedCommand);
        $mockProcess->shouldReceive('getOutput')->andReturn('dummy_output');

        $mockLaravelCodeChecker = \Mockery::mock('dodger451\LaravelCodeChecker\LaravelCodeChecker[newProcess]')
            ->makePartial()->shouldAllowMockingProtectedMethods();
        $mockLaravelCodeChecker->shouldReceive('newProcess')
            ->with($expectedCommand)
            ->andReturn($mockProcess);

        $out = $mockLaravelCodeChecker->phpLint([__FILE__]);
        $this->assertEquals($expectedReturn, $out);
    }

    public function testPhpLintRunsThrowsOnFail()
    {
        $mockLaravelCodeChecker = $this->getFailingMock();

        $this->expectException(\Symfony\Component\Process\Exception\ProcessFailedException::class);
        $mockLaravelCodeChecker->phpcsCheck([__FILE__]);
    }
    /**
     * HELPER
     */
    protected function getSuccessfulMock($expectedCommand)
    {
        $mockProcess = \Mockery::mock('Symfony\Component\Process\Process')
            ->makePartial();
        $mockProcess->shouldReceive('run')->once();
        $mockProcess->shouldReceive('isSuccessful')->once()->andReturn(true);
        $mockProcess->shouldReceive('getCommandLine')->once()->andReturn($expectedCommand);
        $mockProcess->shouldReceive('getOutput')->once()->andReturn('dummy_output');

        $mockLaravelCodeChecker = \Mockery::mock('dodger451\LaravelCodeChecker\LaravelCodeChecker[newProcess,newProcessFailedException]')
            ->shouldAllowMockingProtectedMethods();
        $mockLaravelCodeChecker->shouldReceive('newProcess')
            ->with($expectedCommand)
            ->andReturn($mockProcess);
        return $mockLaravelCodeChecker;
    }

    protected function getFailingMock()
    {
        $mockProcess = \Mockery::mock('Symfony\Component\Process\Process');//->makePartial();
        $mockProcess->shouldReceive('run')->once();
        $mockProcess->shouldReceive('isSuccessful')->andReturn(false);
        $mockProcess->shouldReceive('newProcessFailedException')->andReturn($mockProcess);

        $mockLaravelCodeChecker = \Mockery::mock(
            'dodger451\LaravelCodeChecker\LaravelCodeChecker[newProcess,newProcessFailedException]'
        )->shouldAllowMockingProtectedMethods();
        $mockLaravelCodeChecker->shouldReceive('newProcess')
            ->andReturn($mockProcess);
        $mockLaravelCodeChecker->shouldReceive('newProcessFailedException')
            ->andReturn(\Mockery::mock('Symfony\Component\Process\Exception\ProcessFailedException'));
        return $mockLaravelCodeChecker;
    }
}

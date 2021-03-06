<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Tests\Component\HttpKernel\Debug;

use Symfony\Component\HttpKernel\Debug\Stopwatch;

/**
 * StopwatchTest
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class StopwatchTest extends \PHPUnit_Framework_TestCase
{
    public function testStart()
    {
        $stopwatch = new Stopwatch();
        $event = $stopwatch->start('foo', 'cat');

        $this->assertInstanceof('Symfony\Component\HttpKernel\Debug\StopwatchEvent', $event);
        $this->assertEquals('cat', $event->getCategory());
    }

    public function testStop()
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('foo', 'cat');
        usleep(10000);
        $event = $stopwatch->stop('foo');

        $this->assertInstanceof('Symfony\Component\HttpKernel\Debug\StopwatchEvent', $event);
        $total = $event->getTotalTime();
        $this->assertTrue($total >= 9 && $total <= 20);
    }

    public function testLap()
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('foo', 'cat');
        usleep(10000);
        $event = $stopwatch->lap('foo');
        usleep(10000);
        $stopwatch->stop('foo');

        $this->assertInstanceof('Symfony\Component\HttpKernel\Debug\StopwatchEvent', $event);
        $total = $event->getTotalTime();
        $this->assertTrue($total >= 18 && $total <= 30);
    }

    /**
     * @expectedException \LogicException
     */
    public function testStopWithoutStart()
    {
        $stopwatch = new Stopwatch();
        $stopwatch->stop('foo');
    }

    public function testSection()
    {
        $stopwatch = new Stopwatch();

        $stopwatch->startSection();
        $stopwatch->start('foo', 'cat');
        $stopwatch->stop('foo');
        $stopwatch->start('bar', 'cat');
        $stopwatch->stop('bar');
        $stopwatch->stopSection(1);

        $stopwatch->startSection();
        $stopwatch->start('foobar', 'cat');
        $stopwatch->stop('foobar');
        $stopwatch->stopSection(2);

        // the section is an event by itself
        $this->assertEquals(3, count($stopwatch->getSectionEvents(1)));
        $this->assertEquals(2, count($stopwatch->getSectionEvents(2)));
    }
}

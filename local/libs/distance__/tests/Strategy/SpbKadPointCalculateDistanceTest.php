<?php

namespace Tests\Strategy;

use InvalidArgumentException;
use Mrden\MkadDistance\Geometry\Point;
use Mrden\MkadDistance\Geometry\Polygon\SpbKad;
use Mrden\MkadDistance\Geometry\Polygon\SpbKadJunctions;
use Mrden\MkadDistance\Iterface\DistanceCalculatorStrategy;
use Mrden\MkadDistance\Strategy\PointDistanceCalculator;
use Tests\TestCase;

class SpbKadPointCalculateDistanceTest extends TestCase
{
    /**
     * @var DistanceCalculatorStrategy
     */
    private $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PointDistanceCalculator(
            new SpbKad(),
            new SpbKadJunctions()
        );
    }

    public function testCalculateByRouteToVsevolozhsk(): void
    {
        $distance = $this->calculator->calculate(new Point(60.021319, 30.654084));
        $this->assertEquals(17.57, $distance);
    }

    public function testCalculateByLineToVsevolozhsk(): void
    {
        $distance = $this->calculator->calculate(new Point(60.021319, 30.654084), false);
        $this->assertEquals(9.89, $distance);
    }

    public function testFailCalculateByAddress(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->calculator->calculate('Всеволожск, Ленинградская область');
    }

    public function testFailCalculateByArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->calculator->calculate([60.021319, 30.654084]);
    }
}
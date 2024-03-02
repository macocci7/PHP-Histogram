<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpHistogram\Traits;

use PHPUnit\Framework\TestCase;
use Macocci7\PhpHistogram\Traits\JudgeTrait;
use Nette\Neon\Neon;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class JudgeTraitTest extends TestCase
{
    use JudgeTrait;

    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    // phpcs:disable Generic.Files.LineLength.TooLong

    public static function provide_isNumber_can_judge_correctly(): array
    {
        return [
            [ 'item' => null, 'expect' => false, ],
            [ 'item' => true, 'expect' => false, ],
            [ 'item' => false, 'expect' => false, ],
            [ 'item' => '', 'expect' => false, ],
            [ 'item' => [], 'expect' => false, ],
            [ 'item' => 0, 'expect' => true, ],
            [ 'item' => -100, 'expect' => true, ],
            [ 'item' => 100, 'expect' => true, ],
            [ 'item' => 0.0, 'expect' => true, ],
            [ 'item' => -100.5, 'expect' => true, ],
            [ 'item' => 100.5, 'expect' => true, ],
            [ 'item' => '0', 'expect' => false, ],
            [ 'item' => '-100', 'expect' => false, ],
            [ 'item' => '100', 'expect' => false, ],
            [ 'item' => '0.0', 'expect' => false, ],
            [ 'item' => '-100.5', 'expect' => false, ],
            [ 'item' => '100.5', 'expect' => false, ],
        ];
    }

    /**
     * @dataProvider provide_isNumber_can_judge_correctly
     */
    public function test_isNumber_can_judge_correctly(mixed $item, bool $expect): void
    {
        $this->assertSame($expect, $this->isNumber($item));
    }

    public static function provide_isColorCode_can_judge_correctly(): array
    {
        return [
            ['color' => '', 'expect' => false, ],
            ['color' => 'red', 'expect' => false, ],
            ['color' => 'ffffff', 'expect' => false, ],
            ['color' => '#ff', 'expect' => false, ],
            ['color' => '#00', 'expect' => false, ],
            ['color' => '#ffg', 'expect' => false, ],
            ['color' => '#fff', 'expect' => true, ],
            ['color' => '#000', 'expect' => true, ],
            ['color' => '#ffff', 'expect' => false, ],
            ['color' => '#0000', 'expect' => false, ],
            ['color' => '#fffff', 'expect' => false, ],
            ['color' => '#00000', 'expect' => false, ],
            ['color' => '#fffffg', 'expect' => false, ],
            ['color' => '#ffffff', 'expect' => true, ],
            ['color' => '#000000', 'expect' => true, ],
            ['color' => '#f0f0f0', 'expect' => true, ],
            ['color' => '#0f0f0f', 'expect' => true, ],
            ['color' => '#fffffff', 'expect' => false, ],
            ['color' => '#0000000', 'expect' => false, ],
        ];
    }

    /**
     * @dataProvider provide_isColorCode_can_judge_correctly
     */
    public function test_isColorCode_can_judge_correctly(string $color, bool $expect): void
    {
        $this->assertSame($expect, $this->isColorCode($color));
    }
}

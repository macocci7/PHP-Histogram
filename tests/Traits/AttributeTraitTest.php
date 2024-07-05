<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpHistogram\Traits;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Macocci7\PhpHistogram\Traits\AttributeTrait;
use Macocci7\PhpHistogram\Traits\JudgeTrait;
use Nette\Neon\Neon;

final class AttributeTraitTest extends TestCase
{
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    // phpcs:disable Generic.Files.LineLength.TooLong

    public static function provide_size_can_return_size_correctly(): array
    {
        return [
            ['size' => ['width' => 0, 'height' => 0]],
            ['size' => ['width' => 100, 'height' => 200]],
            ['size' => ['width' => 300, 'height' => 400]],
            ['size' => ['width' => 600, 'height' => 500]],
        ];
    }

    #[DataProvider('provide_size_can_return_size_correctly')]
    public function test_size_can_return_size_correctly(array $size): void
    {
        $o = new class (...$size) {
            use AttributeTrait;
            use JudgeTrait;

            public function __construct($width, $height)
            {
                $this->canvasWidth = $width;
                $this->canvasHeight = $height;
            }
        };
        $this->assertSame($size, $o->size());
    }

    public static function provide_resize_can_resize_correctly(): array
    {
        return [
            [
                'size' => ['width' => 100, 'height' => 200],
                'resize' => ['width' => 50, 'height' => 50],
                'expected' => ['width' => 50, 'height' => 50],
            ],
            [
                'size' => ['width' => 100, 'height' => 200],
                'resize' => ['width' => 200, 'height' => 300],
                'expected' => ['width' => 200, 'height' => 300],
            ],
        ];
    }

    #[DataProvider('provide_resize_can_resize_correctly')]
    public function test_resize_can_resize_correctly(array $size, array $resize, array $expected): void
    {
        $o = new class (...$size) {
            use AttributeTrait;
            use JudgeTrait;

            public function __construct($width, $height)
            {
                $this->CANVAS_WIDTH_LIMIT_LOWER = 50;
                $this->CANVAS_HEIGHT_LIMIT_LOWER = 50;
                $this->canvasWidth = $width;
                $this->canvasHeight = $height;
            }
        };
        $this->assertSame($expected, $o->resize(...$resize)->size());
    }

    public static function provide_plotarea_can_set_plotarea_correctly(): array
    {
        return [
            [
                'plotarea' => [
                    'offset' => [0, 0],
                ],
                'expected' => [
                    'offset' => [0, 0],
                    'backgroundColor' => null,
                ],
            ],
            [
                'plotarea' => [
                    'offset' => [10, 20],
                ],
                'expected' => [
                    'offset' => [10, 20],
                    'backgroundColor' => null,
                ],
            ],
            [
                'plotarea' => [
                    'width' => 200,
                ],
                'expected' => [
                    'width' => 200,
                    'backgroundColor' => null,
                ],
            ],
            [
                'plotarea' => [
                    'height' => 500,
                ],
                'expected' => [
                    'height' => 500,
                    'backgroundColor' => null,
                ],
            ],
            [
                'plotarea' => [
                    'backgroundColor' => '#aabbcc',
                ],
                'expected' => [
                    'backgroundColor' => '#aabbcc',
                ],
            ],
            [
                'plotarea' => [
                    'offset' => [10, 20],
                    'width' => 200,
                ],
                'expected' => [
                    'offset' => [10, 20],
                    'width' => 200,
                    'backgroundColor' => null,
                ],
            ],
            [
                'plotarea' => [
                    'offset' => [10, 20],
                    'height' => 100,
                ],
                'expected' => [
                    'offset' => [10, 20],
                    'height' => 100,
                    'backgroundColor' => null,
                ],
            ],
            [
                'plotarea' => [
                    'offset' => [10, 20],
                    'backgroundColor' => '#aabbcc',
                ],
                'expected' => [
                    'offset' => [10, 20],
                    'backgroundColor' => '#aabbcc',
                ],
            ],
            [
                'plotarea' => [
                    'width' => 100,
                    'height' => 200,
                ],
                'expected' => [
                    'width' => 100,
                    'height' => 200,
                    'backgroundColor' => null,
                ],
            ],
            [
                'plotarea' => [
                    'width' => 100,
                    'backgroundColor' => '#aabbcc',
                ],
                'expected' => [
                    'width' => 100,
                    'backgroundColor' => '#aabbcc',
                ],
            ],
            [
                'plotarea' => [
                    'height' => 100,
                    'backgroundColor' => '#aabbcc',
                ],
                'expected' => [
                    'height' => 100,
                    'backgroundColor' => '#aabbcc',
                ],
            ],
            [
                'plotarea' => [
                    'offset' => [10, 20],
                    'width' => 100,
                    'height' => 200,
                ],
                'expected' => [
                    'offset' => [10, 20],
                    'width' => 100,
                    'height' => 200,
                    'backgroundColor' => null,
                ],
            ],
            [
                'plotarea' => [
                    'offset' => [10, 20],
                    'width' => 100,
                    'backgroundColor' => '#aabbcc',
                ],
                'expected' => [
                    'offset' => [10, 20],
                    'width' => 100,
                    'backgroundColor' => '#aabbcc',
                ],
            ],
            [
                'plotarea' => [
                    'offset' => [10, 20],
                    'height' => 100,
                    'backgroundColor' => '#aabbcc',
                ],
                'expected' => [
                    'offset' => [10, 20],
                    'height' => 100,
                    'backgroundColor' => '#aabbcc',
                ],
            ],
            [
                'plotarea' => [
                    'offset' => [10, 20],
                    'width' => 100,
                    'height' => 200,
                    'backgroundColor' => '#aabbcc',
                ],
                'expected' => [
                    'offset' => [10, 20],
                    'width' => 100,
                    'height' => 200,
                    'backgroundColor' => '#aabbcc',
                ],
            ],
        ];
    }

    #[DataProvider('provide_plotarea_can_set_plotarea_correctly')]
    public function test_plotarea_can_set_plotarea_correctly(array $plotarea, array $expected): void
    {
        $o = new class {
            use AttributeTrait;
            use JudgeTrait;

            public function getPlotarea()
            {
                return $this->plotarea;
            }
        };
        $this->assertSame($expected, $o->plotarea(...$plotarea)->getPlotarea());
    }

    public static function provide_frame_can_set_frame_correctly(): array
    {
        return [
            ['xRatio' => 0.5, 'yRatio' => 0.4],
        ];
    }

    #[DataProvider('provide_frame_can_set_frame_correctly')]
    public function test_frame_can_set_frame_correctly(float $xRatio, float $yRatio): void
    {
        $o = new class {
            use AttributeTrait;
            use JudgeTrait;

            public function getXRatio()
            {
                return $this->frameXRatio;
            }

            public function getYRatio()
            {
                return $this->frameYRatio;
            }
        };
        $o->frame($xRatio, $yRatio);
        $this->assertSame($xRatio, $o->getXRatio());
        $this->assertSame($yRatio, $o->getYRatio());
    }

    public static function provide_labelX_can_set_label_correctly(): array
    {
        return [
            [
                'params' => [
                    'label' => 'aaa',
                ],
                'expected' => [
                    'label' => 'aaa',
                    'offsetX' => 0,
                    'offsetY' => 0,
                ],
            ],
            [
                'params' => [
                    'label' => 'aaa',
                    'offsetX' => 10,
                ],
                'expected' => [
                    'label' => 'aaa',
                    'offsetX' => 10,
                    'offsetY' => 0,
                ],
            ],
            [
                'params' => [
                    'label' => 'aaa',
                    'offsetY' => 10,
                ],
                'expected' => [
                    'label' => 'aaa',
                    'offsetX' => 0,
                    'offsetY' => 10,
                ],
            ],
            [
                'params' => [
                    'label' => 'aaa',
                    'offsetX' => 10,
                    'offsetY' => 20,
                ],
                'expected' => [
                    'label' => 'aaa',
                    'offsetX' => 10,
                    'offsetY' => 20,
                ],
            ],
        ];
    }

    #[DataProvider('provide_labelX_can_set_label_correctly')]
    public function test_labelX_can_set_label_correctly(array $params, array $expected): void
    {
        $o = new class {
            use AttributeTrait;
            use JudgeTrait;

            public function getResult()
            {
                return [
                    'label' => $this->labelX,
                    'offsetX' => $this->labelXOffsetX,
                    'offsetY' => $this->labelXOffsetY,
                ];
            }
        };
        $o->labelX(...$params);
        $this->assertSame($expected, $o->getResult());
    }

    public static function provide_labelY_can_set_label_correctly(): array
    {
        return [
            [
                'params' => [
                    'label' => 'aaa',
                ],
                'expected' => [
                    'label' => 'aaa',
                    'offsetX' => 0,
                    'offsetY' => 0,
                ],
            ],
            [
                'params' => [
                    'label' => 'aaa',
                    'offsetX' => 10,
                ],
                'expected' => [
                    'label' => 'aaa',
                    'offsetX' => 10,
                    'offsetY' => 0,
                ],
            ],
            [
                'params' => [
                    'label' => 'aaa',
                    'offsetY' => 10,
                ],
                'expected' => [
                    'label' => 'aaa',
                    'offsetX' => 0,
                    'offsetY' => 10,
                ],
            ],
            [
                'params' => [
                    'label' => 'aaa',
                    'offsetX' => 10,
                    'offsetY' => 20,
                ],
                'expected' => [
                    'label' => 'aaa',
                    'offsetX' => 10,
                    'offsetY' => 20,
                ],
            ],
        ];
    }

    #[DataProvider('provide_labelY_can_set_label_correctly')]
    public function test_labelY_can_set_label_correctly(array $params, array $expected): void
    {
        $o = new class {
            use AttributeTrait;
            use JudgeTrait;

            public function getResult()
            {
                return [
                    'label' => $this->labelY,
                    'offsetX' => $this->labelYOffsetX,
                    'offsetY' => $this->labelYOffsetY,
                ];
            }
        };
        $o->labelY(...$params);
        $this->assertSame($expected, $o->getResult());
    }

    public static function provide_caption_can_set_caption_correctly(): array
    {
        return [
            [
                'params' => [
                    'caption' => 'aaa',
                ],
                'expected' => [
                    'caption' => 'aaa',
                    'offsetX' => 0,
                    'offsetY' => 0,
                ],
            ],
            [
                'params' => [
                    'caption' => 'aaa',
                    'offsetX' => 10,
                ],
                'expected' => [
                    'caption' => 'aaa',
                    'offsetX' => 10,
                    'offsetY' => 0,
                ],
            ],
            [
                'params' => [
                    'caption' => 'aaa',
                    'offsetY' => 10,
                ],
                'expected' => [
                    'caption' => 'aaa',
                    'offsetX' => 0,
                    'offsetY' => 10,
                ],
            ],
            [
                'params' => [
                    'caption' => 'aaa',
                    'offsetX' => 10,
                    'offsetY' => 20,
                ],
                'expected' => [
                    'caption' => 'aaa',
                    'offsetX' => 10,
                    'offsetY' => 20,
                ],
            ],
        ];
    }

    #[DataProvider('provide_caption_can_set_caption_correctly')]
    public function test_caption_can_set_caption_correctly(array $params, array $expected): void
    {
        $o = new class {
            use AttributeTrait;
            use JudgeTrait;

            public function getResult()
            {
                return [
                    'caption' => $this->caption,
                    'offsetX' => $this->captionOffsetX,
                    'offsetY' => $this->captionOffsetY,
                ];
            }
        };
        $o->caption(...$params);
        $this->assertSame($expected, $o->getResult());
    }
}

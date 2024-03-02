<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpHistogram\Helpers;

use PHPUnit\Framework\TestCase;
use Macocci7\PhpHistogram\Helpers\Config;
use Nette\Neon\Neon;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class ConfigTest extends TestCase
{
    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    // phpcs:disable Generic.Files.LineLength.TooLong

    public string $basConf = __DIR__ . '/ConfigTest.neon';
    public string $testConf = __DIR__ . '/../../conf/ConfigTest.neon';

    public static function setUpBeforeClass(): void
    {
        $baseConf = __DIR__ . '/ConfigTest.neon';
        $testConf = __DIR__ . '/../../conf/ConfigTest.neon';
        copy($baseConf, $testConf);
    }

    public function test_load_can_load_config_file_correctly(): void
    {
        Config::load();
        $r = new \ReflectionClass(Config::class);
        $p = $r->getProperty('conf');
        $p->setAccessible(true);
        $this->assertSame(
            Neon::decodeFile($this->testConf),
            $p->getValue()[$this::class]
        );
    }

    public function return_class_name_from_config(): string|null
    {
        return Config::class();
    }

    public function test_class_can_return_class_name_correctly(): void
    {
        $this->assertSame($this::class, $this->return_class_name_from_config());
    }

    public static function provide_className_can_return_class_name_correctly(): array
    {
        return [
            "Fully Qualified" => [ 'class' => '\Macocci7\PhpHistogram\Helper\ConfigTest', 'expect' => 'ConfigTest', ],
            "Relative" => [ 'class' => 'Helper\ConfigTest', 'expect' => 'ConfigTest', ],
            "Only Class Name" => [ 'class' => 'ConfigTest', 'expect' => 'ConfigTest', ],
        ];
    }

    /**
     * @dataProvider provide_className_can_return_class_name_correctly
     */
    public function test_className_can_return_class_name_correctly(string $class, string $expect): void
    {
        $this->assertSame($expect, Config::className($class));
    }

    public function test_get_can_return_value_correctly(): void
    {
        Config::load();
        foreach (Neon::decodeFile($this->testConf) as $key => $value) {
            $this->assertSame(
                $value,
                Config::get($key)
            );
        }
    }

    public static function provide_support_object_like_keys_correctly(): array
    {
        $testConf = __DIR__ . '/../../conf/ConfigTest.neon';
        return [
            "null" => [ 'key' => null, 'expect' => null, ],
            "empty string" => [ 'key' => '', 'expect' => null, ],
            "dot" => [ 'key' => '.', 'expect' => null, ],
            "item2" => [ 'key' => 'item2', 'expect' => Neon::decodeFile($testConf)['item2'], ],
            "item2.child2" => [ 'key' => 'item2.child2', 'expect' => Neon::decodeFile($testConf)['item2']['child2'], ],
            "item2.child2.grandChild2" => [ 'key' => 'item2.child2.grandChild2', 'expect' => Neon::decodeFile($testConf)['item2']['child2']['grandChild2'], ],
        ];
    }

    /**
     * @dataProvider provide_support_object_like_keys_correctly
     */
    public function get_can_support_object_like_keys_correctly(string $key, array|null $expect): void
    {
        $this->assertSame($expect, Config::get($key));
    }

    public static function provide_isValid_can_judge_correctly(): array
    {
        return [
            [ 'input' => null, 'def' => 'int', 'expect' => false, ],
            [ 'input' => true, 'def' => 'int', 'expect' => false, ],
            [ 'input' => false, 'def' => 'int', 'expect' => false, ],
            [ 'input' => [], 'def' => 'int', 'expect' => false, ],
            [ 'input' => '1', 'def' => 'int', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'int', 'expect' => true, ],
            [ 'input' => 1.5, 'def' => 'int', 'expect' => false, ],
            [ 'input' => null, 'def' => 'float', 'expect' => false, ],
            [ 'input' => true, 'def' => 'float', 'expect' => false, ],
            [ 'input' => false, 'def' => 'float', 'expect' => false, ],
            [ 'input' => [], 'def' => 'float', 'expect' => false, ],
            [ 'input' => '1.5', 'def' => 'float', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'float', 'expect' => false, ],
            [ 'input' => 1.5, 'def' => 'float', 'expect' => true, ],
            [ 'input' => null, 'def' => 'bool', 'expect' => false, ],
            [ 'input' => true, 'def' => 'bool', 'expect' => true, ],
            [ 'input' => false, 'def' => 'bool', 'expect' => true, ],
            [ 'input' => [], 'def' => 'bool', 'expect' => false, ],
            [ 'input' => 'true', 'def' => 'bool', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'bool', 'expect' => false, ],
            [ 'input' => 1.5, 'def' => 'bool', 'expect' => false, ],
            [ 'input' => null, 'def' => 'string', 'expect' => false, ],
            [ 'input' => true, 'def' => 'string', 'expect' => false, ],
            [ 'input' => false, 'def' => 'string', 'expect' => false, ],
            [ 'input' => [], 'def' => 'string', 'expect' => false, ],
            [ 'input' => '', 'def' => 'string', 'expect' => true, ],
            [ 'input' => 1, 'def' => 'string', 'expect' => false, ],
            [ 'input' => 1.5, 'def' => 'string', 'expect' => false, ],
            [ 'input' => null, 'def' => 'array', 'expect' => false, ],
            [ 'input' => true, 'def' => 'array', 'expect' => false, ],
            [ 'input' => false, 'def' => 'array', 'expect' => false, ],
            [ 'input' => [], 'def' => 'array', 'expect' => true, ],
            [ 'input' => 1, 'def' => 'array', 'expect' => false, ],
            [ 'input' => 1.5, 'def' => 'array', 'expect' => false, ],
            [ 'input' => null, 'def' => 'number', 'expect' => false, ],
            [ 'input' => true, 'def' => 'number', 'expect' => false, ],
            [ 'input' => false, 'def' => 'number', 'expect' => false, ],
            [ 'input' => [], 'def' => 'number', 'expect' => false, ],
            [ 'input' => '1', 'def' => 'number', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'number', 'expect' => true, ],
            [ 'input' => 1.5, 'def' => 'number', 'expect' => true, ],
            [ 'input' => null, 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => true, 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => false, 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => [], 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => 1.5, 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => 'fff', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => 'ffffff', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '#fff', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#000', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#0f0', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#ggg', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '#0fg', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '#fffff', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '#ffffff', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#fffffff', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '#000000', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#0f0f0f', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#0f0f0g', 'def' => 'colorCode', 'expect' => false, ],
        ];
    }

    /**
     * @dataProvider provide_isValid_can_judge_correctly
     */
    public function test_isValid_can_judge_correctly(mixed $input, string $def, bool $expect): void
    {
        $this->assertSame($expect, Config::isValid($input, $def));
    }

    public static function tearDownAfterClass(): void
    {
        $testConf = __DIR__ . '/../../conf/ConfigTest.neon';
        unlink($testConf);
    }
}

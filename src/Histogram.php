<?php

namespace Macocci7\PhpHistogram;

use Macocci7\PhpHistogram\Helpers\Config;
use Macocci7\PhpHistogram\Plotter;
use Nette\Neon\Neon;

/**
 * Class for Histogram operation
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Histogram extends Plotter
{
    use Traits\JudgeTrait;

    /**
     * constructor
     */
    public function __construct(int $width = 400, int $height = 300)
    {
        parent::__construct();
        $this->loadConf();
        $this->resize($width, $height);
    }

    /**
     * loads config.
     */
    private function loadConf(): void
    {
        Config::load();
        $props = [
            'CANVAS_WIDTH_LIMIT_LOWER',
            'CANVAS_HEIGHT_LIMIT_LOWER',
        ];
        foreach ($props as $prop) {
            $this->{$prop} = Config::get($prop);
        }
    }

    /**
     * set config from specified resource
     * @param   string|mixed[]  $configResource
     */
    public function config(string|array $configResource): self
    {
        if (is_string($configResource)) {
            $conf = $this->configFromFile($configResource);
        } else {
            $conf = $this->configFromArray($configResource);
        }
        foreach ($conf as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }

    /**
     * returns valid config items from specified file
     * @return  mixed[]
     * @thrown  \Exception
     */
    private function configFromFile(string $path): array
    {
        if (strlen($path) === 0) {
            throw new \Exception("Specify valid filename.");
        }
        if (!is_readable($path)) {
            throw new \Exception("Cannot read file $path.");
        }
        $content = Neon::decodeFile($path);
        return $this->configFromArray($content);
    }

    /**
     * returns valid config items from specified array
     * @param   mixed[] $content
     * @return  mixed[]
     * @thrown  \Exception
     */
    private function configFromArray(array $content): array
    {
        $conf = [];
        foreach ($this->validConfig as $key => $def) {
            if (array_key_exists($key, $content)) {
                if (Config::isValid($content[$key], $def['type'])) {
                    $conf[$key] = $content[$key];
                } else {
                    $message = $key . " must be type of " . $def['type'] . ".";
                    throw new \Exception($message);
                }
            }
        }
        return $conf;
    }

    /**
     * returns config:
     * - of the specified key
     * - all configs if param is not specified
     * @return  mixed
     */
    public function getConfig(string|null $key = null): mixed
    {
        if (is_null($key)) {
            $config = [];
            foreach (array_keys($this->validConfig) as $key) {
                $config[$key] = $this->{$key};
            }
            return $config;
        }
        if (isset($this->validConfig[$key])) {
            return $this->{$key};
        }
        return null;
    }

    /**
     * sets class range
     * @thrown  \Exception
     */
    public function setClassRange(int|float $classRange): self
    {
        if ($classRange <= 0) {
            throw new \Exception("Class range must be a positive number.");
        }
        $this->ft->setClassRange($classRange);
        return $this;
    }

    /**
     * sets data
     * @param   array<int|string, int|float>    $data
     * @thrown  \Exception
     */
    public function setData(array $data): self
    {
        if (!$this->ft->isSettableData($data)) {
            throw new \Exception("Invalid data. Expected: array<int|string, int|float>");
        }
        $this->ft->setData($data);
        return $this;
    }
}

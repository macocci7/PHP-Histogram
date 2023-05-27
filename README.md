# PHP-Histogram

PHP-Histogram is easy to use for creating histograms.

<img src="img/HistogramExample08.png" width="300"/>

<img src="img/HistogramOhtaniShohei2023-05-15.png" width="300"/><br/>

## Installation

```bash
composer require macocci7/php-histogram
```

## Usage

### Most Simple Usage

- PHP

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpHistogram\Histogram;

    $hg = new Histogram();
    $hg->ft->setClassRange(5);
    $hg->ft->setData([0,5,8,10,12,13,15,16,17,18,19,20,24]);
    $hg->create('img/Histogram.png');
    ```

- Result: `img/Histogram.png`

    <img src="img/Histogram.png">

## Examples

- [HistogramExample.php](examples/HistogramExample.php) >> results in:

    ![Histogram.png](examples/img/Histogram.png)

- [HistogramExampleCase.php](examples/HistogramExampleCase.php) >> results in [HistogramExampleCase.md](examples/HistogramExampleCase.md)

- [OhtaniShoheiHistogram2023.php](examples/OhtaniShoheiHistogram2023.php) >> results in [OhtaniShoheiHistogram2023.md](examples/OhtaniShoheiHistogram2023.md)

- [PopulationInJapanHistogram2022.php](examples/PopulationInJapanHistogram2022.php) >> results in [PopulationInJapanHistogram2022.md](examples/PopulationInJapanHistogram2022.md)

## Lisence

[MIT](LICENSE)

*Document created: 2023/05/28*
*Document updated: 2023/05/28*

Copyright 2023 macocci7

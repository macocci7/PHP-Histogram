# FrequencyTable.php

## Example Images

- Frequency Table Example:

    <a href="#the-most-simple-usage"><img src="src/img/FrequencyTableExample.png" width="400"></a>

- Histogram and Boxplots:

    <a href="#histogram"><img src="src/img/HistogramExample06.png" width="300"></a>
    　<a href="#boxplot"><img src="src/img/BoxplotDetmersReid2023_01.png" width="300"></a>

## Contents
- [Overview](#overview)
- [Prerequisite](#prerequisite)
- [Installation](#installation)
- [Usage](#usage)
    - [The Most Simple Usage](#the-most-simple-usage)
    - [Other Usage](#other-usage)
- [Methods](#methods)
- [Examples](#examples)
- [Testing](#testing)
- [LICENSE](#license)
- [Appendix](#appendix)
    - [Histogram](#histogram)
    - [Boxplot](#boxplot)
    - [IQR Method](#iqr-method)

## Overview

`FrequencyTable.php` is single object class file written in PHP in order to operate frequency table easily.

ChatGTP and Google Bard cannot take statistics correctly at present, so I made this to teach them how to make a frequency table.

There seems to be some tools to make a Frequency Table in the world.

However, this FrequencyTable class is the most easiest tool to use, I think. (just I think so)

You can use it easily by just requiring the file `FrequencyTable.php`.

Locate `FrequencyTable.php` wherever you like.

Let's create an instance of FrequencyTable and operate it!

## Prerequisite

- `FrequencyTable.php` is written and tested in `PHP 8.1.2 CLI` environment. 
- `PHPUnit 10.1.3` is used in testing.
- You are expected to have known what frequency table is, and mathmatical terms used in this code:

    <details>
    <summary>Mathmatical Terms used in this code</summary>

    - Frequency Table
    - Class
    - Class Range
    - Class Value
    - Frequency
    - Cumulative Frequency
    - Relative Frequency
    - Cumulative Relative Frequency
    - Total
    - Mean
    - Max(imum)
    - Min(imum)
    - Data Range
    - Mode
    - Median
    - First Quartile
    - Third Quartile
    - Inter Quartile Range
    - Quartile Deviation
    </details>

## Installation

Locate `FrequencyTable.php` wherever you like.

## Usage

### The Most Simple Usage

You can use FrequencyTable class as follows.

**PHP Code: [Example.php](src/Example.php)**

```php
<?php
require('./class/FrequencyTable.php');

$ft = new FrequencyTable(['data'=>[0,5,10,15,20],'classRange'=>10]);
$ft->show();
```

**Command to Excute**

```bash
cd src
php -f Example.php
```

**Standard Output**

```bash
|Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
|:---:|:---:|:---:|:---:|---:|
|0 ~ 10|2|0.40|5.0|10.0|
|10 ~ 20|2|0.40|15.0|30.0|
|20 ~ 30|1|0.20|25.0|25.0|
|Total|5|1.00|---|65.0|
|Mean|---|---|---|13.0|
```

**Output Preview On VSCode**

|Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
|:---:|:---:|:---:|:---:|---:|
|0 ~ 10|2|0.40|5.0|10.0|
|10 ~ 20|2|0.40|15.0|30.0|
|20 ~ 30|1|0.20|25.0|25.0|
|Total|5|1.00|---|65.0|
|Mean|---|---|---|13.0|

### Other Usage

Let's create the PHP code to show a Frequency Table.

The name of new PHP file is `Example.php`.

1. Require `FrequencyTable.php`

    Require `FrequencyTable.php` as follows in your PHP code (Example.php).

    ```php
    <?php
    require('./class/FrequencyTable.php');
    ```

    Rewirte the path to the correct path which you located `FrequencyTable.php`.

2. Create an instance

    Then create an instance of FrequencyTable in your PHP code as follows.

    ```php
    <?php
    require('./class/FrequencyTable.php');

    $ft = new FrequencyTable();
    ```

3. Set the class range

    Then set the class range you as follows.

    ```php
    <?php
    require('./class/FrequencyTable.php');

    $ft = new FrequencyTable();
    $ft->setClassRange(10);
    ```

4. Set the data

    Then set the data to collect statistics as follows.

    ```php
    <?php
    require('./class/FrequencyTable.php');

    $ft = new FrequencyTable();
    $ft->setClassRange(10);

    $data = [0,5,10,15,20];
    $ft->setData($data);
    ```

5. Show the Frequency Table

    Now you can show the Frequency Table of the data you gave before as follows.

    ```php
    <?php
    require('./class/FrequencyTable.php');

    $ft = new FrequencyTable();
    $ft->setClassRange(10);

    $data = [0,5,10,15,20];
    $ft->setData($data);

    $ft->show();
    ```

    Or you can set both the class range and the data when you create an instance of FrequencyTable as follows.

    ```php
    <?php
    require('./class/FrequencyTable.php');

    $data = [0,5,10,15,20];
    $ft = new FrequencyTable(['data' => $data, 'classRange' => 10]);

    $ft->show();
    ```
    This is more simple. You can choose the way you like.

    By using the former way, you can set other data or class range after some operations.

6. Execute the PHP file `Example.php` you made

    Then the Frequency Table will be shown as text in Mark Down table format on the standard output in your console.

    Excecute the PHP code in you console as follows.

    ```bash
    php -f Example.php
    ```

    Standard Output

    ```bash
    $ php -f Example.php
    |Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
    |:---:|:---:|:---:|:---:|---:|
    |0 ~ 10|2|0.40|5.0|10.0|
    |10 ~ 20|2|0.40|15.0|30.0|
    |20 ~ 30|1|0.20|25.0|25.0|
    |Total|5|1.00|---|65.0|
    |Mean|---|---|---|13.0|
    ```
    You can make the output file as follows.

    ```bash
    php -f Example.php > Example.md
    ```

    Then the output will be written in [Example.md](src/Example.md).

    When you open [Example.md](src/Example.md) in your tool like `VSCode Preview` (or on Github),

    the frequency table will be shown as follows.

    |Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
    |:---:|:---:|:---:|:---:|---:|
    |0 ~ 10|2|0.40|5.0|10.0|
    |10 ~ 20|2|0.40|15.0|30.0|
    |20 ~ 30|1|0.20|25.0|25.0|
    |Total|5|1.00|---|65.0|
    |Mean|---|---|---|13.0|

## Methods

Learn more: [Methods](Methods.md)

## Examples

- [ExampleCases.php](src/ExampleCases.php) >> results in [ExampleCases.md](src/ExampleCases.md)
- [PopulationInJapan2022.php](src/PopulationInJapan2022.php) >> results in [PopulationInJapan2022.md](src/PopulationInJapan2022.md)
- [OhtaniShohei2023.php](src/OhtaniShohei2023.php) >> results in [OhtaniShohei2023.md](src/OhtaniShohei2023.md)
- [OutlierDetection.php](src/OutlierDetection.php) >> results in [OutlierDetection.md](src/OutlierDetection.md)
- [FrequencyTableTest.php](tests/FrequencyTableTest.php) : all usage is written in this code.

## Testing

You can test FrequencyTable.php using PHPUnit (phpunit.phar).

Type the command at the project top folder.

```bash
php ./tools/phpunit.phar ./tests/FrequencyTableTest.php --color auto --testdox
```

[TestResult.txt](TestResult.txt)

## LICENSE

[MIT](LICENSE)

## Appendix

### Histogram

[Histogram.php](src/class/Histogram.php) class is also additionally implemented.

You can create Histogram images like this:

- Histogram & Frequency Polygon & Cumulative Relative Frequency Polygon & Frequency

    ![HistogramExample08.png](src/img/HistogramExample08.png)

- Histogram & Frequency

    ![HistogramExample04.png](src/img/HistogramExample04.png)

Before using `Histogram.php`, you need to install [intervention/image](https://github.com/Intervention/image) as follows.

```bash
php ./tools/composer.phar require intervention/image
```

And you also need to install [Imagick PHP extension](https://www.php.net/manual/en/book.imagick.php) and make it enabled.

Check whether `imagick` is installed or not as follows:
- Command:
    ```bash
    php -i | grep imagick
    ```
- Output: `imagick` is `enabled`.
    ```bash
    /etc/php/8.1/cli/conf.d/20-imagick.ini,
    imagick
    imagick module => enabled
    imagick module version => 3.6.0
    imagick classes => Imagick, ImagickDraw, ImagickPixel, ImagickPixelIterator, ImagickKernel
    imagick.allow_zero_dimension_images => 0 => 0
    imagick.locale_fix => 0 => 0
    imagick.progress_monitor => 0 => 0
    imagick.set_single_thread => 1 => 1
    imagick.shutdown_sleep_count => 10 => 10
    imagick.skip_version_check => 1 => 1
    ```

If `imagick` is not installed or not `enabled`, follow the instruction of [php.net](https://www.php.net/manual/en/book.imagick.php) to install it or make it enabled.

Documents of Histogram is not written at present.

To learn more, see some examples.

- [HistogramExample.php](src/HistogramExample.php) >> results in [HistogramExample.md](src/HistogramExample.md)
- [OhtaniShoheiHistogram2023.php](src/OhtaniShoheiHistogram2023.php) >> results in [OhtaniShoheiHistogram2023.md](src/OhtaniShoheiHistogram2023.md)
- [PopulationInJapanHistogram2022.php](src/PopulationInJapanHistogram2022.php) >> result in [PopulationInJapanHistogram2022.md](src/PopulationInJapanHistogram2022.md)

`Histogram.php` uses [IPA ex Gothic 00401](https://moji.or.jp/ipafont/ipafontdownload/) font.

You can use other True Type Font by the next step:
- Copy `*.ttf` file which you want to use into `src/fonts/` folder.
- Configure the font path as follows: replace `hoge.ttf` with the name of the copied font file.
    ```php
    <?php
    require('./class/FrequencyTable.php');
    require('./class/Histogram.php');

    $ft = new FrequencyTable();
    $ft->setClassRange(10);
    $ft->setData([0,5,10,15,20]);

    $hg = new Histogram();
    $hg->configure(['fontPath' => 'fonts/hoge.ttf']);
    $hg->create($ft, 'img/Histogram.png');
    ```
- Note: If the `fontPath` you specified does not exist, `fontPath` is not overwritten and `IPA ex Gothic 0041` is used.

### Boxplot

[Boxplot.php](src/class/Boxplot.php) is also implemented.

It's still under construction, but you can create boxplot image file by using this file.

Example:
- [DetmersReidBoxplot2023.php](src/DetmersReidBoxplot2023.php) >> results in:
    - with Outlier Detection & Jitter Plotting & Mean Plotting

        ![BoxplotDetmersReid2023_01.png](src/img/BoxplotDetmersReid2023_01.png)

    - with Mean Plotting, without Outlier Detection, without Jitter Plotting

        ![BoxplotDetmersReid2023_02.png](src/img/BoxplotDetmersReid2023_02.png)

- [BoxplotExample.php](src/BoxplotExample.php) >> results in:
    - with Outlier Detection & Jitter Plotting & Mean Plotting & Legends Plotting & Vertical Grid & Multiple Legends

        ![BoxplotExample.png](src/img/BoxplotExample.png)
    
    - Note: This example needs [FakerPHP/Faker](https://github.com/FakerPHP/Faker) to be installed.
    
        In order to install `FakerPHP/Faker`, type a command as follows:

        ```bash
        php ./tools/composer.phar require fakerphp/faker
        ```

`Boxplot.php` has instance of FrequencyTable inside.

You can also get all data to draw a boxplot by using this FrequencyTable class.(without outlier detection)
- Max Value
- Min Value
- First Quartile
- Third Quartile
- Median
- Mean 
- Data Range
- Inter Quartile Range

Outlier Detection is not the job of FrequencyTable.

But, if you want to detect outliers, you can detect them by using IQR (Inter Quartile Range) Method.

Sample code is here: [OutlierDetection.php](src/OutlierDetection.php) >> results in [OutlierDetection.md](src/OutlierDetection.md)

### IQR Method

1. Set the UCL

    Mathmatical Formula (not PHP) is:
    ```
    UCL = Q3 + 1.5IQR
    ```
    UCL: Upper Control Limit / Q3: Third Quartile / IQR: Inter Quartile Range

2. Set the LCL

    ```
    LCL = Q1 - 1.5IQR
    ```
    LCL: Lower Control Limit / Q1: First Quartile / IQR: Inter Quartile Range

3. Detect Outliers

    If the VALUE meets the following condition, it's the Outlier.
    ```
    VALUE < LCL or UCL < VALUE
    ```

Learn more about boxplot: [box plot](https://byjus.com/maths/box-plot/)

Thanks for reading.

Have a happy coding!


*Document written: 2023/05/18*

*Last updated: 2023/05/24*

Copyright (c) 2023 macocci7

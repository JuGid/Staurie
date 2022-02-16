<?php

namespace Jugid\Staurie\Component\PrettyPrinter;

use InvalidArgumentException;
use Jugid\Staurie\Component\AbstractComponent;
use Jugid\Staurie\Component\Console\Console;

class PrettyPrinter extends AbstractComponent {

    public function name() : string {
        return 'prettyprinter';
    }

    public function require() : array {
        return [Console::class];
    }

    public function getEventName() : array {
        return [];
    }

    public function initialize() : void {}

    protected function action(string $event, array $arguments) : void {}

    public function defaultConfiguration() : array {
        return [];
    }
    
    const foreground_colors = [
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37',
    ];

    const background_colors = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47',
    ];

    /**
     * Use it to write a simple line
     * <code>
     * $this->write('Hello world');
     * 
     * Output :
     * <code>
     * Hello world
     * </code>
     */
    public function write(string $str, $foreground_color = null, $background_color = null, bool $centered = false) : void
    {
        if($centered) {
            $columns = exec('tput cols');
            $str = str_pad($str, $columns, " ", STR_PAD_BOTH);
        }

        echo sprintf('%s', $this->colored($str, $foreground_color, $background_color));
    }

    /**
     * Use it to write a simple line with an escape character
     * <code>
     * $this->writeLn('Hello world');
     * </code>
     * Output :
     * <code>
     * Hello world(\n)
     * </code>
     */
    public function writeLn(string $str, $foreground_color = null, $background_color = null, bool $centered = false) : void
    {
        if($centered) {
            $columns = exec('tput cols');
            $str = str_pad($str, $columns, " ", STR_PAD_BOTH);
        }

        echo sprintf("%s\n", $this->colored($str, $foreground_color, $background_color));
    }

    /**
     * Use it to write a line which is underlined
     * <code>
     *  $this->writeUnder('Hello world');
     * </code>
     * 
     * Output :
     * <code>
     * Hello world(\n)
     * -----------(\n)
     * </code>
     */
    public function writeUnder(string $str, $foreground_color = null, $background_color = null, bool $centered = false) : void 
    {
        $this->write($str, $foreground_color, $background_color, $centered);
        $this->writeSeparator('-', strlen($str), $centered);
    }

    /**
     * Use it to write a line that represents a separator
     * <code>
     * $this->writeSeparator('-', 30);
     * </code>
     * 
     * Output :
     * <code>
     * (\n)------------------------------(\n)
     * </code>
     */
    public function writeSeparator(string $separator = '-', int $size = 60, bool $centered = false) 
    {
        $this->writeLn('');
        $this->writeLn(str_repeat($separator, $size), null, null, $centered);
    }

    /**
     * Use it to write a line with a sleep between each characters
     */
    public function writeScroll(string $str, int $time_milliseconds = 5, bool $centered = false) {
        if($centered) {
            $columns = exec('tput cols');
            $str = str_pad($str, $columns, " ", STR_PAD_BOTH);
        }

        for($i = 0; $i < strlen($str); $i++) {
            $this->write($str[$i]);
            usleep($time_milliseconds * 1000);
        }
        $this->writeln('');
    }

    public function writeProgressbar(int $value, int $min = 0, int $max = 100, string $label = '',string $barAppareance = '=', int $nbBars = 10, bool $centered = false) {
        if($value > $max) {
            $value = $max;
        }

        $valuePerCent = round($value*100/$max, 2);
        $valuePerBar = round($max/$nbBars, 0, PHP_ROUND_HALF_UP);
        $nbBars = $valuePerBar > 0 ? round($value/$valuePerBar, 0, PHP_ROUND_HALF_UP) : 0;
        $nbSpaces = 10 - $nbBars;

        $progressBar = sprintf('[%s%s] %.2f%% (%d/%d)', str_repeat($barAppareance, $nbBars), str_repeat(' ', $nbSpaces), $valuePerCent, $value, $max);

        if(!empty($label)) {
            $progressBar = $label . ' : ' . $progressBar;
        }

        $this->writeLn($progressBar, null, null, $centered);
    }

    /**
     * Use it to write a table
     * <code>
     * $this->writeTable($header, $lines);
     * </code>
     * 
     * Output :
     * <code>
     * | Name    | Value |
     * +---------+-------+
     * | Param   | 60    |
     * | Foregro | false |
     * +---------+-------+
     * </code>
     * 
     * <code>
     * TWConsole::writeTable($header, $lines, true);
     * </code>
     * 
     * Output :
     * <code>
     * | Name    | Value |
     * +---------+-------+
     * | Param   | 60    |
     * +---------+-------+
     * | Foregro | false |
     * +---------+-------+
     * </code>
     * 
     * @param array $header The header of the table ['Name', 'Value]
     * @param array $lines The lines of the table [['Param', 60], ['Foregro', false]]
     * 
     */

    public function writeTable(array $header, array $lines, bool $with_separator = false)
    {
        if(count($lines) == 0) {
            throw new InvalidArgumentException('The lines are empty');
        }

        $columnsLength = $this->getMaxColumnsLengthForArray($header, $lines);
        $this->printArraySeparator($columnsLength);
        $this->printArrayLine($header, $columnsLength, true);
       
        foreach($lines as $line) {
            $this->printArrayLine($line, $columnsLength, $with_separator);
        }

        if(!$with_separator) {
            $this->printArraySeparator($columnsLength);
        }
    }

    private function printArrayLine(array $array, array $columns_length, bool $with_separator = false) : void 
    {
        $cell_format = '| %s ';
        $end_line = '|';

        $line_print = '';
        $column = 0;
        foreach($array as $cell) {
            $line_print .= sprintf($cell_format, $cell . str_repeat(' ', $columns_length[$column] - strlen($cell)));
            $column++;
        }
        $line_print.=$end_line;

        $this->writeln($line_print);

        if($with_separator) {
            $this->printArraySeparator($columns_length);
        }
    }

    private function printArraySeparator(array $columns_length) 
    {
        $separator_print = '+%s';
        $separator = '';

        for($column = 0; $column < count($columns_length); $column++) {
            $separator .= sprintf($separator_print, str_repeat('-', $columns_length[$column] + 2));
        }
        $separator .= sprintf($separator_print, '');

        $this->writeln($separator);
    }

    private function getMaxColumnsLengthForArray(array $header, array $content)
    {
        $maxHeader = $this->getColumnsLength($header);
        $contentMax = $this->getLinesColumnsLength($content);
        $numberOfColumns = count($maxHeader);
        $maxColumns = [];

        for($i = 0; $i < $numberOfColumns; $i++)
        {
            $columnLengths = array_map(function($line) use($i) {
                return $line[$i];
            }, $contentMax);

            $maxLengthOfColumn = max($columnLengths);
            
            if($maxHeader[$i] > $maxLengthOfColumn) {
                $maxColumns[] = $maxHeader[$i];
            } else {
                $maxColumns[] = $maxLengthOfColumn;
            }
        }

        return $maxColumns;
        
    }

    private function getColumnsLength(array $array) : array 
    {
        $map_strlen = array_map('strlen', $array);
        return array_values($map_strlen);
    }

    private function getLinesColumnsLength(array $lines) : array 
    {
        $columns_length = [];
        foreach($lines as $line) 
        {
            $columns_length[] = $this->getColumnsLength($line);
        }

        return $columns_length;
    }

    private function colored($string, $foreground_color = null, $background_color = null) : string
    {
        $colored_string = "";

        if (isset(self::foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . self::foreground_colors[$foreground_color] . "m";
        }

        if (isset(self::background_colors[$background_color])) {
            $colored_string .= "\033[" . self::background_colors[$background_color] . "m";
        }

        $colored_string .=  $string . "\033[0m";

        return $colored_string;
    }

}
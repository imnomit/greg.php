<?php

namespace nomit\Console\Format;

use nomit\Utility\Bag\BagInterface;
use nomit\Console\Format\Style\Style;
use nomit\Console\Format\Style\StyleInterface;
use nomit\Utility\Bag\Bag;
use nomit\Utility\String\UnicodeString;

class Formatter implements FormatterInterface
{

    protected array $styles = [];

    protected BagInterface $stack;

    protected bool $decorated;

    public static function width(?string $string): int
    {
        $string ??= '';

        if (preg_match('//u', $string)) {
            return (new UnicodeString($string))->width(false);
        }

        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return \strlen($string);
        }

        return mb_strwidth($string, $encoding);
    }

    public static function length(?string $string): int
    {
        $string ??= '';

        if (preg_match('//u', $string)) {
            return (new UnicodeString($string))->length();
        }

        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return \strlen($string);
        }

        return mb_strlen($string, $encoding);
    }

    public static function removeDecoration(FormatterInterface|OutputFormatterInterface $formatter, ?string $string): string
    {
        $isDecorated = $formatter->isDecorated();

        $formatter->setDecorated(false);

        // remove <...> formatting
        $string = $formatter->format($string ?? '');

        // remove already formatted characters
        $string = preg_replace("/\033\[[^m]*m/", '', $string ?? '');

        // remove terminal hyperlinks
        $string = preg_replace('/\\033]8;[^;]*;[^\\033]*\\033\\\\/', '', $string ?? '');

        $formatter->setDecorated($isDecorated);

        return $string;

    }

    public static function escape(string $text): string
    {
        $text = preg_replace('/([^\\\\]|^)([<>])/', '$1\\\\$2', $text);

        return self::escapeTrailingBackslash($text);
    }

    public static function escapeTrailingBackslash(string $text): string
    {
        if (str_ends_with($text, '\\')) {
            $len = \strlen($text);
            $text = rtrim($text, '\\');
            $text = str_replace("\0", '', $text);
            $text .= str_repeat("\0", $len - \strlen($text));
        }

        return $text;
    }

    public function __construct(bool $decorated = false, array $styles = [])
    {
        $this->decorated = $decorated;
        $this->stack = new Bag();

        $this->setStyle('error', new Style('white', 'red'));
        $this->setStyle('warn', new Style('yellow'));
        $this->setStyle('info', new Style('green'));
        $this->setStyle('comment', new Style('gray'));
        $this->setStyle('question', new Style('black', 'cyan'));

        foreach($styles as $name => $style) {
            $this->setStyle($name, $style);
        }
    }

    public function escapeTags(string $text): string
    {
        $text = preg_replace('/([^\\\\]|^)([<>])/', '$1\\\\$2', $text);

        if (str_ends_with($text, '\\')) {
            $len = \strlen($text);
            $text = rtrim($text, '\\');
            $text = str_replace("\0", '', $text);
            $text .= str_repeat("\0", $len - \strlen($text));
        }

        return $text;
    }

    public function setStyle(string $name, StyleInterface $style): self
    {
        $this->styles[strtolower($name)] = $style;

        return $this;
    }

    public function hasStyle(string $name): bool
    {
        return isset($this->styles[strtolower($name)]);
    }

    public function getStyle(string $name): ?StyleInterface
    {
        return $this->styles[strtolower($name)] ?? null;
    }

    public function decorate(bool $decorated = true): self
    {
        $this->decorated = $decorated;

        return $this;
    }

    public function isDecorated(): bool
    {
        return $this->decorated;
    }

    public function setDecorated(bool $decorated): self
    {
        $this->decorated = $decorated;

        return $this;
    }

    public function format(?string $message): string
    {
        return $this->wrap($message, 0);
    }

    public function wrap(?string $message, int $width): string
    {
        if (null === $message) {
            return '';
        }

        $offset = 0;
        $output = '';
        $openTagRegex = '[a-z](?:[^\\\\<>]*+ | \\\\.)*';
        $closeTagRegex = '[a-z][^<>]*+';
        $currentLineLength = 0;

        preg_match_all("#<(($openTagRegex) | /($closeTagRegex)?)>#ix", $message, $matches, \PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as $i => $match) {
            [$text, $pos] = $match;

            if (0 !== $pos && '\\' === $message[$pos - 1]) {
                continue;
            }

            // add the text up to the next tag
            $output .= $this->apply(substr($message, $offset, $pos - $offset), $output, $width, $currentLineLength);
            $offset = $pos + \strlen($text);

            // opening tag?
            if ($open = ('/' !== $text[1])) {
                $tag = $matches[1][$i][0];
            } else {
                $tag = $matches[3][$i][0] ?? '';
            }

            if (!$open && !$tag) {
                // </>
                $this->stack->pop();
            } elseif (null === $style = $this->createStyle($tag)) {
                $output .= $this->apply($text, $output, $width, $currentLineLength);
            } elseif ($open) {
                $this->stack->push($style);
            } else {
                $this->stack->pop();
            }
        }

        $output .= $this->apply(substr($message, $offset), $output, $width, $currentLineLength);

        return strtr($output, ["\0" => '\\', '\\<' => '<', '\\>' => '>']);
    }

    protected function createStyle(string $string): ?StyleInterface
    {
        if (isset($this->styles[$string])) {
            return $this->styles[$string];
        }

        if (!preg_match_all('/([^=]+)=([^;]+)(;|$)/', $string, $matches, \PREG_SET_ORDER)) {
            return null;
        }

        $style = new Style();

        foreach ($matches as $match) {
            array_shift($match);
            $match[0] = strtolower($match[0]);

            if ('fg' === $match[0]) {
                $style->setForeground(strtolower($match[1]));
            } elseif ('bg' === $match[0]) {
                $style->setBackground(strtolower($match[1]));
            } elseif ('href' === $match[0]) {
                $url = preg_replace('{\\\\([<>])}', '$1', $match[1]);

                $style->setLink($url);
            } elseif ('options' === $match[0]) {
                preg_match_all('([^,;]+)', strtolower($match[1]), $options);

                $options = array_shift($options);

                foreach ($options as $option) {
                    $style->setOption($option);
                }
            } else {
                return null;
            }
        }

        return $style;
    }

    protected function apply(string $text, string $current, int $width, int $currentLineLength): string
    {
        if ('' === $text) {
            return '';
        }

        if (!$width) {
            return $this->isDecorated() ? $this->stack->current()->apply($text) : $text;
        }

        if (!$currentLineLength && '' !== $current) {
            $text = ltrim($text);
        }

        if ($currentLineLength) {
            $prefix = substr($text, 0, $i = $width - $currentLineLength)."\n";
            $text = substr($text, $i);
        } else {
            $prefix = '';
        }

        preg_match('~(\\n)$~', $text, $matches);

        $text = $prefix.preg_replace('~([^\\n]{'.$width.'})\\ *~', "\$1\n", $text);
        $text = rtrim($text, "\n").($matches[1] ?? '');

        if (!$currentLineLength && '' !== $current && "\n" !== substr($current, -1)) {
            $text = "\n".$text;
        }

        $lines = explode("\n", $text);

        foreach ($lines as $line) {
            $currentLineLength += \strlen($line);
            if ($width <= $currentLineLength) {
                $currentLineLength = 0;
            }
        }

        if ($this->isDecorated()) {
            foreach ($lines as $i => $line) {
                $lines[$i] = $this->stack->current()->apply($line);
            }
        }

        return implode("\n", $lines);
    }

    public static function substr(?string $string, int $from, int $length = null): string
    {
        $string ?? $string = '';

        if (false === $encoding = mb_detect_encoding($string, null, true)) {
            return substr($string, $from, $length);
        }

        return mb_substr($string, $from, $length, $encoding);
    }

    public static function time(int $seconds): string
    {
        static $timeFormats = [
            [0, '< 1 sec'],
            [1, '1 sec'],
            [2, 'secs', 1],
            [60, '1 min'],
            [120, 'mins', 60],
            [3600, '1 hr'],
            [7200, 'hrs', 3600],
            [86400, '1 day'],
            [172800, 'days', 86400],
        ];

        foreach ($timeFormats as $index => $format) {
            if ($seconds >= $format[0]) {
                if ((isset($timeFormats[$index + 1]) && $seconds < $timeFormats[$index + 1][0])
                    || $index == \count($timeFormats) - 1
                ) {
                    if (2 == \count($format)) {
                        return $format[1];
                    }

                    return floor($seconds / $format[2]).' '.$format[1];
                }
            }
        }

        return $seconds;
    }

    public static function memory(int $memory): string
    {
        if ($memory >= 1024 * 1024 * 1024) {
            return sprintf('%.1f GiB', $memory / 1024 / 1024 / 1024);
        }

        if ($memory >= 1024 * 1024) {
            return sprintf('%.1f MiB', $memory / 1024 / 1024);
        }

        if ($memory >= 1024) {
            return sprintf('%d KiB', $memory / 1024);
        }

        return sprintf('%d B', $memory);
    }

    public function section(string $section, string $message, string $style = 'info'): string
    {
        return sprintf('<%s>[%s]</%s> %s', $style, $section, $style, $message);
    }

    public function block($messages, string $style, bool $large = false): string
    {
        if (!\is_array($messages)) {
            $messages = [$messages];
        }

        $len = 0;
        $lines = [];
        foreach ($messages as $message) {
            $message = self::escape($message);
            $lines[] = sprintf($large ? '  %s  ' : ' %s ', $message);
            $len = max(self::width($message) + ($large ? 4 : 2), $len);
        }

        $messages = $large ? [str_repeat(' ', $len)] : [];
        for ($i = 0; isset($lines[$i]); ++$i) {
            $messages[] = $lines[$i].str_repeat(' ', $len - self::width($lines[$i]));
        }
        if ($large) {
            $messages[] = str_repeat(' ', $len);
        }

        for ($i = 0; isset($messages[$i]); ++$i) {
            $messages[$i] = sprintf('<%s>%s</%s>', $style, $messages[$i], $style);
        }

        return implode("\n", $messages);
    }

    /**
     * Truncates a message to the given length.
     *
     * @return string
     */
    public function truncate(string $message, int $length, string $suffix = '...'): string
    {
        $computedLength = $length - self::width($suffix);

        if ($computedLength > self::width($message)) {
            return $message;
        }

        return self::substr($message, 0, $length) . $suffix;
    }

}
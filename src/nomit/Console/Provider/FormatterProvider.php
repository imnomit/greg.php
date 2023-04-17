<?php

namespace nomit\Console\Provider;

use nomit\Console\Format\Formatter;
use nomit\Console\Format\FormatterInterface;

class FormatterProvider extends Provider
{

    protected FormatterInterface $formatter;

    public function __construct(FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function __call(string $method, array $arguments)
    {
        if(method_exists($method, $method)) {
            return call_user_func_array([$this->formatter, $method], $arguments);
        }

        return null;
    }

    /**
     * Formats a message within a section.
     */
    public function section(string $section, string $message, string $style = 'info'): string
    {
        return sprintf('<%s>[%s]</%s> %s', $style, $section, $style, $message);
    }

    /**
     * Formats a message as a block of text.
     */
    public function block(string|array $messages, string $style, bool $large = false): string
    {
        if (!\is_array($messages)) {
            $messages = [$messages];
        }

        $len = 0;
        $lines = [];
        foreach ($messages as $message) {
            $message = $this->formatescape($message);
            $lines[] = sprintf($large ? '  %s  ' : ' %s ', $message);
            $len = max($this->width($message) + ($large ? 4 : 2), $len);
        }

        $messages = $large ? [str_repeat(' ', $len)] : [];
        for ($i = 0; isset($lines[$i]); ++$i) {
            $messages[] = $lines[$i].str_repeat(' ', $len - $this->width($lines[$i]));
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
     */
    public function truncate(string $message, int $length, string $suffix = '...'): string
    {
        $computedLength = $length - $this->width($suffix);

        if ($computedLength > $this->width($message)) {
            return $message;
        }

        return $this->substr($message, 0, $length).$suffix;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'format';
    }

}
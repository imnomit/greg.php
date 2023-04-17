<?php

namespace nomit\Console\Shell;

class LinuxShell extends AbstractShell
{

    public function getWidth(): ?int
    {
        return $this->getDimension($this->tput('cols'));
    }

    public function getHeight(): ?int
    {
        return $this->getDimension($this->tput('lines'));
    }

    protected function tput(string $type): array|null|string
    {
        return $this->execute("tput {$type} 2>/dev/null");
    }

    public function canAccessBash(): bool
    {
        return (rtrim($this->execute("/usr/bin/env bash -c 'echo OK'")) === 'OK');
    }

    public function getHiddenResponsePrompt(string $prompt): string
    {
        $bash_command = 'read -s -p "' . $prompt . '" response && echo $output';

        return rtrim($this->execute("/usr/bin/env bash -c '{$bash_command}'"));
    }

    protected function getDimension(int|string|null $dimension): ?int
    {
        return (!is_nan($dimension)) ? $dimension : null;
    }

    public function supportsAnsi(): bool
    {
        if ('Hyper' === getenv('TERM_PROGRAM')) {
            return true;
        }

        # If we're running in a web context then we can't use stdout
        if (!defined('STDOUT')) {
            return false;
        }

        $stream = \STDOUT;

        if (function_exists('stream_isatty')) {
            return @stream_isatty($stream);
        }

        if (function_exists('posix_isatty')) {
            return @posix_isatty($stream);
        }

        $stat = @fstat($stream);

        // Check if formatted mode is S_IFCHR
        return $stat && 0020000 === ($stat['mode'] & 0170000);
    }

}
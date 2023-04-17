<?php

namespace nomit\Resource;

use nomit\Resource\Version\VersionDifferentiatorTrait;
use nomit\Resource\Version\VersionInterface;
use nomit\Utility\Object\SmartObjectTrait;

abstract class AbstractResource implements ResourceInterface
{

    use SmartObjectTrait,
        VersionDifferentiatorTrait;

    public function __construct(
        protected VersionInterface $version
    )
    {
    }

    public function setVersion(VersionInterface $version): ResourceInterface
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion(): VersionInterface
    {
        return $this->version;
    }

    public function interpolate(string $path): string
    {
        return $this->version->interpolate($path);
    }

    protected function minifyStylesheet(string $string): string
    {
        $last = '';

        return preg_replace_callback(
            <<<'XX'
				(
					(^
						|'(?:\\.|[^\n'\\])*'
						|"(?:\\.|[^\n"\\])*"
						|([0-9A-Za-z_*#.%:()[\]-]+)
						|.
					)(?:\s|/\*(?:[^*]|\*(?!/))*+\*/)* # optional space
				())sx
				XX,
            function ($match) use (&$last) {
                [, $result, $word] = $match;
                if ($last === ';') {
                    $result = $result === '}' ? '}' : ';' . $result;
                    $last = '';
                }

                if ($word !== '') {
                    $result = ($last === 'word' ? ' ' : '') . $result;
                    $last = 'word';
                } elseif ($result === ';') {
                    $last = ';';
                    $result = '';
                } else {
                    $last = '';
                }

                return $result;
            },
            $string . "\n",
        );
    }

    protected function minifyScript(string $string): string
    {
        $last = '';

        return preg_replace_callback(
            <<<'XX'
				(
					(?:
						(^|[-+\([{}=,:;!%^&*|?~]|/(?![/*])|return|throw) # context before regexp
						(?:\s|//[^\n]*+\n|/\*(?:[^*]|\*(?!/))*+\*/)* # optional space
						(/(?![/*])(?:\\[^\n]|[^[\n/\\]|\[(?:\\[^\n]|[^]])++)+/) # regexp
						|(^
							|'(?:\\.|[^\n'\\])*'
							|"(?:\\.|[^\n"\\])*"
							|([0-9A-Za-z_$]+)
							|([-+]+)
							|.
						)
					)(?:\s|//[^\n]*+\n|/\*(?:[^*]|\*(?!/))*+\*/)* # optional space
				())sx
				XX,
            function ($match) use (&$last) {
                [, $context, $regexp, $result, $word, $operator] = $match;
                if ($word !== '') {
                    $result = ($last === 'word' ? ' ' : ($last === 'return' ? ' ' : '')) . $result;
                    $last = ($word === 'return' || $word === 'throw' || $word === 'break' ? 'return' : 'word');
                } elseif ($operator) {
                    $result = ($last === $operator[0] ? ' ' : '') . $result;
                    $last = $operator[0];
                } else {
                    if ($regexp) {
                        $result = $context . ($context === '/' ? ' ' : '') . $regexp;
                    }

                    $last = '';
                }

                return $result;
            },
            $string . "\n",
        );
    }

    protected function normalizeFileName(string $fileName): string
    {
        $fileName = substr($fileName, 0, 32);
        $fileName = str_replace(['.', '/'], '', $fileName);
        $fileName = trim($fileName);

        return $fileName;
    }

}
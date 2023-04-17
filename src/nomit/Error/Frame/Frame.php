<?php

namespace nomit\Error\Frame;

use nomit\Exception\InvalidArgumentException;
use nomit\Utility\Concern\Arrayable;
use nomit\Utility\Concern\Serializable;

class Frame implements FrameInterface
{

    private ?string $fileContentsCache = null;

    private array $comments = [];

    public function __construct(
        private array $frame
    )
    {
    }

    public function getFile(): ?string
    {
        if (empty($this->frame['file'])) {
            return null;
        }

        $file = $this->frame['file'];

        // Check if this frame occurred within an eval().
        // @todo: This can be made more reliable by checking if we've entered
        // eval() in a previous trace, but will need some more work on the upper
        // trace collector(s).
        if (preg_match('/^(.*)\((\d+)\) : (?:eval\(\)\'d|assert) code$/', $file, $matches)) {
            $file = $this->frame['file'] = $matches[1];
            $this->frame['line'] = (int)$matches[2];
        }

        return $file;
    }

    public function getLine(): ?int
    {
        return isset($this->frame['line']) ? $this->frame['line'] : null;
    }

    public function getClass(): ?string
    {
        return isset($this->frame['class']) ? $this->frame['class'] : null;
    }

    public function getFunction(): ?string
    {
        return isset($this->frame['function']) ? $this->frame['function'] : null;
    }

    public function getArguments(): array
    {
        $arrayArguments = [];
        $arguments = $this->frame['args'] ?? [];

        foreach($arguments as $argument) {
            if(is_object($argument) && $argument instanceof Arrayable) {
                $argument = $argument->toArray();
            }

            $arrayArguments[] = $argument;
        }

        return $arrayArguments;
    }

    public function getFileContents(string $fileName = null, int $line = null, int $sourceContext = 10): ?string
    {
        if(!$fileName) {
            $fileName = $this->getFile();
        }

        if(!$line) {
            $line = $this->getLine();
        }

        if ($this->fileContentsCache === null) {
            if (!$fileName || $fileName === "Unknown" || !is_file($fileName)) {
                return null;
            }

            $content = file_get_contents($fileName);
            $content = explode("\n", $content);
            $lines = [];

            if (0 > $sourceContext) {
                $sourceContext = \count($content);
            }

            for ($i = max($line - $sourceContext, 1), $max = min($line + $sourceContext, \count($content)); $i <= $max; ++$i) {
                $lines[] = $content[$i - 1];
            }

            $this->fileContentsCache = implode("\n", $lines);
        }

        return $this->fileContentsCache;
    }

    public function addComment(string $comment, string $context = 'global'): FrameInterface
    {
        $this->comments[] = [
            'comment' => $comment,
            'context' => $context,
        ];

        return $this;
    }

    public function getComments(string $filter = null): array
    {
        $comments = $this->comments;

        if ($filter !== null) {
            $comments = array_filter($comments, function ($c) use ($filter) {
                return $c['context'] == $filter;
            });
        }

        return $comments;
    }

    public function getRawFrame(): array
    {
        return $this->frame;
    }

    public function equals(FrameInterface $frame): bool
    {
        if (!$this->getFile() || $this->getFile() === 'Unknown' || !$this->getLine()) {
            return false;
        }

        return $frame->getFile() === $this->getFile() && $frame->getLine() === $this->getLine();
    }

    public function toArray(): array
    {
        return [
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'class' => $this->getClass(),
            'function' => $this->getFunction(),
            'arguments' => $this->getArguments(),
            'comments' => $this->getComments()
        ];
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR | $options);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toJson();
    }

    public function serialize(): string
    {
        return serialize($this->frame);
    }

    public function unserialize(string $payload): ?self
    {
        return unserialize($payload);
    }

    public function toString(): string
    {
        return $this->getClass() . '::' . $this->getFunction() . '()';
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}
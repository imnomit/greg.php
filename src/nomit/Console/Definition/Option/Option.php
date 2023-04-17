<?php

namespace nomit\Console\Definition\Option;

use nomit\Console\Completion\CompletionInput;
use nomit\Console\Completion\CompletionInputInterface;
use nomit\Console\Completion\CompletionSuggestionsInterface;
use nomit\Console\Exception\LogicException;

class Option implements OptionInterface
{

    protected string $name;

    protected ?string $shortcut = null;

    protected ?string $description;

    protected ?int $mode;

    protected bool $is_required;

    protected mixed $default = null;

    protected mixed $value = null;

    protected \Closure|array $suggested_values;

    public function __construct(string $name, string|array $shortcut = null, int $mode = null, string $description = null,
                                string|bool|int|float|array $default = null, array|\Closure $suggestedValues = []
    )
    {
        if (str_starts_with($name, '--')) {
            $name = substr($name, 2);
        }

        if (empty($name)) {
            throw new \InvalidArgumentException('A console option\'s name cannot be left empty.');
        }

        if (empty($shortcut)) {
            $shortcut = null;
        }

        if (null !== $shortcut) {
            if (\is_array($shortcut)) {
                $shortcut = implode('|', $shortcut);
            }

            $shortcuts = preg_split('{(\|)-?}', ltrim($shortcut, '-'));
            $shortcuts = array_filter($shortcuts);
            $shortcut = implode('|', $shortcuts);

            if (empty($shortcut)) {
                throw new \InvalidArgumentException('A console option\'s shortcut, when defined, cannot be empty.');
            }
        }

        if (null === $mode) {
            $mode = self::VALUE_NONE;
        }

        if ($mode >= (self::VALUE_NEGATABLE << 1) || $mode < 1) {
            throw new \InvalidArgumentException(sprintf('The supplied console option mode, "%s", is invalid.', $mode));
        }

        $this->name = $name;
        $this->shortcut = $shortcut;
        $this->mode = $mode;
        $this->description = $description;

        if ($suggestedValues && !$this->acceptsValue()) {
            throw new LogicException('Suggested values cannot be assigned to the option if the it does not accept a value.');
        }

        $this->suggested_values = $suggestedValues;

        if ($this->isArray() && !$this->acceptsValue()) {
            throw new \InvalidArgumentException('It is impossible for a console option to have a mode of "VALUE_IS_ARRAY" if the option does not accept a value.');
        }

        if ($this->isNegatable() && $this->acceptsValue()) {
            throw new \InvalidArgumentException('It is impossible for a console option to have a mode of "VALUE_NEGATABLE" if the option also accepts a value.');
        }

        $this->setDefault($default);
    }

    public function setName(string $name): OptionInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(string $description): OptionInterface
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setValue(mixed $value): OptionInterface
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): mixed
    {
        $value = $this->value;

        if(!$value && $this->isValueRequired()) {
            throw new \RuntimeException(sprintf('The console option named "%s" is marked as required, and so therefore must be assigned a value.', $this->getName()));
        }

        return $value ?? $this->getDefault();
    }

    public function setDefault(mixed $default): self
    {
        if (self::VALUE_NONE === (self::VALUE_NONE & $this->mode) && null !== $default) {
            throw new \LogicException('A default value cannot be set when the console option is using the "InputOption::VALUE_NONE" mode.');
        }

        if ($this->isArray()) {
            if (null === $default) {
                $default = [];
            } elseif (!\is_array($default)) {
                throw new \LogicException(sprintf('A default value for a console option array parameter must be typed as an array: instead, a "%s"-typed value was given.', get_debug_type($default)));
            }
        }

        $this->default = $this->acceptsValue() || $this->isNegatable() ? $default : false;

        return $this;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function requireValue(): OptionInterface
    {
        $this->mode = self::VALUE_REQUIRED;

        return $this;
    }

    public function optionalValue(mixed $default = null): OptionInterface
    {
        $this->mode = self::VALUE_OPTIONAL;

        $this->setDefault($default);

        return $this;
    }

    public function isValueRequired(): bool
    {
        return self::VALUE_REQUIRED === (self::VALUE_REQUIRED & $this->mode);
    }

    public function isValueOptional(): bool
    {
        return self::VALUE_OPTIONAL === (self::VALUE_OPTIONAL & $this->mode);
    }

    public function isArray(): bool
    {
        return self::VALUE_ARRAY === (self::VALUE_ARRAY & $this->mode);
    }

    public function acceptsValue(): bool
    {
        return $this->isValueRequired() || $this->isValueOptional() || $this->isArray();
    }

    public function isNegatable(): bool
    {
        return self::VALUE_NEGATABLE === (self::VALUE_NEGATABLE & $this->mode);
    }

    public function setShortcut(string $shortcut): self
    {
        $this->shortcut = $shortcut;

        return $this;
    }

    public function getShortcut(): ?string
    {
        return $this->shortcut;
    }

    public function hasShortcut(): bool
    {
        return $this->shortcut !== null;
    }

    public function isInteger(): bool
    {
        return is_int($this->getValue());
    }

    public function isFloat(): bool
    {
        return is_float($this->getValue());
    }

    public function isString(): bool
    {
        return is_string($this->getValue());
    }

    public function hasCompletion(): bool
    {
        return [] !== $this->suggested_values;
    }

    /**
     * Adds suggestions to $suggestions for the current completion input.
     *
     * @see Command::complete()
     */
    public function complete(CompletionInputInterface $input, CompletionSuggestionsInterface $suggestions): void
    {
        $values = $this->suggested_values;

        if ($values instanceof \Closure && !\is_array($values = $values($input))) {
            throw new LogicException(sprintf('Closure for option "%s" must return an array. Got "%s".', $this->name, get_debug_type($values)));
        }

        if ($values) {
            $suggestions->suggestValues($values);
        }
    }

    public function equals(OptionInterface $option): bool
    {
        return $option->getName() === $this->getName()
            && $option->getShortcut() === $this->getShortcut()
            && $option->getDefault() === $this->getDefault()
            && $option->isNegatable() === $this->isNegatable()
            && $option->isArray() === $this->isArray()
            && $option->isValueRequired() === $this->isValueRequired()
            && $option->isValueOptional() === $this->isValueOptional()
        ;
    }

}
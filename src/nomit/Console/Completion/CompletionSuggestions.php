<?php

namespace nomit\Console\Completion;

use nomit\Console\Definition\Option\OptionInterface;

class CompletionSuggestions implements CompletionSuggestionsInterface
{

    protected array $value_suggestions = [];

    protected array $option_suggestions = [];

    /**
     * Add a suggested value for an input option or argument.
     *
     * @return $this
     */
    public function suggestValue(string|SuggestionInterface $value): static
    {
        $this->value_suggestions[] = !$value instanceof SuggestionInterface ? new Suggestion($value) : $value;

        return $this;
    }

    /**
     * Add multiple suggested values at once for an input option or argument.
     *
     * @param array<string|Suggestion> $values
     *
     * @return $this
     */
    public function suggestValues(array $values): static
    {
        foreach ($values as $value) {
            $this->suggestValue($value);
        }

        return $this;
    }

    /**
     * Add a suggestion for an input option name.
     *
     * @return $this
     */
    public function suggestOption(OptionInterface $option): static
    {
        $this->option_suggestions[] = $option;

        return $this;
    }

    /**
     * Add multiple suggestions for input option names at once.
     *
     * @param OptionInterface[] $options
     *
     * @return $this
     */
    public function suggestOptions(array $options): static
    {
        foreach ($options as $option) {
            $this->suggestOption($option);
        }

        return $this;
    }

    /**
     * @return OptionInterface[]
     */
    public function getOptionSuggestions(): array
    {
        return $this->option_suggestions;
    }

    /**
     * @return Suggestion[]
     */
    public function getValueSuggestions(): array
    {
        return $this->value_suggestions;
    }

}
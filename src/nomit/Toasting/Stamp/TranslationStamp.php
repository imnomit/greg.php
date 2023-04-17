<?php

namespace nomit\Toasting\Stamp;

final class TranslationStamp extends AbstractStamp
{

    private array $parameters;

    private ?string $locale;

    public static function parametersOrder(array $parameters = [], string $locale = null)
    {
        return [
            'parameters' => $parameters,
            'locale' => $locale
        ];
    }

    public function __construct(array $parameters = [], string $locale = null)
    {
        $order = self::parametersOrder($parameters, $locale);
        $parameters = $order['parameters'];
        $locale = $order['locale'];

        $this->parameters = $parameters;
        $this->locale = $locale;
    }

    /**
     * @return array|mixed
     */
    public function getParameters(): mixed
    {
        return $this->parameters;
    }

    /**
     * @return mixed|string|null
     */
    public function getLocale(): mixed
    {
        return $this->locale;
    }

    public function toArray(): array
    {
        return [
            'parameters' => $this->getParameters(),
            'locale' => $this->getLocale()
        ];
    }

}
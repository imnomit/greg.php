<?php

namespace nomit\Security\Csrf\Token\Generator;

use nomit\Cryptography\Entropy\EntropyFactory;
use nomit\Cryptography\Entropy\Mixer\McryptRijndael128;

class UriSafeTokenGenerator implements TokenGeneratorInterface
{

    public function __construct(
        private int $entropy = 256
    )
    {
    }

    public function generateToken(): string
    {
        $factory = new EntropyFactory();
        $factory->registerMixer('McryptRijndael128', McryptRijndael128::class);

        $generator = $factory->getMediumStrengthGenerator();
        $key = $generator->generate($this->entropy / 8);

        // Generate an URI safe base64 encoded string that does not contain "+",
        // "/" or "=" which need to be URL encoded and make URLs unnecessarily
        // longer.
        return rtrim(strtr(base64_encode($key), '+/', '-_'), '=');
    }

}
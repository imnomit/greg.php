<?php

namespace nomit\Security\Session\Token\Identity;

use nomit\Cryptography\Entropy\EntropyFactory;
use nomit\Cryptography\Hasher\HasherInterface;
use nomit\Security\Session\Fingerprint\FingerprintGeneratorInterface;
use nomit\Web\Request\RequestInterface;

class FingerprintIdentityGenerator implements IdentityGeneratorInterface
{

    public function __construct(
        private FingerprintGeneratorInterface $fingerprintGenerator,
        private HasherInterface $hasher,
        private EntropyFactory $entropyFactory,
    )
    {
    }

    public function generate(RequestInterface $request): string
    {
        $fingerprint = $this->fingerprintGenerator->generate($request);
        $entropy = $this->entropyFactory->getMediumStrengthGenerator()->generate(32);
        $seed = microtime();
        $hash = $this->hasher->make($fingerprint . $entropy . $seed);

        return str_replace(['/', '\\'], '', rawurlencode(base64_encode($hash)));
    }

}
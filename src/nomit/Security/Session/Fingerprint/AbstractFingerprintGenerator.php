<?php

namespace nomit\Security\Session\Fingerprint;

use nomit\Cryptography\Hasher\HasherInterface;
use nomit\Cryptography\Hasher\Sha256Hasher;

abstract class AbstractFingerprintGenerator implements FingerprintGeneratorInterface
{

    protected HasherInterface $hasher;

    /**
     * AbstractRequestFingerprintGenerator constructor.
     * @param HasherInterface|null $hasher
     */
    public function __construct(HasherInterface $hasher = null)
    {
        if(!$hasher) {
            $hasher = new Sha256Hasher();
        }

        $this->setHasher($hasher);
    }

    /**
     * @param HasherInterface $hasher
     * @return $this
     */
    public function setHasher(HasherInterface $hasher): self
    {
        $this->hasher = $hasher;

        return $this;
    }

    /**
     * @return HasherInterface
     */
    public function getHasher(): HasherInterface
    {
        return $this->hasher;
    }

}
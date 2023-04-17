<?php

namespace nomit\Toasting\Stamp;

use nomit\Toasting\Envelope\EnvelopeInterface;

final class UuidStamp extends AbstractStamp
{

    private string $uuid;

    public static function indexByUuid($envelopes): array
    {
        /**
         * @var EnvelopeInterface[]
         */
        $envelopes = \is_array($envelopes) ? $envelopes : \func_get_args();
        $map = array();

        foreach ($envelopes as $envelope) {
            $uuidStamp = $envelope->get(self::class);

            if(!$uuidStamp instanceof self) {
                $uuidStamp = new UuidStamp(spl_object_hash($envelope));
                $envelope->stamp($uuidStamp);
            }

            $uuid = $uuidStamp->getUuid();
            $map[$uuid] = $envelope;
        }

        return $map;
    }

    public function __construct(string $uuid = null)
    {
        $this->uuid = $uuid ?: sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->getUuid()
        ];
    }

}
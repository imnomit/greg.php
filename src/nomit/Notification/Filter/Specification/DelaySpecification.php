<?php

namespace nomit\Notification\Filter\Specification;

use nomit\Notification\Notification\NotificationInterface;
use nomit\Notification\Stamp\DelayStamp;
use Psr\Log\LoggerInterface;

final class DelaySpecification implements SpecificationInterface
{

    public function __construct(
        private int $minimumDelay,
        private ?int $maximumDelay = null,
        private ?LoggerInterface $logger = null,
    )
    {
    }

    public function isSatisfiedBy(NotificationInterface $notification): bool
    {
        $stamp = $notification->last(DelayStamp::class);

        if(!$stamp instanceof DelayStamp) {
            $this->logger?->warning('The handled stamp is missing the {delayStamp} stamp, and so cannot be evaluated by the {specification} filter specification.');

            return false;
        }

        if(null !== $this->maximumDelay && $stamp->getDelay() > $this->maximumDelay) {
            $this->logger?->notice('The handled notification has failed to pass the {specification} filter specification because its delay value, {delay}, is greater than the maximum delay value, {maximumDelay}.', [
                'specification' => get_class($this),
                'delay' => $stamp->getDelay(),
                'maximumDelay' => $this->maximumDelay,
                'notification' => $notification->toArray()
            ]);

            return false;
        }

        $result = $stamp->getDelay() >= $this->minimumDelay;

        if($result) {
            $this->logger?->notice('The handled notification has successfully passed the {specification} filter specification because its delay value, {delay}, is greater than or equal to the minimum delay value, {minimumDelay}.', [
                'specification' => get_class($this),
                'delay' => $stamp->getDelay(),
                'minimumDelay' => $this->minimumDelay,
                'notification' => $notification->toArray()
            ]);
        } else {
            $this->logger?->notice('The handled notification has failed to pass the {specification} filter specification because its delay value, {delay}, is less than the minimum delay value, {minimumDelay}.', [
                'specification' => get_class($this),
                'delay' => $stamp->getDelay(),
                'minimumDelay' => $this->minimumDelay,
                'notification' => $notification->toArray()
            ]);
        }

        return $result;
    }

}
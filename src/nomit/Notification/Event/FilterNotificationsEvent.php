<?php

namespace nomit\Notification\Event;

use nomit\EventDispatcher\Event;
use nomit\Notification\Filter\Filter;
use nomit\Notification\Filter\FilterInterface;
use nomit\Security\User\UserInterface;
use Psr\Log\LoggerInterface;

final class FilterNotificationsEvent extends Event
{

    private FilterInterface $filter;

    public function __construct(
        array $notifications,
        array $criteria,
        private UserInterface $user,
        private ?LoggerInterface $logger = null
    )
    {
        $this->filter = new Filter($notifications, $criteria, $this->logger);
    }

    public function getNotifications(): array
    {
        return $this->filter->getResult();
    }

    /**
     * @param Filter|FilterInterface $filter
     */
    public function setFilter(FilterInterface|Filter $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * @return Filter|FilterInterface
     */
    public function getFilter(): FilterInterface|Filter
    {
        return $this->filter;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

}
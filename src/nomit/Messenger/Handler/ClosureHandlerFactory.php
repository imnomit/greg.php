<?php

namespace nomit\Messenger\Handler;

class ClosureHandlerFactory implements HandlerFactoryInterface
{

    public function supports(mixed $receiver): bool
    {
        return $receiver instanceof \Closure;
    }

    public function factory(mixed $receiver): ?HandlerInterface
    {
        return $this->supports($receiver)
            ? new ClosureHandler($receiver)
            : null;
    }

}
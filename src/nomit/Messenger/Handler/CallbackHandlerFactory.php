<?php

namespace nomit\Messenger\Handler;

class CallbackHandlerFactory implements HandlerFactoryInterface
{

    public function supports(mixed $receiver): bool
    {
        if(is_callable($receiver)) {
            return true;
        }

        if(is_string($receiver)) {
            $receiver = explode(CallbackHandler::CALLBACK_DELIMITER, $job);
        }

        if(count($receiver) < 2) {
            return false;
        }

        [$object, $method] = $receiver;

        if(!method_exists($object, $method)) {
            return false;
        }

        return is_callable([$object, $method]);
    }

    public function factory(mixed $receiver): ?HandlerInterface
    {
        return $this->supports($receiver)
            ? new CallbackHandler($receiver)
            : null;
    }

}
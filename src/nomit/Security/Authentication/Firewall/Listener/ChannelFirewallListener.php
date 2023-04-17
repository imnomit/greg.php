<?php

namespace nomit\Security\Authentication\Firewall\Listener;

use nomit\Kernel\Event\RequestEvent;
use nomit\Security\Authentication\EntryPoint\EntryPointInterface;
use nomit\Security\Authorization\AccessMapInterface;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Response\RedirectResponse;
use Psr\Log\LoggerInterface;

final class ChannelFirewallListener extends AbstractFirewallListener
{

    private ?EntryPointInterface $entryPoint = null;

    public function __construct(
        private AccessMapInterface $map,
        private ?LoggerInterface $logger = null,
        private int $httpPort = 80,
        private int $httpsPort = 443
    )
    {
        if($logger instanceof EntryPointInterface) {
            $this->entryPoint = $logger;
            $argumentsNumber = func_num_args();
            $logger = $argumentsNumber > 2 ? func_get_arg(2) : null;

            if(null !== $logger && !$logger instanceof LoggerInterface) {
                throw new \TypeError(sprintf('The "$logger" argument of "%s()" must be an instance of "%s": instead, a "%s"-typed object was given.', __METHOD__, LoggerInterface::class, get_debug_type($logger)));
            }

            $this->logger = $logger;
            $this->httpPort = $argumentsNumber > 3 ? func_get_arg(3) : 80;
            $this->httpsPort = $argumentsNumber > 4 ? func_get_arg(4) : 443;
        }
    }

    public function supports(RequestInterface $request): ?bool
    {
        [, $channel] = $this->map->getPatterns($request);

        if('https' === $channel && !$request->isSecure()) {
            if(null !== $this->logger) {
                if('https' === $request->headers->get('X-Forwarded-Proto')) {
                    $this->logger->info('The client is being redirected to the HTTPS version of the requested resource. Note that the "X-Forwarded-Proto" header has been set to "https": has "trusted_proxies" been correctly configured?');
                } else if(str_contains($request->headers->get('Forwarded', ''), 'proto=https')) {
                    $this->logger->info('The client is being redirected to the HTTPS version of the requested resource. Note that the "Forwarded" header has been set to "proto=https": has "trusted_proxies" been correctly configured?');
                } else {
                    $this->logger->info('The client is being redirected to the HTTPS version of the requested resource.');
                }
            }

            return true;
        }

        if('http' === $channel && $request->isSecure()) {
            if(null !== $this->logger) {
                $this->logger->info('The client is being redirected to the HTTP version of the requested resource.');
            }

            return true;
        }

        return false;
    }

    public function authenticate(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $event->setResponse(
            $this->createRedirectResponse($request)

        );
    }

    private function createRedirectResponse(RequestInterface $request): RedirectResponse
    {
        if(null !== $this->entryPoint) {
            return $this->entryPoint->respond($request);
        }

        $scheme = $request->isSecure() ? 'http' : 'https';

        if('http' === $scheme && 80 !== $this->httpPort) {
            $port = ':' . $this->httpPort;
        } else if('https' === $scheme && 443 !== $this->httpsPort) {
            $port = ':' . $this->httpsPort;
        } else {
            $port = '';
        }

        $queryString = $request->getQueryString();

        if(null !== $queryString) {
            $queryString = '?' . $queryString;
        }

        $url = $scheme . '://' . $request->getHost() . $port . $request->getBaseUrl() . $request->getPathInfo() . $queryString;

        return new RedirectResponse($url, 301);
    }

}
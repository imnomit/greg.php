<?php

namespace nomit\Web\DependencyInjection;

use nomit\DependencyInjection\CompilerExtension;
use nomit\DependencyInjection\ContainerBuilder;
use nomit\Kernel\Event\ResponseEvent;
use nomit\Kernel\EventListener\ResponseEventListener;
use nomit\Schema\Expect;
use nomit\Web\Request\Request;
use nomit\Web\Request\RequestFactory;
use nomit\Web\Request\RequestInterface;
use nomit\Web\Request\RequestStack;
use nomit\Web\Response\Response;

final class WebExtension extends CompilerExtension
{

    public function __construct(
        private bool $cliMode = false
    )
    {
    }

    public function getConfigSchema(): \nomit\Schema\Schema
    {
        return Expect::structure([
            'proxy' => Expect::anyOf(Expect::arrayOf('string'), Expect::string()->castTo('array'))->firstIsDefault()->dynamic(),
            'headers' => Expect::arrayOf('scalar|null')->default([
                'X-Powered-By' => 'nomit',
                'Content-Type' => 'text/html; charset=utf-8',
            ])->mergeDefaults(),
            'frames' => Expect::anyOf(Expect::string(), Expect::bool(), null)->default('SAMEORIGIN'), // X-Frame-Options
            'csp' => Expect::arrayOf('array|scalar|null'), // Content-Security-Policy
            'csp_report_only' => Expect::arrayOf('array|scalar|null'), // Content-Security-Policy-Report-Only
            'feature_policy' => Expect::arrayOf('array|scalar|null'), // Feature-Policy
            'cookie_path' => Expect::string(),
            'cookie_domain' => Expect::string(),
            'cookie_secure' => Expect::anyOf('auto', null, true, false)->firstIsDefault(), // Whether the cookie is available only through HTTPS
            'disable_nomit_cookie' => Expect::bool(false), // disables cookie use by Nette,
            'access_control' => Expect::string('*')
        ]);
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->config;

        $this->loadDefinitionsFromConfig(
            $this->loadFromFile(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services.neon')
        );

        $builder->addDefinition($this->prefix('request.factory'))
            ->setFactory(RequestFactory::class)
            ->addSetup('setProxy', [$config->proxy]);

        $request = $builder->addDefinition($this->prefix('request'))
            ->setType(RequestInterface::class)
            ->setFactory([RequestFactory::class, 'createFromGlobals']);

        $builder->addAlias(Request::class, $this->prefix('request'));
        $builder->addAlias('request', $this->prefix('request'));

        $requestStack = $builder->addDefinition($this->prefix('requests'))
            ->setType(RequestStack::class);

        $builder->addAlias('requests', $this->prefix('requests'));

        $this->initialization->addBody('$this->getService(\'web.requests\')->push($this->getService(\'web.request\'));');

        $response = $builder->addDefinition($this->prefix('response'))
            ->setFactory(Response::class);

        if ($config->cookie_path !== null) {
            $response->addSetup('$cookie_path', [$config->cookie_path]);
        }

        if ($config->cookie_domain !== null) {
            $value = $config->cookie_domain === 'domain'
                ? $builder::literal('$this->getService(?)->getUrl()->getDomain(2)', [$request->getName()])
                : $config->cookie_domain;
            $response->addSetup('$cookie_domain', [$value]);
        }

        if ($config->cookie_secure !== null) {
            $value = $config->cookie_secure === 'auto'
                ? $builder::literal('$this->getService(?)->isSecured()', [$request->getName()])
                : $config->cookie_secure;
            $response->addSetup('$cookie_secure', [$value]);
        }

        if ($this->name === 'http') {
            $builder->addAlias('request.factory', $this->prefix('request.factory'));
            $builder->addAlias('request', $this->prefix('request'));
            $builder->addAlias('requests', $this->prefix('requests'));
            $builder->addAlias('response', $this->prefix('response'));
        }

        if (!$this->cliMode) {
            $this->sendHeaders();
        }

        $this->registerResponseEventListener($builder, $config);
        $this->registerAccessControlEventListener($builder, $config);
    }

    private function sendHeaders(): void
    {
        $config = $this->config;
        $headers = array_map('strval', $config->headers);

        if (isset($config->frames) && $config->frames !== true && !isset($headers['X-Frame-Options'])) {
            $frames = $config->frames;

            if ($frames === false) {
                $frames = 'DENY';
            } elseif (preg_match('#^https?:#', $frames)) {
                $frames = "ALLOW-FROM $frames";
            }

            $headers['X-Frame-Options'] = $frames;
        }

        foreach (['csp', 'csp_report_only'] as $key) {
            if (empty($config->$key)) {
                continue;
            }

            $value = self::buildPolicy($config->$key);

            if (str_contains($value, "'nonce'")) {
                $this->initialization->addBody('$cspNonce = base64_encode(random_bytes(16));');
                $value = ContainerBuilder::literal(
                    'str_replace(?, ? . $cspNonce, ?)',
                    ["'nonce", "'nonce-", $value],
                );
            }

            $headers['Content-Security-Policy' . ($key === 'csp' ? '' : '-Report-Only')] = $value;
        }

        if (!empty($config->feature_policy)) {
            $headers['Feature-Policy'] = self::buildPolicy($config->feature_policy);
        }

        $this->initialization->addBody('$response = $this->getService(?);', [$this->prefix('response')]);

        foreach ($headers as $key => $value) {
            if ($value !== '') {
                $this->initialization->addBody('$response->headers->set(?, ?);', [$key, $value]);
            }
        }

        if (!$config->disable_nomit_cookie) {
            $this->initialization->addBody(
                '$response->headers->setCookie(new nomit\Web\Cookie(nomit\Web\Cookie::STRICT_COOKIE_NAME, \'\', 0, \'/\', sameSite: nomit\Web\Response\ResponseInterface::SAME_SITE_STRICT));'
            );
        }
    }

    private static function buildPolicy(array $config): string
    {
        $nonQuoted = ['require-sri-for' => 1, 'sandbox' => 1];
        $value = '';

        foreach ($config as $type => $policy) {
            if ($policy === false) {
                continue;
            }

            $policy = $policy === true ? [] : (array) $policy;
            $value .= $type;

            foreach ($policy as $item) {
                if (is_array($item)) {
                    $item = key($item) . ':';
                }

                $value .= !isset($nonQuoted[$type]) && preg_match('#^[a-z-]+$#D', $item)
                    ? " '$item'"
                    : " $item";
            }

            $value .= '; ';
        }

        return $value;
    }

    private function registerResponseEventListener(ContainerBuilder $container, \stdClass $config): void
    {
        $container->addDefinition($this->prefix('event_listener.response'))
            ->setType(ResponseEventListener::class)
            ->setArguments([
                '%kernel.charset%',
                true
            ])
            ->addTag('event_subscriber');
    }

    private function registerAccessControlEventListener(ContainerBuilder $container, \stdClass $config): void
    {
        $container->getDefinition($this->prefix('event_listener.access_control'))
            ->setArguments([
                $config->access_control ?? '*'
            ]);
    }

}
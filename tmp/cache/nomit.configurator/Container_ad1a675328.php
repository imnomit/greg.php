<?php
// source: /Users/im.nomit/Sites/nomit.php/config/config.neon
// source: array

/** @noinspection PhpParamsInspection,PhpMethodMayBeStaticInspection */

declare(strict_types=1);

class Container_ad1a675328 extends nomit\DependencyInjection\Container
{
	protected array $tags = [
		'application.summary.administration.extension' => [
			'application.controller.page.summary.page' => true,
			'application.controller.photo.summary.photo' => true,
			'application.security.authorization.summary.authorization' => true,
			'application.security.user.routing.summary.user' => true,
			'application.summary.documentation.summary.documentation' => true,
		],
		'event_subscriber' => [
			'application.rate_limiter.event_listener.application_throttling' => true,
			'application.resource.event_listener' => true,
			'application.security.authentication.routing.event_listener.routing' => true,
			'application.security.user.routing.event_listener' => true,
			'authentication.event_listener.account_information' => true,
			'authentication.event_listener.authentication_throttling.main' => [
				'dispatcher' => 'authentication.event_dispatcher.main',
			],
			'authentication.event_listener.evaluate_credentials.main' => [
				'dispatcher' => 'authentication.event_dispatcher.main',
			],
			'authentication.event_listener.logout.cookie_clearing.main' => [
				'dispatcher' => 'authentication.event_dispatcher.main',
			],
			'authentication.event_listener.logout.redirect.main' => [
				'dispatcher' => 'authentication.event_dispatcher.main',
			],
			'authentication.event_listener.logout.session.main' => ['dispatcher' => 'authentication.event_dispatcher.main'],
			'authentication.event_listener.password_migrating.main' => [
				'dispatcher' => 'authentication.event_dispatcher.main',
			],
			'authentication.event_listener.session_authentication_strategy.main' => [
				'dispatcher' => 'authentication.event_dispatcher.main',
			],
			'authentication.event_listener.token_persistence.main' => [
				'dispatcher' => 'authentication.event_dispatcher.main',
			],
			'authentication.event_listener.update_login_record.main' => [
				'dispatcher' => 'authentication.event_dispatcher.main',
			],
			'authentication.event_listener.user_provider.main' => [
				'nomit\Security\Authentication\Event\CheckPassportAuthenticationEvent' => 'onCheckPassport',
			],
			'authentication.event_listener.user_validator.main' => ['dispatcher' => 'authentication.event_dispatcher.main'],
			'authentication.firewall.main' => [
				'kernel.request' => 'onKernelRequest',
				'kernel.finish_request' => 'onKernelFinishRequest',
			],
			'authentication.remember_me.event_listener.evaluate_conditions.main' => [
				'dispatcher' => 'authentication.event_dispatcher.main',
			],
			'authentication.remember_me.event_listener.remember_me.main' => [
				'dispatcher' => 'authentication.event_dispatcher.main',
			],
			'authorization.event_listener.user_role_provider' => 'event_dispatcher',
			'confirmation.event_listener.confirmation' => true,
			'controller.event_listener.controller' => true,
			'error.event_listener.exception' => true,
			'notification.event_listener.notifying' => true,
			'notification.event_listener.remove_notifications' => true,
			'notification.event_listener.stamp_notifications' => true,
			'notification.event_listener.store_notifications' => true,
			'password_reset.event_listener.password_hashing' => true,
			'password_reset.event_listener.perform' => true,
			'password_reset.event_listener.request' => true,
			'password_reset.event_listener.validate' => true,
			'profile.event_listener.password_hashing' => true,
			'profile.event_listener.update_profile' => true,
			'profile.event_listener.view_profile' => true,
			'registration.event_listener.password_hashing' => true,
			'registration.event_listener.registration' => true,
			'session.event_listener.closing.main' => [
				'dispatcher' => ['event_dispatcher', 'session.event_dispatcher.main'],
			],
			'session.event_listener.invalidating.main' => [
				'dispatcher' => ['event_dispatcher', 'session.event_dispatcher.main'],
			],
			'session.event_listener.opening.main' => [
				'dispatcher' => ['event_dispatcher', 'session.event_dispatcher.main'],
			],
			'session.event_listener.regenerating.main' => [
				'dispatcher' => ['event_dispatcher', 'session.event_dispatcher.main'],
			],
			'session.firewall.main' => true,
			'toasting.event_listener.preset_envelopes' => true,
			'toasting.event_listener.remove_envelopes' => true,
			'toasting.event_listener.stamp_envelopes' => true,
			'toasting.event_listener.store_envelopes' => true,
			'toasting.event_listener.toasting' => true,
			'web.event_listener.access_control' => true,
			'web.event_listener.response' => true,
		],
		'logger' => [
			'application.resource.event_listener' => 'resource',
			'application.security.authentication.routing.event_listener.routing' => 'security.authentication.routing',
			'application.security.user.routing.event_listener' => 'application.security.user',
			'authentication.authenticator.remember_me.main' => 'security.authentication.authenticator.remember_me.main',
			'authentication.authenticator.resolver.main' => 'security.authentication.firewall.main',
			'authentication.event_listener.account_information' => 'security.authentication',
			'authentication.event_listener.authentication_throttling.main' => 'security.authentication.rate_limiter',
			'authentication.event_listener.logout.redirect.main' => 'security.authentication.firewall.logout.main',
			'authentication.event_listener.password_migrating.main' => 'security.authentication.firewall.main',
			'authentication.event_listener.token_persistence.main' => 'security.authentication.firewall.main',
			'authentication.event_listener.update_login_record.main' => 'security.authentication.firewall.main',
			'authentication.firewall.listener.channel.main' => 'security.authentication.firewall.main',
			'authentication.firewall.listener.exception.main' => 'security.authentication.firewall.main',
			'authentication.firewall.listener.logout.main' => 'security.authentication.firewall.logout.main',
			'authentication.firewall.listener.session.main' => 'security.authentication.firewall.main',
			'authentication.handler.failure.default' => 'security.authentication.handler.failure',
			'authentication.remember_me.event_listener.evaluate_conditions.main' => 'security.authentication.authenticator.remember_me.main',
			'authentication.remember_me.event_listener.remember_me.main' => 'security.authentication.authenticator.remember_me.main',
			'authentication.remember_me.event_listener.response' => 'security.authentication.authenticator.remember_me',
			'authentication.remember_me.handler.main' => 'security.authentication.remember_me',
			'authentication.token.persistence.filesystemmain' => 'security.authentication.firewall.main',
			'authentication.user.provider.concrete.database' => 'security.authentication.user.provider',
			'authorization.manager' => 'security.authorization',
			'authorization.user.provider.database' => 'security.authorization',
			'client.client' => 'client',
			'confirmation.event_listener.confirmation' => 'security.registration.confirmation',
			'controller.1' => 'application.controller',
			'controller.10' => 'application.controller',
			'controller.11' => 'application.controller',
			'controller.12' => 'application.controller',
			'controller.13' => 'application.controller',
			'controller.14' => 'application.controller',
			'controller.15' => 'application.controller',
			'controller.16' => 'application.controller',
			'controller.17' => 'application.controller',
			'controller.18' => 'application.controller',
			'controller.19' => 'application.controller',
			'controller.2' => 'application.controller',
			'controller.3' => 'application.controller',
			'controller.4' => 'application.controller',
			'controller.5' => 'application.controller',
			'controller.6' => 'application.controller',
			'controller.7' => 'application.controller',
			'controller.8' => 'application.controller',
			'controller.9' => 'application.controller',
			'error.handler.log' => 'error',
			'lock.factory' => 'lock',
			'messenger.consumer' => 'messenger',
			'messenger.worker' => 'messenger',
			'model.1' => 'application.model',
			'model.10' => 'application.model',
			'model.11' => 'application.model',
			'model.12' => 'application.model',
			'model.13' => 'application.model',
			'model.14' => 'application.model',
			'model.15' => 'application.model',
			'model.16' => 'application.model',
			'model.17' => 'application.model',
			'model.18' => 'application.model',
			'model.2' => 'application.model',
			'model.3' => 'application.model',
			'model.4' => 'application.model',
			'model.5' => 'application.model',
			'model.6' => 'application.model',
			'model.7' => 'application.model',
			'model.8' => 'application.model',
			'model.9' => 'application.model',
			'notification.event_listener.notifying' => 'notification',
			'notification.event_listener.store_notifications' => 'notification.event_listener',
			'notification.storage.bag.filesystem' => 'notification.storage.bag',
			'notification.storage.manager' => 'notification.storage',
			'password_reset.event_listener.password_hashing' => 'security.password_hashing',
			'password_reset.event_listener.perform' => 'security.password_reset',
			'password_reset.event_listener.request' => 'security.password_reset',
			'password_reset.event_listener.validate' => 'security.password_reset',
			'password_reset.token.persistence.database' => 'security.password_reset.token.persistence',
			'profile.event_listener.password_hashing' => 'security.profile',
			'profile.event_listener.update_profile' => 'security.profile',
			'profile.event_listener.view_profile' => 'security.profile',
			'profile.provider.database' => 'security.profile.user.provider',
			'registration.event_listener.password_hashing' => 'security.registration',
			'registration.event_listener.registration' => 'security.registration',
			'registration.user.provider.database' => 'security.registration.user.provider',
			'session.event_listener.closing.main' => 'security.session.firewall.main',
			'session.event_listener.invalidating.main' => 'security.session.firewall.main',
			'session.event_listener.opening.main' => 'security.session.firewall.main',
			'session.event_listener.regenerating.main' => 'security.session.firewall.main',
			'session.firewall.main' => 'security.session',
			'session.session.main' => 'security.session',
			'session.token.persistence.filesystem' => 'security.session.token.persistence',
			'session.token.persistence.main' => 'security.session.token.persistence',
			'toasting.event_listener.toasting' => 'toasting',
		],
		'application.profile.repository' => [
			'application.security.profile.repository.page' => true,
			'application.security.profile.repository.photo' => true,
		],
		'console.command' => [
			'authentication.command.collect_login_garbage' => ['command' => 'auth:gc'],
			'authentication.command.logout_user' => ['command' => 'auth:logout'],
			'authorization.command.create_permission' => ['command' => 'authorization:create_permission'],
			'authorization.command.create_role' => ['command' => 'authorization:create_role'],
			'command.collect_garbage' => ['command' => 'session:gc'],
			'console.command.complete' => ['command' => 'complete'],
			'console.command.dump_completion' => ['command' => 'dump'],
			'console.command.help' => ['command' => 'help'],
			'console.command.list' => ['command' => 'list'],
			'console.command.queue.dequeue' => ['command' => 'queue:dequeue'],
			'console.command.queue.enqueue' => ['command' => 'queue:enqueue'],
			'console.command.welcome' => ['command' => 'welcome'],
			'kernel.command.about' => ['command' => 'about'],
			'kernel.command.secret.decrypt_to_local' => ['command' => 'secrets:decrypt-to-local'],
			'kernel.command.secret.encrypt_secrets_from_local' => ['command' => 'secrets:encrypt-from-local'],
			'kernel.command.secret.list' => ['command' => 'secrets:list'],
			'kernel.command.secret.remove' => ['command' => 'secrets:remove'],
			'kernel.command.secret.set' => ['command' => 'secrets:set'],
			'logger.command.collect_garbage' => ['command' => 'logger:gc'],
			'messenger.command.consume' => ['command' => 'messenger:consume'],
			'notification.command.clear_user' => ['command' => 'notification:clear'],
			'notification.command.get_user' => ['command' => 'notification:user'],
			'notification.command.push_to_everyone' => ['command' => 'notification:everyone'],
			'notification.command.push_to_user' => ['command' => 'notification:user'],
			'resource.command.clear_cache' => ['command' => 'resource:clear'],
		],
		'event_dispatcher.dispatcher' => [
			'authentication.event_dispatcher.main' => true,
			'event_dispatcher.dispatcher' => true,
			'session.event_dispatcher.main' => true,
		],
		'event_dispatcher' => [
			'authentication.firewall.listener.logout.main' => ['dispatcher' => 'authentication.event_dispatcher.main'],
		],
		'authentication.remember_me_aware' => [
			'authentication.firewall.listener.session.main' => ['id' => 'main', 'provider' => 'none'],
		],
		'event_subscriaber' => ['authentication.remember_me.event_listener.response' => true],
		'authentication.remember_me.token.persistence' => ['authentication.remember_me.token.persistence.main' => true],
		'cache' => [
			'authentication.remember_me.token.persistence.main' => 'application',
			'controller.1' => 'application.controller',
			'controller.10' => 'application.controller',
			'controller.11' => 'application.controller',
			'controller.12' => 'application.controller',
			'controller.13' => 'application.controller',
			'controller.14' => 'application.controller',
			'controller.15' => 'application.controller',
			'controller.16' => 'application.controller',
			'controller.17' => 'application.controller',
			'controller.18' => 'application.controller',
			'controller.19' => 'application.controller',
			'controller.2' => 'application.controller',
			'controller.3' => 'application.controller',
			'controller.4' => 'application.controller',
			'controller.5' => 'application.controller',
			'controller.6' => 'application.controller',
			'controller.7' => 'application.controller',
			'controller.8' => 'application.controller',
			'controller.9' => 'application.controller',
			'database.default.explorer' => 'database',
			'database.default.structure' => 'database',
			'model.1' => 'application.model',
			'model.10' => 'application.model',
			'model.11' => 'application.model',
			'model.12' => 'application.model',
			'model.13' => 'application.model',
			'model.14' => 'application.model',
			'model.15' => 'application.model',
			'model.16' => 'application.model',
			'model.17' => 'application.model',
			'model.18' => 'application.model',
			'model.2' => 'application.model',
			'model.3' => 'application.model',
			'model.4' => 'application.model',
			'model.5' => 'application.model',
			'model.6' => 'application.model',
			'model.7' => 'application.model',
			'model.8' => 'application.model',
			'model.9' => 'application.model',
			'template.templateFactory' => 'application',
		],
		'authentication.remember_me.token.storage' => ['authentication.remember_me.token.storage.main' => true],
		'authentication.session.strategy' => ['authentication.session.strategy.default.main' => true],
		'authentication.token.storage' => ['authentication.token.storage.session' => true],
		'authorization.user.provider' => ['authorization.user.provider.database' => true],
		'authorization.voter' => [
			'authorization.voter.authenticated' => ['priority' => 250],
			'authorization.voter.role_hierarchy' => ['priority' => 245],
			'authorization.voter.simple' => ['priority' => 245],
		],
		'confirmation.handler.failure' => ['confirmation.handler.failure.default' => true],
		'confirmation.handler.success' => ['confirmation.handler.success.default' => true],
		'confirmation.token.persistence' => ['confirmation.token.persistence.database' => true],
		'nette.exported' => ['console.command.repository' => true],
		'console.input.reader' => ['console.input.reader.stream' => true],
		'console.output.writer' => [
			'console.output.writer.buffer' => true,
			'console.output.writer.file' => true,
			'console.output.writer.stderr' => true,
			'console.output.writer.stdout' => true,
		],
		'nomit.inject' => [
			'controller.1' => true,
			'controller.10' => true,
			'controller.11' => true,
			'controller.12' => true,
			'controller.13' => true,
			'controller.14' => true,
			'controller.15' => true,
			'controller.16' => true,
			'controller.17' => true,
			'controller.18' => true,
			'controller.19' => true,
			'controller.2' => true,
			'controller.3' => true,
			'controller.4' => true,
			'controller.5' => true,
			'controller.6' => true,
			'controller.7' => true,
			'controller.8' => true,
			'controller.9' => true,
			'model.1' => true,
			'model.10' => true,
			'model.11' => true,
			'model.12' => true,
			'model.13' => true,
			'model.14' => true,
			'model.15' => true,
			'model.16' => true,
			'model.17' => true,
			'model.18' => true,
			'model.2' => true,
			'model.3' => true,
			'model.4' => true,
			'model.5' => true,
			'model.6' => true,
			'model.7' => true,
			'model.8' => true,
			'model.9' => true,
		],
		'cryptography.hasher' => ['cryptography.hasher.sha256' => true],
		'cryptography.password.hasher' => [
			'cryptography.password.hasher.message_digest' => true,
			'cryptography.password.hasher.native' => true,
			'cryptography.password.hasher.pbkdf2' => true,
			'cryptography.password.hasher.sodium' => true,
			'cryptography.password.hasher.user' => true,
		],
		'cryptogrpahy.password.hasher' => ['cryptography.password.hasher.plaintext' => true],
		'csrf.token.generator' => ['csrf.token.generator.uri_safe' => true],
		'csrf.token.storage' => ['csrf.token.storage.session' => true],
		'error.extension' => [
			'dependency_injection.di.bridge.error.extension' => true,
			'session.bridge.error.extension' => true,
		],
		'error.handler' => ['error.handler.log' => true],
		'error.view' => ['error.view.console' => true, 'error.view.html' => true, 'error.view.serialized' => true],
		'kernel.secret.loader' => ['kernel.secret.vault' => true],
		'lock.store' => ['lock.store.database' => true],
		'lock.strategy' => ['lock.strategy.majority' => true, 'lock.strategy.unanimous' => true],
		'logger.handler' => ['logger.handler.stream' => true, 'logger.handler.stream.main' => true],
		'logger.channel' => [
			'logger.logger.main.application.controller' => 'application.controller',
			'logger.logger.main.application.model' => 'application.model',
			'logger.logger.main.application.security.user' => 'application.security.user',
			'logger.logger.main.error' => 'error',
			'logger.logger.main.lock' => 'lock',
			'logger.logger.main.messenger' => 'messenger',
			'logger.logger.main.notification.event_listener' => 'notification.event_listener',
			'logger.logger.main.notification.storage' => 'notification.storage',
			'logger.logger.main.resource' => 'resource',
			'logger.logger.main.security.authentication' => 'security.authentication',
			'logger.logger.main.security.authentication.authenticator.remember_me' => 'security.authentication.authenticator.remember_me',
			'logger.logger.main.security.authentication.authenticator.remember_me.main' => 'security.authentication.authenticator.remember_me.main',
			'logger.logger.main.security.authentication.firewall.logout.main' => 'security.authentication.firewall.logout.main',
			'logger.logger.main.security.authentication.firewall.main' => 'security.authentication.firewall.main',
			'logger.logger.main.security.authentication.handler.failure' => 'security.authentication.handler.failure',
			'logger.logger.main.security.authentication.rate_limiter' => 'security.authentication.rate_limiter',
			'logger.logger.main.security.authentication.remember_me' => 'security.authentication.remember_me',
			'logger.logger.main.security.authentication.routing' => 'security.authentication.routing',
			'logger.logger.main.security.authentication.user.provider' => 'security.authentication.user.provider',
			'logger.logger.main.security.authorization' => 'security.authorization',
			'logger.logger.main.security.password_hashing' => 'security.password_hashing',
			'logger.logger.main.security.password_reset' => 'security.password_reset',
			'logger.logger.main.security.password_reset.token.persistence' => 'security.password_reset.token.persistence',
			'logger.logger.main.security.profile' => 'security.profile',
			'logger.logger.main.security.registration' => 'security.registration',
			'logger.logger.main.security.registration.confirmation' => 'security.registration.confirmation',
			'logger.logger.main.security.session' => 'security.session',
			'logger.logger.main.security.session.firewall.main' => 'security.session.firewall.main',
			'logger.logger.main.security.session.token.persistence' => 'security.session.token.persistence',
		],
		'logger.processor' => [
			'logger.processor.git' => true,
			'logger.processor.hostname' => true,
			'logger.processor.instrospection' => true,
			'logger.processor.memory.peak_usage' => true,
			'logger.processor.memory.usage' => true,
			'logger.processor.mercurial' => true,
			'logger.processor.process_id' => true,
			'logger.processor.psr_log_message' => true,
			'logger.processor.psr_log_message.main' => true,
			'logger.processor.tag' => true,
			'logger.processor.tag.main' => true,
			'logger.processor.uid' => true,
			'logger.processor.web' => true,
		],
		'messenger.driver' => ['messenger.driver.filesystem.system' => true],
		'messenger.handler' => [
			'messenger.handler.callback' => true,
			'messenger.handler.closure' => true,
			'messenger.handler.invokable' => true,
		],
		'messenger.queue' => ['messenger.queue.system' => true],
		'notification.console.command' => [
			'notification.command.clear_user' => true,
			'notification.command.get_user' => true,
			'notification.command.push_to_everyone' => true,
			'notification.command.push_to_user' => true,
		],
		'notification.response.view' => [
			'notification.response.view.array' => true,
			'notification.response.view.serialized' => true,
		],
		'notifiation.storage.bag' => ['notification.storage.bag.array' => true],
		'notification.storage.bag' => ['notification.storage.bag.filesystem' => true],
		'security.password_reset.token.persistence' => ['password_reset.token.persistence.database' => true],
		'profile.handler.failure' => ['profile.handler.failure.default' => true],
		'profile.handler.success' => ['profile.handler.success.default' => true],
		'profile.provider' => ['profile.provider.database' => true],
		'rate_limiter.factory' => [
			'rate_limiter.factory._login_globalmain' => true,
			'rate_limiter.factory._login_local.main' => true,
			'rate_limiter.factory.anonymous_api' => true,
			'rate_limiter.factory.authenticated_api' => true,
		],
		'rate_limiter.storage' => ['rate_limiter.storage.cache' => true, 'rate_limiter.storage.in_memory' => true],
		'registration.handler.failure' => ['registration.handler.failure.default' => true],
		'registration.handler.success' => ['registration.handler.success.default' => true],
		'registration.token.storage' => ['registration.token.storage.session' => true],
		'registration.user.provider' => ['registration.user.provider.database' => true],
		'serializer.encoder' => [
			'serializer.encoder.json' => true,
			'serializer.encoder.md' => true,
			'serializer.encoder.neon' => true,
			'serializer.encoder.null' => true,
			'serializer.encoder.php' => true,
			'serializer.encoder.xml' => true,
			'serializer.encoder.yaml' => true,
		],
		'serializer.transformer' => [
			'serializer.transformer.array' => true,
			'serializer.transformer.array.flat' => true,
			'serializer.transformer.closure' => true,
			'serializer.transformer.json' => true,
			'serializer.transformer.xml' => true,
			'serializer.transformer.yaml' => true,
		],
		'session.firewall.listener' => ['session.firewall.listener.matcher.main' => true],
		'session' => ['session.session.main' => true],
		'toasting.response.view' => ['toasting.response.view.array' => true, 'toasting.response.view.serialized' => true],
		'toasting.storage.bag' => ['toasting.storage.bag.array' => true, 'toasting.storage.bag.session' => true],
		'view' => ['view.html' => true],
	];

	protected array $types = ['container' => 'nomit\DependencyInjection\Container'];

	protected array $aliases = [
		'authentication.authenticator.resolver' => 'authentication.authenticator.resolver.main',
		'authentication.token.persistence' => 'authentication.token.persistence.filesystemmain',
		'authentication.token.persistence.main' => 'authentication.token.persistence.filesystemmain',
		'authentication.token.storage' => 'authentication.token.storage.session',
		'authentication.token.storage.main' => 'authentication.token.storage.session',
		'authentication.user.provider.main' => 'authentication.user.provider.concrete.database',
		'authentication.user_providers' => 'authentication.user.provider.concrete.database',
		'authentication.utilities.web' => 'authentication.utilities.web.main',
		'authorization.user.provider' => 'authorization.user.provider.database',
		'cache' => 'cache.cache.application',
		'console' => 'console.kernel',
		'cryptography.hasher' => 'cryptography.hasher.sha256',
		'csrf.token.generator' => 'csrf.token.generator.uri_safe',
		'csrf.token.storage' => 'csrf.token.storage.session',
		'database.default' => 'database.default.connection',
		'database.default.context' => 'database.default.explorer',
		'event_dispatcher' => 'event_dispatcher.dispatcher',
		'filesystem' => 'filesystem.filesystem',
		'logger' => 'logger.main',
		'mailer' => 'mail.mailer',
		'nomit.kernelTemplateFactory' => 'template.kernelTemplateFactory',
		'nomit.templateFactory' => 'template.templateFactory',
		'nomit\Web\Request\Request' => 'web.request',
		'notifier' => 'notification.notifier',
		'profile' => 'profile.profile',
		'property_accessor' => 'property_accessor.accessor',
		'rate_limiter.factory' => 'rate_limiter.factory.anonymous_api',
		'rate_limiter.rate_limiter.storage.cache._login_globalmain' => 'rate_limiter.rate_limiter.storage.cache',
		'rate_limiter.rate_limiter.storage.cache._login_local.main' => 'rate_limiter.rate_limiter.storage.cache',
		'registration.token.storage' => 'registration.token.storage.session',
		'request' => 'web.request',
		'requests' => 'web.requests',
		'secret' => 'parameters.factory',
		'serializer' => 'serializer.resolver',
		'session' => 'session.session.main',
		'toaster' => 'toasting.toaster',
	];

	protected array $wiring = [
		'Psr\Container\ContainerInterface' => [['container']],
		'nomit\DependencyInjection\Container' => [['container']],
		'nomit\Kernel\Secret\SecretFactory' => [['parameters.factory']],
		'nomit\Console\Command\Command' => [
			2 => [
				'kernel.command.about',
				'kernel.command.secret.decrypt_to_local',
				'kernel.command.secret.encrypt_secrets_from_local',
				'kernel.command.secret.list',
				'kernel.command.secret.remove',
				'kernel.command.secret.set',
				'authentication.command.collect_login_garbage',
				'authentication.command.logout_user',
				'authorization.command.create_role',
				'authorization.command.create_permission',
				'logger.command.collect_garbage',
				'command.collect_garbage',
				'resource.command.clear_cache',
				'notification.command.push_to_everyone',
				'notification.command.push_to_user',
				'notification.command.get_user',
				'notification.command.clear_user',
				'messenger.command.consume',
				'console.command.complete',
				'console.command.dump_completion',
				'console.command.help',
				'console.command.list',
				'console.command.welcome',
				'console.command.queue.enqueue',
				'console.command.queue.dequeue',
			],
		],
		'nomit\Console\SynopsisInterface' => [
			2 => [
				'kernel.command.about',
				'kernel.command.secret.decrypt_to_local',
				'kernel.command.secret.encrypt_secrets_from_local',
				'kernel.command.secret.list',
				'kernel.command.secret.remove',
				'kernel.command.secret.set',
				'authentication.command.collect_login_garbage',
				'authentication.command.logout_user',
				'authorization.command.create_role',
				'authorization.command.create_permission',
				'logger.command.collect_garbage',
				'command.collect_garbage',
				'resource.command.clear_cache',
				'notification.command.push_to_everyone',
				'notification.command.push_to_user',
				'notification.command.get_user',
				'notification.command.clear_user',
				'messenger.command.consume',
				'console.command.complete',
				'console.command.dump_completion',
				'console.command.help',
				'console.command.list',
				'console.command.welcome',
				'console.command.queue.enqueue',
				'console.command.queue.dequeue',
			],
		],
		'Psr\Log\LoggerInterface' => [
			0 => [
				'logger.main',
				'logger.logger.main.error',
				'logger.logger.main.security.authentication',
				'logger.logger.main.security.authentication.firewall.main',
				'logger.logger.main.security.authentication.firewall.logout.main',
				'logger.logger.main.security.authentication.rate_limiter',
				'logger.logger.main.security.authentication.authenticator.remember_me.main',
				'logger.logger.main.security.authentication.authenticator.remember_me',
				'logger.logger.main.security.authorization',
				'logger.logger.main.application.model',
				'logger.logger.main.application.controller',
				'logger.logger.main.lock',
				'logger.logger.main.security.session.token.persistence',
				'logger.logger.main.security.session',
				'logger.logger.main.security.session.firewall.main',
				'logger.logger.main.security.registration',
				'logger.logger.main.security.registration.confirmation',
				'logger.logger.main.notification.event_listener',
				'logger.logger.main.notification.storage',
				'logger.logger.main.messenger',
				'logger.logger.main.security.password_hashing',
				'logger.logger.main.security.password_reset',
				'logger.logger.main.security.password_reset.token.persistence',
				'logger.logger.main.security.profile',
				'logger.logger.main.resource',
				'logger.logger.main.security.authentication.routing',
				'logger.logger.main.application.security.user',
				'logger.logger.main.security.authentication.user.provider',
				'logger.logger.main.security.authentication.handler.failure',
				'logger.logger.main.security.authentication.remember_me',
			],
			2 => [
				0 => 'kernel.command.about',
				1 => 'kernel.command.secret.decrypt_to_local',
				2 => 'kernel.command.secret.encrypt_secrets_from_local',
				3 => 'kernel.command.secret.list',
				4 => 'kernel.command.secret.remove',
				5 => 'kernel.command.secret.set',
				6 => 'authentication.command.collect_login_garbage',
				7 => 'authentication.command.logout_user',
				8 => 'authorization.command.create_role',
				9 => 'authorization.command.create_permission',
				10 => 'logger.command.collect_garbage',
				12 => 'command.collect_garbage',
				13 => 'resource.command.clear_cache',
				14 => 'notification.command.push_to_everyone',
				15 => 'notification.command.push_to_user',
				16 => 'notification.command.get_user',
				17 => 'notification.command.clear_user',
				18 => 'messenger.command.consume',
				19 => 'console.command.complete',
				20 => 'console.command.dump_completion',
				21 => 'console.command.help',
				22 => 'console.command.list',
				23 => 'console.command.welcome',
				24 => 'console.command.queue.enqueue',
				25 => 'console.command.queue.dequeue',
			],
		],
		'nomit\Utilities\Concern\Stringable' => [
			0 => [
				'dependency_injection.di.bridge.error.extension',
				'session.bridge.error.extension',
				'session.session.main',
				'view.html',
				'messenger.queue.system',
			],
			2 => [
				0 => 'kernel.command.about',
				1 => 'kernel.command.secret.decrypt_to_local',
				2 => 'kernel.command.secret.encrypt_secrets_from_local',
				3 => 'kernel.command.secret.list',
				4 => 'kernel.command.secret.remove',
				5 => 'kernel.command.secret.set',
				7 => 'authentication.command.collect_login_garbage',
				8 => 'authentication.command.logout_user',
				9 => 'authorization.command.create_role',
				10 => 'authorization.command.create_permission',
				11 => 'logger.command.collect_garbage',
				14 => 'command.collect_garbage',
				15 => 'resource.command.clear_cache',
				17 => 'notification.command.push_to_everyone',
				18 => 'notification.command.push_to_user',
				19 => 'notification.command.get_user',
				20 => 'notification.command.clear_user',
				21 => 'messenger.command.consume',
				23 => 'console.command.complete',
				24 => 'console.command.dump_completion',
				25 => 'console.command.help',
				26 => 'console.command.list',
				27 => 'console.command.welcome',
				28 => 'console.command.queue.enqueue',
				29 => 'console.command.queue.dequeue',
			],
		],
		'Stringable' => [
			0 => [
				'dependency_injection.di.bridge.error.extension',
				'session.bridge.error.extension',
				'session.session.main',
				'web.response',
				'view.html',
				'messenger.queue.system',
			],
			2 => [
				0 => 'kernel.command.about',
				1 => 'kernel.command.secret.decrypt_to_local',
				2 => 'kernel.command.secret.encrypt_secrets_from_local',
				3 => 'kernel.command.secret.list',
				4 => 'kernel.command.secret.remove',
				5 => 'kernel.command.secret.set',
				7 => 'authentication.command.collect_login_garbage',
				8 => 'authentication.command.logout_user',
				9 => 'authorization.command.create_role',
				10 => 'authorization.command.create_permission',
				11 => 'logger.command.collect_garbage',
				14 => 'command.collect_garbage',
				16 => 'resource.command.clear_cache',
				18 => 'notification.command.push_to_everyone',
				19 => 'notification.command.push_to_user',
				20 => 'notification.command.get_user',
				21 => 'notification.command.clear_user',
				22 => 'messenger.command.consume',
				24 => 'console.command.complete',
				25 => 'console.command.dump_completion',
				26 => 'console.command.help',
				27 => 'console.command.list',
				28 => 'console.command.welcome',
				29 => 'console.command.queue.enqueue',
				30 => 'console.command.queue.dequeue',
			],
		],
		'nomit\Console\Command\CommandInterface' => [
			2 => [
				'kernel.command.about',
				'kernel.command.secret.decrypt_to_local',
				'kernel.command.secret.encrypt_secrets_from_local',
				'kernel.command.secret.list',
				'kernel.command.secret.remove',
				'kernel.command.secret.set',
				'authentication.command.collect_login_garbage',
				'authentication.command.logout_user',
				'authorization.command.create_role',
				'authorization.command.create_permission',
				'logger.command.collect_garbage',
				'command.collect_garbage',
				'resource.command.clear_cache',
				'notification.command.push_to_everyone',
				'notification.command.push_to_user',
				'notification.command.get_user',
				'notification.command.clear_user',
				'messenger.command.consume',
				'console.command.complete',
				'console.command.dump_completion',
				'console.command.help',
				'console.command.list',
				'console.command.welcome',
				'console.command.queue.enqueue',
				'console.command.queue.dequeue',
			],
		],
		'nomit\Kernel\Command\AboutCommand' => [2 => ['kernel.command.about']],
		'nomit\Kernel\Command\DecryptSecretsToLocalCommand' => [2 => ['kernel.command.secret.decrypt_to_local']],
		'nomit\Kernel\Command\EncryptSecretsFromLocalCommand' => [
			2 => ['kernel.command.secret.encrypt_secrets_from_local'],
		],
		'nomit\Kernel\Command\ListSecretsCommand' => [2 => ['kernel.command.secret.list']],
		'nomit\Kernel\Command\RemoveSecretsCommand' => [2 => ['kernel.command.secret.remove']],
		'nomit\Kernel\Command\SetSecretsCommand' => [2 => ['kernel.command.secret.set']],
		'nomit\Error\View\AbstractView' => [['error.view.serialized', 'error.view.html', 'error.view.console']],
		'nomit\Error\View\ViewInterface' => [['error.view.serialized', 'error.view.html', 'error.view.console']],
		'nomit\Error\View\SerializedView' => [['error.view.serialized']],
		'nomit\Error\View\HtmlView' => [['error.view.html']],
		'nomit\Error\View\ConsoleView' => [['error.view.console']],
		'nomit\Error\Handler\HandlerInterface' => [['error.handler.log']],
		'nomit\Error\Handler\LogHandler' => [['error.handler.log']],
		'nomit\Error\ErrorHandlerInterface' => [['error.handler']],
		'nomit\Error\ErrorHandler' => [['error.handler']],
		'nomit\EventDispatcher\EventDispatcher' => [
			['event_dispatcher.dispatcher', 'authentication.event_dispatcher.main', 'session.event_dispatcher.main'],
		],
		'nomit\EventDispatcher\EventDispatcherInterface' => [
			['event_dispatcher.dispatcher', 'authentication.event_dispatcher.main', 'session.event_dispatcher.main'],
		],
		'nomit\EventDispatcher\LazyEventDispatcher' => [
			['event_dispatcher.dispatcher', 'authentication.event_dispatcher.main', 'session.event_dispatcher.main'],
		],
		'nomit\Error\Extension\Extension' => [
			['dependency_injection.di.bridge.error.extension', 'session.bridge.error.extension'],
		],
		'nomit\Error\Extension\ExtensionInterface' => [
			['dependency_injection.di.bridge.error.extension', 'session.bridge.error.extension'],
		],
		'nomit\DependencyInjection\Bridge\Error\DependencyInjectionExtension' => [
			['dependency_injection.di.bridge.error.extension'],
		],
		'nomit\Security\Authentication\Command\CollectLoginGarbageAuthenticationCommand' => [
			2 => ['authentication.command.collect_login_garbage'],
		],
		'nomit\Security\Authentication\Command\LogoutUserAuthenticationCommand' => [
			2 => ['authentication.command.logout_user'],
		],
		'nomit\EventDispatcher\EventSubscriberInterface' => [
			[
				'authentication.event_listener.account_information',
				'authentication.event_listener.evaluate_credentials.main',
				'authentication.event_listener.update_login_record.main',
				'authentication.event_listener.session_authentication_strategy.main',
				'authentication.event_listener.token_persistence.main',
				'authentication.event_listener.password_migrating.main',
				'authentication.event_listener.logout.redirect.main',
				'authentication.event_listener.logout.session.main',
				'authentication.event_listener.logout.cookie_clearing.main',
				'authentication.event_listener.authentication_throttling.main',
				'authentication.remember_me.event_listener.evaluate_conditions.main',
				'authentication.remember_me.event_listener.remember_me.main',
				'authentication.remember_me.event_listener.response',
				'authentication.firewall.main',
				'authorization.event_listener.user_role_provider',
				'controller.event_listener.controller',
				'session.event_listener.closing.main',
				'session.event_listener.invalidating.main',
				'session.event_listener.opening.main',
				'session.event_listener.regenerating.main',
				'session.firewall.main',
				'web.event_listener.access_control',
				'web.event_listener.response',
				'registration.event_listener.password_hashing',
				'registration.event_listener.registration',
				'confirmation.event_listener.confirmation',
				'notification.event_listener.notifying',
				'notification.event_listener.remove_notifications',
				'notification.event_listener.stamp_notifications',
				'notification.event_listener.store_notifications',
				'password_reset.event_listener.password_hashing',
				'password_reset.event_listener.perform',
				'password_reset.event_listener.request',
				'password_reset.event_listener.validate',
				'profile.event_listener.password_hashing',
				'profile.event_listener.update_profile',
				'profile.event_listener.view_profile',
				'toasting.event_listener.preset_envelopes',
				'toasting.event_listener.remove_envelopes',
				'toasting.event_listener.stamp_envelopes',
				'toasting.event_listener.store_envelopes',
				'toasting.event_listener.toasting',
				'application.resource.event_listener',
				'application.security.authentication.routing.event_listener.routing',
				'application.security.user.routing.event_listener',
				'application.rate_limiter.event_listener.application_throttling',
				'error.event_listener.exception',
				'authentication.event_listener.user_provider.main',
				'authentication.event_listener.user_validator.main',
			],
		],
		'nomit\Security\Authentication\EventListener\AccountInformationEventListener' => [
			['authentication.event_listener.account_information'],
		],
		'nomit\Security\Authentication\Token\Storage\AbstractTokenStorage' => [['authentication.token.storage.session']],
		'nomit\Security\Authentication\Token\Storage\TokenStorageInterface' => [['authentication.token.storage.session']],
		'nomit\Security\Authentication\Token\Storage\SessionTokenStorage' => [['authentication.token.storage.session']],
		'nomit\Security\Authentication\Trust\TrustResolverInterface' => [['authentication.trust.resolver']],
		'nomit\Security\Authentication\Trust\TrustResolver' => [['authentication.trust.resolver']],
		'nomit\Security\Authentication\Token\Persistence\TokenPersistenceInterface' => [
			['authentication.token.persistence.filesystemmain'],
		],
		'nomit\Security\Authentication\Token\Persistence\FileSystemTokenPersistence' => [
			['authentication.token.persistence.filesystemmain'],
		],
		'nomit\EventDispatcher\AbstractEventSubscriber' => [
			[
				'authentication.event_listener.evaluate_credentials.main',
				'authentication.firewall.main',
				'session.event_listener.closing.main',
				'session.event_listener.invalidating.main',
				'session.event_listener.opening.main',
				'session.event_listener.regenerating.main',
				'web.event_listener.response',
				'authentication.event_listener.user_provider.main',
				'authentication.event_listener.user_validator.main',
			],
		],
		'nomit\Security\Authentication\EventListener\EvaluateCredentialsEventListener' => [
			['authentication.event_listener.evaluate_credentials.main'],
		],
		'nomit\Security\Authentication\EventListener\UpdateLoginRecordAuthenticationEventListener' => [
			['authentication.event_listener.update_login_record.main'],
		],
		'nomit\Security\Authentication\EventListener\SessionAuthenticationStrategyEventListener' => [
			['authentication.event_listener.session_authentication_strategy.main'],
		],
		'nomit\Security\Authentication\EventListener\TokenPersistenceEventListener' => [
			['authentication.event_listener.token_persistence.main'],
		],
		'nomit\Security\Authentication\EventListener\PasswordMigratingEventListener' => [
			['authentication.event_listener.password_migrating.main'],
		],
		'nomit\Security\Authentication\Firewall\Listener\AbstractFirewallListener' => [
			[
				'authentication.firewall.listener.logout.main',
				'authentication.firewall.listener.channel.main',
				'authentication.firewall.listener.session.main',
				'authentication.firewall.listener.authenticator_resolver',
				'authentication.firewall.listener.registration.main',
				'authentication.firewall.listener.authorization.main',
			],
		],
		'nomit\Security\Authentication\Firewall\Listener\FirewallListenerInterface' => [
			[
				'authentication.firewall.listener.logout.main',
				'authentication.firewall.listener.channel.main',
				'authentication.firewall.listener.session.main',
				'authentication.firewall.listener.authenticator_resolver',
				'authentication.firewall.listener.registration.main',
				'authentication.firewall.listener.authorization.main',
			],
		],
		'nomit\Security\Authentication\Firewall\Listener\LogoutFirewallListener' => [
			['authentication.firewall.listener.logout.main'],
		],
		'nomit\Security\Authentication\EventListener\Logout\LogoutEventListenerInterface' => [
			[
				'authentication.event_listener.logout.redirect.main',
				'authentication.event_listener.logout.session.main',
				'authentication.event_listener.logout.cookie_clearing.main',
			],
		],
		'nomit\Security\Authentication\EventListener\Logout\RedirectingLogoutEventListener' => [
			['authentication.event_listener.logout.redirect.main'],
		],
		'nomit\Security\Authentication\EventListener\Logout\SessionClearingLogoutEventListener' => [
			['authentication.event_listener.logout.session.main'],
		],
		'nomit\Security\Authentication\EventListener\Logout\CookieClearingLogoutEventListener' => [
			['authentication.event_listener.logout.cookie_clearing.main'],
		],
		'nomit\Security\Authentication\EventListener\AuthenticationThrottlingEventListener' => [
			['authentication.event_listener.authentication_throttling.main'],
		],
		'nomit\Security\Authentication\EventListener\EvaluateRememberMeConditionsEventListener' => [
			['authentication.remember_me.event_listener.evaluate_conditions.main'],
		],
		'nomit\Security\Authentication\EventListener\RememberMeEventListener' => [
			['authentication.remember_me.event_listener.remember_me.main'],
		],
		'nomit\Security\Authentication\RememberMe\EventListener\RememberMeResponseEventListener' => [
			['authentication.remember_me.event_listener.response'],
		],
		'nomit\Security\Authentication\Firewall\Firewall' => [['authentication.firewall.main']],
		'nomit\Security\Authorization\Command\CreateRoleCommand' => [2 => ['authorization.command.create_role']],
		'nomit\Security\Authorization\Command\CreatePermissionCommand' => [
			2 => ['authorization.command.create_permission'],
		],
		'nomit\Security\Authorization\EventListener\UserRoleProviderEventListener' => [
			['authorization.event_listener.user_role_provider'],
		],
		'nomit\Security\Authorization\User\UserAuthorizationProviderInterface' => [
			['authorization.user.provider.database'],
		],
		'nomit\Security\Authorization\User\DatabaseUserAuthorizationProvider' => [['authorization.user.provider.database']],
		'nomit\Security\Authorization\Voter\VoterInterface' => [
			['authorization.voter.simple', 'authorization.voter.authenticated', 'authorization.voter.role_hierarchy'],
		],
		'nomit\Security\Authorization\Voter\RoleVoter' => [
			['authorization.voter.simple', 'authorization.voter.role_hierarchy'],
		],
		'nomit\Security\Authorization\Voter\AuthenticatedVoter' => [['authorization.voter.authenticated']],
		'nomit\Security\Authorization\Voter\RoleHierarchyVoter' => [['authorization.voter.role_hierarchy']],
		'nomit\Security\Authorization\AccessMapInterface' => [['authorization.access_map']],
		'nomit\Security\Authorization\AccessMap' => [['authorization.access_map']],
		'nomit\Security\Authorization\AccessDecisionManagerInterface' => [['authorization.decision_manager']],
		'nomit\Security\Authorization\AccessDecisionManager' => [['authorization.decision_manager']],
		'nomit\Security\Authorization\AuthorizationManagerInterface' => [['authorization.manager']],
		'nomit\Security\Authorization\AuthorizationManager' => [['authorization.manager']],
		'nomit\Security\Authorization\AuthorizationCheckerInterface' => [['authorization.checker']],
		'nomit\Security\Authorization\AuthorizationChecker' => [['authorization.checker']],
		'nomit\Kernel\Model\AbstractModel' => [
			[
				'model.1',
				'model.2',
				'model.3',
				'model.4',
				'model.5',
				'model.6',
				'model.7',
				'model.8',
				'model.9',
				'model.10',
				'model.11',
				'model.12',
				'model.13',
				'model.14',
				'model.15',
				'model.16',
				'model.17',
				'model.18',
			],
		],
		'nomit\Kernel\Model\ModelInterface' => [
			[
				'model.1',
				'model.2',
				'model.3',
				'model.4',
				'model.5',
				'model.6',
				'model.7',
				'model.8',
				'model.9',
				'model.10',
				'model.11',
				'model.12',
				'model.13',
				'model.14',
				'model.15',
				'model.16',
				'model.17',
				'model.18',
			],
		],
		'nomit\Kernel\Model\TabularModelInterface' => [
			['model.1', 'model.2', 'model.3', 'model.4', 'model.15', 'model.16', 'model.17'],
		],
		'Application\Model\Page\PageModel' => [['model.1']],
		'Application\Model\Page\Participant\ParticipantModel' => [['model.2']],
		'Application\Model\Page\Discussion\DiscussionModel' => [['model.3']],
		'Application\Model\Page\Revision\RevisionModel' => [['model.4']],
		'Application\Model\Documentation\Revision\DocumentationModel' => [['model.5']],
		'Application\Model\Documentation\Tag\TagRelationshipModel' => [['model.6']],
		'Application\Model\Documentation\Tag\TagModel' => [['model.7']],
		'Application\Model\Documentation\Revision\RevisionModel' => [['model.8']],
		'Application\Model\Forum\ForumModel' => [['model.9']],
		'Application\Model\Forum\Thread\PostModel' => [['model.10']],
		'Application\Model\Forum\Thread\ThreadModel' => [['model.11']],
		'nomit\Kernel\Model\AbstractInteractiveModel' => [['model.12', 'model.13', 'model.14']],
		'nomit\Kernel\Model\InteractiveModelInterface' => [['model.12', 'model.13', 'model.14']],
		'Application\Model\Article\Participant\ParticipantModel' => [['model.12']],
		'Application\Model\Article\ArticleModel' => [['model.13']],
		'Application\Model\Article\Revision\RevisionModel' => [['model.14']],
		'Application\Model\Photo\PhotoModel' => [['model.15']],
		'Application\Model\Photo\Participant\ParticipantModel' => [['model.16']],
		'Application\Model\Photo\Revision\RevisionModel' => [['model.17']],
		'Application\Model\Editor\Image\ImageModel' => [['model.18']],
		'nomit\Cache\Storages\JournalInterface' => [
			[
				'cache.journal.application',
				'cache.journal.database',
				'cache.journal.application.application.model',
				'cache.journal.application.database',
				'cache.journal.application.application.controller',
				'cache.journal.application.application',
			],
		],
		'nomit\Cache\StorageInterface' => [
			[
				'cache.storage.file.application',
				'cache.storage.file.database',
				'cache.storage.file.application.application.model',
				'cache.storage.file.application.database',
				'cache.storage.file.application.application.controller',
				'cache.storage.file.application.application',
			],
		],
		'nomit\Cache\Storages\FileStorage' => [
			[
				'cache.storage.file.application',
				'cache.storage.file.database',
				'cache.storage.file.application.application.model',
				'cache.storage.file.application.database',
				'cache.storage.file.application.application.controller',
				'cache.storage.file.application.application',
			],
		],
		'nomit\Cache\Cache' => [
			[
				'cache.cache.application',
				'cache.cache.database',
				'cache.cache.application.application.model',
				'cache.cache.application.database',
				'cache.cache.application.application.controller',
				'cache.cache.application.application',
			],
		],
		'nomit\Logger\Command\CollectGarbageLoggerCommand' => [2 => ['logger.command.collect_garbage']],
		'nomit\Logger\Handler\AbstractProcessingHandler' => [['logger.handler.stream', 'logger.handler.stream.main']],
		'nomit\Logger\Handler\AbstractHandler' => [['logger.handler.stream', 'logger.handler.stream.main']],
		'nomit\Logger\Handler\Handler' => [['logger.handler.stream', 'logger.handler.stream.main']],
		'nomit\Logger\Handler\FormattableHandlerInterface' => [['logger.handler.stream', 'logger.handler.stream.main']],
		'nomit\Logger\Handler\ProcessableHandlerInterface' => [['logger.handler.stream', 'logger.handler.stream.main']],
		'nomit\Utilities\Service\ResetInterface' => [
			[
				'logger.handler.stream',
				'logger.processor.uid',
				'logger.main',
				'logger.handler.stream.main',
				'logger.logger.main.error',
				'logger.logger.main.security.authentication',
				'logger.logger.main.security.authentication.firewall.main',
				'logger.logger.main.security.authentication.firewall.logout.main',
				'logger.logger.main.security.authentication.rate_limiter',
				'logger.logger.main.security.authentication.authenticator.remember_me.main',
				'logger.logger.main.security.authentication.authenticator.remember_me',
				'logger.logger.main.security.authorization',
				'logger.logger.main.application.model',
				'logger.logger.main.application.controller',
				'logger.logger.main.lock',
				'logger.logger.main.security.session.token.persistence',
				'logger.logger.main.security.session',
				'logger.logger.main.security.session.firewall.main',
				'logger.logger.main.security.registration',
				'logger.logger.main.security.registration.confirmation',
				'logger.logger.main.notification.event_listener',
				'logger.logger.main.notification.storage',
				'logger.logger.main.messenger',
				'logger.logger.main.security.password_hashing',
				'logger.logger.main.security.password_reset',
				'logger.logger.main.security.password_reset.token.persistence',
				'logger.logger.main.security.profile',
				'logger.logger.main.resource',
				'logger.logger.main.security.authentication.routing',
				'logger.logger.main.application.security.user',
				'logger.logger.main.security.authentication.user.provider',
				'logger.logger.main.security.authentication.handler.failure',
				'logger.logger.main.security.authentication.remember_me',
			],
		],
		'nomit\Logger\Handler\HandlerInterface' => [['logger.handler.stream', 'logger.handler.stream.main']],
		'nomit\Logger\Handler\StreamHandler' => [['logger.handler.stream', 'logger.handler.stream.main']],
		'nomit\Logger\Processor\ProcessorInterface' => [
			[
				'logger.processor.git',
				'logger.processor.hostname',
				'logger.processor.instrospection',
				'logger.processor.memory.peak_usage',
				'logger.processor.memory.usage',
				'logger.processor.mercurial',
				'logger.processor.process_id',
				'logger.processor.psr_log_message',
				'logger.processor.tag',
				'logger.processor.uid',
				'logger.processor.web',
				'logger.processor.tag.main',
				'logger.processor.psr_log_message.main',
			],
		],
		'nomit\Logger\Processor\GitProcessor' => [['logger.processor.git']],
		'nomit\Logger\Processor\HostnameProcessor' => [['logger.processor.hostname']],
		'nomit\Logger\Processor\IntrospectionProcessor' => [['logger.processor.instrospection']],
		'nomit\Logger\Processor\MemoryProcessor' => [
			['logger.processor.memory.peak_usage', 'logger.processor.memory.usage'],
		],
		'nomit\Logger\Processor\MemoryPeakUsageProcessor' => [['logger.processor.memory.peak_usage']],
		'nomit\Logger\Processor\MemoryUsageProcessor' => [['logger.processor.memory.usage']],
		'nomit\Logger\Processor\MercurialProcessor' => [['logger.processor.mercurial']],
		'nomit\Logger\Processor\ProcessIdProcessor' => [['logger.processor.process_id']],
		'nomit\Logger\Processor\PsrLogMessageProcessor' => [
			['logger.processor.psr_log_message', 'logger.processor.psr_log_message.main'],
		],
		'nomit\Logger\Processor\TagProcessor' => [['logger.processor.tag', 'logger.processor.tag.main']],
		'nomit\Logger\Processor\UidProcessor' => [['logger.processor.uid']],
		'nomit\Logger\Processor\WebProcessor' => [['logger.processor.web']],
		'nomit\Logger\Logger' => [
			[
				'logger.main',
				'logger.logger.main.error',
				'logger.logger.main.security.authentication',
				'logger.logger.main.security.authentication.firewall.main',
				'logger.logger.main.security.authentication.firewall.logout.main',
				'logger.logger.main.security.authentication.rate_limiter',
				'logger.logger.main.security.authentication.authenticator.remember_me.main',
				'logger.logger.main.security.authentication.authenticator.remember_me',
				'logger.logger.main.security.authorization',
				'logger.logger.main.application.model',
				'logger.logger.main.application.controller',
				'logger.logger.main.lock',
				'logger.logger.main.security.session.token.persistence',
				'logger.logger.main.security.session',
				'logger.logger.main.security.session.firewall.main',
				'logger.logger.main.security.registration',
				'logger.logger.main.security.registration.confirmation',
				'logger.logger.main.notification.event_listener',
				'logger.logger.main.notification.storage',
				'logger.logger.main.messenger',
				'logger.logger.main.security.password_hashing',
				'logger.logger.main.security.password_reset',
				'logger.logger.main.security.password_reset.token.persistence',
				'logger.logger.main.security.profile',
				'logger.logger.main.resource',
				'logger.logger.main.security.authentication.routing',
				'logger.logger.main.application.security.user',
				'logger.logger.main.security.authentication.user.provider',
				'logger.logger.main.security.authentication.handler.failure',
				'logger.logger.main.security.authentication.remember_me',
			],
		],
		'nomit\Database\ConnectionInterface' => [['database.default.connection']],
		'nomit\Database\StructureInterface' => [['database.default.structure']],
		'nomit\Database\Structure' => [['database.default.structure']],
		'nomit\Database\Conventions' => [['database.default.conventions']],
		'nomit\Database\Conventions\DiscoveredConventions' => [['database.default.conventions']],
		'nomit\Database\ExplorerInterface' => [['database.default.explorer']],
		'nomit\Kernel\Component\ControllerFactoryInterface' => [['controller.factory']],
		'nomit\Kernel\EventListener\ControllerEventListener' => [['controller.event_listener.controller']],
		'Application\Controller\Page\PageController' => [2 => ['controller.1', 'controller.2', 'controller.18']],
		'nomit\Kernel\Component\AbstractController' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'nomit\Kernel\Component\Control' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'nomit\Kernel\Component\Component' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'nomit\Component\Container' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'nomit\Component\Component' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'nomit\Component\ComponentInterface' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'nomit\Component\ContainerInterface' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'nomit\Kernel\Component\SignalReceiverInterface' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'nomit\Kernel\Component\PersistentStateInterface' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'ArrayAccess' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
				'session.session.main',
			],
		],
		'nomit\Kernel\Component\ControlInterface' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'nomit\Kernel\Component\RenderableInterface' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'nomit\Kernel\Component\ControllerInterface' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
			],
		],
		'Psr\Http\Server\RequestHandlerInterface' => [
			2 => [
				'controller.1',
				'controller.2',
				'controller.3',
				'controller.4',
				'controller.5',
				'controller.6',
				'controller.7',
				'controller.8',
				'controller.9',
				'controller.10',
				'controller.11',
				'controller.12',
				'controller.13',
				'controller.14',
				'controller.15',
				'controller.16',
				'controller.17',
				'controller.18',
				'controller.19',
				'routing.router',
				'application.resource.event_listener',
				'application.security.authentication.routing.event_listener.routing',
				'application.security.user.routing.event_listener',
			],
		],
		'Application\Controller\Page\DiscussionController' => [2 => ['controller.1']],
		'Application\Controller\Documentation\DocumentationController' => [
			2 => ['controller.3', 'controller.4', 'controller.11', 'controller.12'],
		],
		'Application\Controller\Documentation\TagController' => [2 => ['controller.4', 'controller.11']],
		'Application\Controller\Forum\ForumController' => [2 => ['controller.5', 'controller.6', 'controller.7']],
		'Application\Controller\Forum\ThreadController' => [2 => ['controller.6', 'controller.7']],
		'Application\Controller\Forum\PostController' => [2 => ['controller.6']],
		'Application\Controller\Index\IndexController' => [2 => ['controller.8']],
		'Application\Controller\Administration\AdministrationController' => [
			2 => ['controller.9', 'controller.10', 'controller.13'],
		],
		'Application\Controller\Administration\UserController' => [2 => ['controller.9']],
		'Application\Controller\Administration\SummaryController' => [2 => ['controller.10']],
		'Application\Controller\Administration\TagController' => [2 => ['controller.11']],
		'Application\Controller\Administration\DocumentationController' => [2 => ['controller.12']],
		'Application\Controller\Administration\AuthorizationController' => [2 => ['controller.13']],
		'Application\Controller\Article\ArticleController' => [2 => ['controller.14']],
		'Application\Controller\Photo\PhotoController' => [2 => ['controller.15']],
		'Application\Controller\Editor\LinkController' => [2 => ['controller.16']],
		'Application\Controller\Editor\ImageController' => [2 => ['controller.17']],
		'Application\Controller\Editor\PageController' => [2 => ['controller.18']],
		'Application\Controller\Summary\SummaryController' => [2 => ['controller.19']],
		'nomit\Cryptography\Hasher\AbstractHasher' => [['cryptography.hasher.sha256']],
		'nomit\Cryptography\Hasher\HasherInterface' => [['cryptography.hasher.sha256']],
		'nomit\Cryptography\Hasher\Sha256Hasher' => [['cryptography.hasher.sha256']],
		'nomit\Cryptography\Security\AbstractFactory' => [['cryptography.entropy_factory']],
		'nomit\Cryptography\Entropy\EntropyFactory' => [['cryptography.entropy_factory']],
		'nomit\Cryptography\Password\PasswordHasherFactoryInterface' => [['cryptography.password.factory']],
		'nomit\Cryptography\Password\PasswordHasherFactory' => [['cryptography.password.factory']],
		'nomit\Cryptography\Password\PasswordHasherInterface' => [
			[
				'cryptography.password.hasher.message_digest',
				'cryptography.password.hasher.native',
				'cryptography.password.hasher.pbkdf2',
				'cryptography.password.hasher.plaintext',
				'cryptography.password.hasher.sodium',
			],
		],
		'nomit\Cryptography\Password\MessageDigestPasswordHasher' => [['cryptography.password.hasher.message_digest']],
		'nomit\Cryptography\Password\NativePasswordHasher' => [['cryptography.password.hasher.native']],
		'nomit\Cryptography\Password\Pbkdf2PasswordHasher' => [['cryptography.password.hasher.pbkdf2']],
		'nomit\Cryptography\Password\PlaintextPasswordHasher' => [['cryptography.password.hasher.plaintext']],
		'nomit\Cryptography\Password\SodiumPasswordHasher' => [['cryptography.password.hasher.sodium']],
		'nomit\Cryptography\Password\UserPasswordHasherInterface' => [['cryptography.password.hasher.user']],
		'nomit\Cryptography\Password\UserPasswordHasher' => [['cryptography.password.hasher.user']],
		'nomit\Cryptography\EncrypterInterface' => [['cryptography.encrypter']],
		'nomit\Cryptography\Encrypter' => [['cryptography.encrypter']],
		'nomit\Template\Bridges\Kernel\KernelTemplateFactory' => [['template.kernelTemplateFactory']],
		'nomit\Kernel\Template\TemplateFactoryInterface' => [['template.templateFactory']],
		'nomit\Template\Bridges\Kernel\TemplateFactory' => [['template.templateFactory']],
		'nomit\Lock\Strategy\StrategyInterface' => [['lock.strategy.majority', 'lock.strategy.unanimous']],
		'nomit\Lock\Strategy\ConsensusStrategy' => [['lock.strategy.majority']],
		'nomit\Lock\Strategy\UnanimousStrategy' => [['lock.strategy.unanimous']],
		'nomit\Lock\PersistingStoreInterface' => [['lock.store.database']],
		'nomit\Lock\Store\DatabaseStore' => [['lock.store.database']],
		'nomit\Lock\LockFactoryInterface' => [['lock.factory']],
		'Psr\Log\LoggerAwareInterface' => [['lock.factory']],
		'nomit\Lock\LockFactory' => [['lock.factory']],
		'nomit\FileSystem\FileSystem' => [['filesystem.filesystem']],
		'nomit\Serialization\Encoder\EncoderInterface' => [
			[
				'serializer.encoder.json',
				'serializer.encoder.null',
				'serializer.encoder.php',
				'serializer.encoder.xml',
				'serializer.encoder.yaml',
				'serializer.encoder.md',
				'serializer.encoder.neon',
			],
		],
		'nomit\Serialization\SerializerInterface' => [
			[
				'serializer.encoder.json',
				'serializer.encoder.null',
				'serializer.encoder.php',
				'serializer.encoder.xml',
				'serializer.encoder.yaml',
				'serializer.encoder.md',
				'serializer.encoder.neon',
				'serializer.transformer.array',
				'serializer.transformer.closure',
				'serializer.transformer.array.flat',
				'serializer.transformer.json',
				'serializer.transformer.xml',
				'serializer.transformer.yaml',
			],
		],
		'nomit\Serialization\Encoder\JsonEncoder' => [['serializer.encoder.json']],
		'nomit\Serialization\Encoder\NullEncoder' => [['serializer.encoder.null']],
		'nomit\Serialization\Encoder\PhpEncoder' => [['serializer.encoder.php']],
		'nomit\Serialization\Encoder\XmlEncoder' => [['serializer.encoder.xml']],
		'nomit\Serialization\Encoder\YamlEncoder' => [['serializer.encoder.yaml']],
		'nomit\Serialization\Encoder\MarkdownEncoder' => [['serializer.encoder.md']],
		'nomit\Serialization\Encoder\NeonEncoder' => [['serializer.encoder.neon']],
		'nomit\Serialization\Transformer\AbstractTransformer' => [
			[
				'serializer.transformer.array',
				'serializer.transformer.closure',
				'serializer.transformer.array.flat',
				'serializer.transformer.json',
				'serializer.transformer.xml',
				'serializer.transformer.yaml',
			],
		],
		'nomit\Serialization\Transformer\TransformerInterface' => [
			[
				'serializer.transformer.array',
				'serializer.transformer.closure',
				'serializer.transformer.array.flat',
				'serializer.transformer.json',
				'serializer.transformer.xml',
				'serializer.transformer.yaml',
			],
		],
		'nomit\Serialization\Transformer\ArrayTransformer' => [
			[
				'serializer.transformer.array',
				'serializer.transformer.array.flat',
				'serializer.transformer.json',
				'serializer.transformer.xml',
				'serializer.transformer.yaml',
			],
		],
		'nomit\Serialization\Transformer\ClosureTransformer' => [['serializer.transformer.closure']],
		'nomit\Serialization\Transformer\FlatArrayTransformer' => [['serializer.transformer.array.flat']],
		'nomit\Serialization\Transformer\JsonTransformer' => [['serializer.transformer.json']],
		'nomit\Serialization\Transformer\XmlTransformer' => [['serializer.transformer.xml']],
		'nomit\Serialization\Transformer\YamlTransformer' => [['serializer.transformer.yaml']],
		'nomit\Serialization\SerializerResolverInterface' => [['serializer.resolver']],
		'nomit\Security\Session\Bridge\Error\SessionExtension' => [['session.bridge.error.extension']],
		'nomit\Security\Session\Token\Persistence\AbstractTokenPersistence' => [
			['session.token.persistence.filesystem', 'session.token.persistence.main'],
		],
		'nomit\Security\Session\Token\Persistence\TokenPersistenceInterface' => [
			['session.token.persistence.filesystem', 'session.token.persistence.main'],
		],
		'nomit\Security\Session\Token\Persistence\FileSystemTokenPersistence' => [
			['session.token.persistence.filesystem', 'session.token.persistence.main'],
		],
		'nomit\Security\Session\Fingerprint\AbstractFingerprintGenerator' => [['session.fingerprint_generator.main']],
		'nomit\Security\Session\Fingerprint\FingerprintGeneratorInterface' => [['session.fingerprint_generator.main']],
		'nomit\Security\Session\Fingerprint\UserAgentFingerprintGenerator' => [['session.fingerprint_generator.main']],
		'nomit\Security\Session\Context\SessionContextInterface' => [['session.context.main']],
		'JsonSerializable' => [
			[
				'session.context.main',
				'session.session.main',
				'application.security.profile.profile',
				'application.security.profile.repository.page',
				'application.security.profile.repository.photo',
			],
		],
		'nomit\Utilities\Concern\Serializable' => [
			[
				'session.context.main',
				'session.session.main',
				'application.security.profile.profile',
				'application.security.profile.repository.page',
				'application.security.profile.repository.photo',
			],
		],
		'nomit\Utilities\Concern\Jsonable' => [
			[
				'session.context.main',
				'session.session.main',
				'application.security.profile.profile',
				'application.security.profile.repository.page',
				'application.security.profile.repository.photo',
			],
		],
		'nomit\Utilities\Concern\Arrayable' => [
			[
				'session.context.main',
				'session.session.main',
				'web.request',
				'toasting.storage.bag.array',
				'toasting.storage.bag.session',
				'application.security.user.routing.summary.user',
				'application.security.authorization.summary.authorization',
				'application.security.profile.profile',
				'application.security.profile.repository.page',
				'application.security.profile.repository.photo',
				'application.controller.page.summary.page',
				'application.controller.photo.summary.photo',
				'application.summary.administration.summary',
				'application.summary.documentation.summary.documentation',
				'toasting.storage.bag',
			],
		],
		'nomit\Security\Session\Storage\StorageInterface' => [['session.context.main', 'session.session.main']],
		'nomit\Security\Session\Context\SessionContext' => [['session.context.main']],
		'nomit\Security\Session\Token\Validation\TokenValidatorInterface' => [['session.token.validator.main']],
		'nomit\Security\Session\Token\Validation\TokenValidator' => [['session.token.validator.main']],
		'nomit\Security\Session\SessionInterface' => [['session.session.main']],
		'Traversable' => [2 => ['session.session.main']],
		'IteratorAggregate' => [2 => ['session.session.main']],
		'nomit\Security\Session\Session' => [['session.session.main']],
		'nomit\Security\Session\Token\Identity\IdentityGeneratorInterface' => [['session.token.identity.main']],
		'nomit\Security\Session\Token\Identity\FingerprintIdentityGenerator' => [['session.token.identity.main']],
		'nomit\Security\Session\Token\Storage\TokenStorageInterface' => [['session.token.storage.main']],
		'nomit\Security\Session\Token\Storage\CookieTokenStorage' => [['session.token.storage.main']],
		'nomit\Security\Session\EventListener\ClosingSessionEventListener' => [['session.event_listener.closing.main']],
		'nomit\Security\Session\EventListener\InvalidatingSessionEventListener' => [
			['session.event_listener.invalidating.main'],
		],
		'nomit\Security\Session\EventListener\OpeningSessionEventListener' => [['session.event_listener.opening.main']],
		'nomit\Security\Session\EventListener\RegeneratingSessionEventListener' => [
			['session.event_listener.regenerating.main'],
		],
		'nomit\Security\Session\Command\CollectGarbageSessionCommand' => [2 => ['command.collect_garbage']],
		'nomit\Security\Session\SessionFactoryInterface' => [['session.factory']],
		'nomit\Security\Session\SessionFactory' => [['session.factory']],
		'nomit\Web\Request\RequestMatcherInterface' => [
			[
				'session.request_matcher.yQraWUf',
				'authentication.request_matcher.uXMEbhd',
				'authorization.request_matcher.HJam.mY',
				'authorization.request_matcher.zle3ai8',
				'authorization.request_matcher.VCweyO8',
				'authorization.request_matcher.hzV4IU4',
				'authorization.request_matcher.oOko5Z9',
				'authorization.request_matcher.OaNeQQr',
			],
		],
		'nomit\Web\Request\RequestMatcher' => [
			[
				'session.request_matcher.yQraWUf',
				'authentication.request_matcher.uXMEbhd',
				'authorization.request_matcher.HJam.mY',
				'authorization.request_matcher.zle3ai8',
				'authorization.request_matcher.VCweyO8',
				'authorization.request_matcher.hzV4IU4',
				'authorization.request_matcher.oOko5Z9',
				'authorization.request_matcher.OaNeQQr',
			],
		],
		'nomit\Security\Session\Firewall\Map\MatcherMapInterface' => [['session.firewall.map.matcher.main']],
		'nomit\Security\Session\Firewall\Map\MatcherMap' => [['session.firewall.map.matcher.main']],
		'nomit\Security\Session\Firewall\Listener\AbstractFirewallListener' => [['session.firewall.listener.matcher.main']],
		'nomit\Security\Session\Firewall\Listener\FirewallListenerInterface' => [
			['session.firewall.listener.matcher.main'],
		],
		'nomit\Security\Session\Firewall\Listener\MatcherFirewallListener' => [['session.firewall.listener.matcher.main']],
		'nomit\Security\Session\Firewall\Listener\ExceptionFirewallListener' => [
			['session.firewall.listener.exception.main'],
		],
		'nomit\Security\Session\Firewall\Map\FirewallMapInterface' => [['session.firewall.map.main']],
		'nomit\Security\Session\Firewall\Map\FirewallMap' => [['session.firewall.map.main']],
		'nomit\Security\Session\Firewall\FirewallInterface' => [['session.firewall.main']],
		'nomit\Security\Session\Firewall\Firewall' => [['session.firewall.main']],
		'nomit\Web\EventListener\AccessControlAllowedOriginHeaderEventListener' => [['web.event_listener.access_control']],
		'Psr\Http\Message\RequestFactoryInterface' => [['web.request.factory']],
		'nomit\Web\Request\RequestFactory' => [['web.request.factory']],
		'Psr\Http\Message\RequestInterface' => [['web.request']],
		'Psr\Http\Message\MessageInterface' => [['web.request', 'web.response']],
		'nomit\Web\Request\RequestInterface' => [['web.request']],
		'Countable' => [
			2 => [
				'web.requests',
				'toasting.storage.bag.array',
				'toasting.storage.bag.session',
				'toasting.storage.manager',
				'toasting.storage.bag',
			],
		],
		'nomit\Web\Request\RequestStack' => [['web.requests']],
		'nomit\Web\Response\ResponseInterface' => [['web.response']],
		'Psr\Http\Message\ResponseInterface' => [['web.response']],
		'nomit\Web\Response\Response' => [['web.response']],
		'nomit\Kernel\EventListener\ResponseEventListener' => [['web.event_listener.response']],
		'nomit\Resource\Command\ClearCacheResourceCommand' => [2 => ['resource.command.clear_cache']],
		'nomit\Security\Registration\EventListener\PasswordHashingEventListener' => [
			['registration.event_listener.password_hashing'],
		],
		'nomit\Security\Registration\EventListener\RegistrationEventListener' => [
			['registration.event_listener.registration'],
		],
		'nomit\Security\Registration\Handler\SuccessfulRegistrationHandlerInterface' => [
			['registration.handler.success.default'],
		],
		'nomit\Security\Registration\Handler\DefaultSuccessfulRegistrationHandler' => [
			['registration.handler.success.default'],
		],
		'nomit\Security\Registration\Handler\FailedRegistrationHandlerInterface' => [
			['registration.handler.failure.default'],
		],
		'nomit\Security\Registration\Handler\DefaultFailedRegistrationHandler' => [
			['registration.handler.failure.default'],
		],
		'nomit\Security\Registration\Token\Storage\AbstractTokenStorage' => [['registration.token.storage.session']],
		'nomit\Security\Registration\Token\Storage\TokenStorageInterface' => [['registration.token.storage.session']],
		'nomit\Security\Registration\Token\Storage\SessionTokenStorage' => [['registration.token.storage.session']],
		'nomit\Security\Authentication\User\DatabaseUserProvider' => [
			[
				'registration.user.provider.database',
				'profile.provider.database',
				'authentication.user.provider.concrete.database',
			],
		],
		'nomit\Security\Authentication\User\UserProviderInterface' => [
			[
				'registration.user.provider.database',
				'profile.provider.database',
				'authentication.user.provider.concrete.database',
			],
		],
		'nomit\Security\Registration\User\RegistrationUserProviderInterface' => [['registration.user.provider.database']],
		'nomit\Security\Registration\User\DatabaseRegistrationUserProvider' => [['registration.user.provider.database']],
		'nomit\View\AbstractView' => [['view.html']],
		'nomit\View\ViewInterface' => [['view.html']],
		'nomit\View\HtmlView' => [['view.html']],
		'nomit\Client\ClientInterface' => [['client.client']],
		'nomit\Security\Registration\Confirmation\EventListener\ConfirmationEventListener' => [
			['confirmation.event_listener.confirmation'],
		],
		'nomit\Security\Registration\Confirmation\Handler\SuccessfulConfirmationHandlerInterface' => [
			['confirmation.handler.success.default'],
		],
		'nomit\Security\Registration\Confirmation\Handler\DefaultSuccessfulConfirmationHandler' => [
			['confirmation.handler.success.default'],
		],
		'nomit\Security\Registration\Confirmation\Handler\FailedConfirmationHandlerInterface' => [
			['confirmation.handler.failure.default'],
		],
		'nomit\Security\Registration\Confirmation\Handler\DefaultFailedConfirmationHandler' => [
			['confirmation.handler.failure.default'],
		],
		'nomit\Security\Registration\Confirmation\Mail\MailerInterface' => [['confirmation.mail.mailer']],
		'nomit\Security\Registration\Confirmation\Mail\Mailer' => [['confirmation.mail.mailer']],
		'nomit\Security\Registration\Confirmation\Token\Persistence\TokenPersistenceInterface' => [
			['confirmation.token.persistence.database'],
		],
		'nomit\Security\Registration\Confirmation\Token\Persistence\DatabaseTokenPersistence' => [
			['confirmation.token.persistence.database'],
		],
		'nomit\Notification\Command\PushToEveryoneNotificationCommand' => [2 => ['notification.command.push_to_everyone']],
		'nomit\Notification\Command\PushToUserNotificationCommand' => [2 => ['notification.command.push_to_user']],
		'nomit\Notification\Command\GetUserNotificationCommand' => [2 => ['notification.command.get_user']],
		'nomit\Notification\Command\ClearUserNotificationCommand' => [2 => ['notification.command.clear_user']],
		'nomit\Notification\EventListener\NotifyingEventListener' => [['notification.event_listener.notifying']],
		'nomit\Notification\EventListener\RemoveNotificationsEventListener' => [
			['notification.event_listener.remove_notifications'],
		],
		'nomit\Notification\EventListener\StampNotificationsEventListener' => [
			['notification.event_listener.stamp_notifications'],
		],
		'nomit\Notification\EventListener\StoreNotificationsEventListener' => [
			['notification.event_listener.store_notifications'],
		],
		'nomit\Notification\Response\View\ViewInterface' => [
			['notification.response.view.array', 'notification.response.view.serialized'],
		],
		'nomit\Notification\Response\View\ArrayView' => [
			['notification.response.view.array', 'notification.response.view.serialized'],
		],
		'nomit\Notification\Response\View\SerializedView' => [['notification.response.view.serialized']],
		'nomit\Notification\Storage\StorageManagerInterface' => [['notification.storage.manager']],
		'nomit\Notification\Storage\StorageManager' => [['notification.storage.manager']],
		'nomit\Notification\Storage\Bag\BagInterface' => [
			['notification.storage.bag.array', 'notification.storage.bag.filesystem'],
		],
		'nomit\Notification\Storage\Bag\ArrayBag' => [['notification.storage.bag.array']],
		'nomit\Notification\Storage\Bag\FileSystemBag' => [['notification.storage.bag.filesystem']],
		'nomit\Notification\NotifierInterface' => [['notification.notifier']],
		'nomit\Notification\Notifier' => [['notification.notifier']],
		'nomit\Messenger\Command\ConsumeMessagesCommand' => [2 => ['messenger.command.consume']],
		'nomit\Messenger\Handler\HandlerFactoryInterface' => [
			['messenger.handler.callback', 'messenger.handler.closure', 'messenger.handler.invokable'],
		],
		'nomit\Messenger\Handler\CallbackHandlerFactory' => [['messenger.handler.callback']],
		'nomit\Messenger\Handler\ClosureHandlerFactory' => [['messenger.handler.closure']],
		'nomit\Messenger\Handler\InvokableHandlerFactory' => [['messenger.handler.invokable']],
		'nomit\Messenger\Serialization\SerializerInterface' => [['messenger.serializer']],
		'nomit\Messenger\Serialization\Serializer' => [['messenger.serializer']],
		'nomit\Messenger\MessageBusInterface' => [['messenger.bus']],
		'nomit\Messenger\MessageBus' => [['messenger.bus']],
		'nomit\Messenger\Router\HandlerRouter' => [['messenger.handler.router']],
		'nomit\Messenger\Router\RouterInterface' => [['messenger.handler.router']],
		'nomit\Messenger\Router\ContainerHandlerRouter' => [['messenger.handler.router']],
		'nomit\Messenger\Producer\ProducerInterface' => [['messenger.producer']],
		'nomit\Messenger\Producer\Producer' => [['messenger.producer']],
		'nomit\Messenger\Driver\DriverInterface' => [['messenger.driver.filesystem.system']],
		'nomit\Messenger\Driver\FileSystemDriver' => [['messenger.driver.filesystem.system']],
		'nomit\Messenger\Queue\AbstractQueue' => [['messenger.queue.system']],
		'nomit\Messenger\Queue\QueueInterface' => [['messenger.queue.system']],
		'nomit\Messenger\Queue\PersistentQueue' => [['messenger.queue.system']],
		'nomit\Messenger\Queue\QueueFactoryInterface' => [['messenger.queue.factory']],
		'nomit\Messenger\Queue\QueueFactory' => [['messenger.queue.factory']],
		'nomit\Messenger\Repository\HandlerRepositoryInterface' => [['messenger.handler.repository']],
		'nomit\Messenger\Repository\HandlerRepository' => [['messenger.handler.repository']],
		'nomit\Messenger\Handler\HandlerResolverInterface' => [['messenger.handler.resolver']],
		'nomit\Messenger\Handler\MapHandlerResolver' => [['messenger.handler.resolver']],
		'nomit\Messenger\Consumer\ConsumerInterface' => [['messenger.consumer']],
		'nomit\Messenger\Consumer\Consumer' => [['messenger.consumer']],
		'nomit\Messenger\Worker\WorkerInterface' => [['messenger.worker']],
		'nomit\Messenger\Worker\Worker' => [['messenger.worker']],
		'nomit\Messenger\Configuration\ConfigurationInterface' => [['config']],
		'nomit\Messenger\Configuration\Configuration' => [['config']],
		'nomit\Utilities\Serialization\Markdown\ParserInterface' => [['markdown.parser']],
		'nomit\Security\Csrf\Token\Generator\TokenGeneratorInterface' => [['csrf.token.generator.uri_safe']],
		'nomit\Security\Csrf\Token\Generator\UriSafeTokenGenerator' => [['csrf.token.generator.uri_safe']],
		'nomit\Security\Csrf\Token\Storage\ClearableTokenStorageInterface' => [['csrf.token.storage.session']],
		'nomit\Security\Csrf\Token\TokenStorageInterface' => [['csrf.token.storage.session']],
		'nomit\Security\Csrf\Token\Storage\SessionTokenStorage' => [['csrf.token.storage.session']],
		'nomit\Security\Csrf\TokenManagerInterface' => [['csrf.manager']],
		'nomit\Security\Csrf\TokenManager' => [['csrf.manager']],
		'nomit\Security\PasswordReset\EventListener\PasswordHashingPasswordResetEventListener' => [
			['password_reset.event_listener.password_hashing'],
		],
		'nomit\Security\PasswordReset\EventListener\PerformPasswordResetEventListener' => [
			['password_reset.event_listener.perform'],
		],
		'nomit\Security\PasswordReset\EventListener\RequestPasswordResetEventListener' => [
			['password_reset.event_listener.request'],
		],
		'nomit\Security\PasswordReset\EventListener\ValidatePasswordResetEventListener' => [
			['password_reset.event_listener.validate'],
		],
		'nomit\Security\PasswordReset\Mail\MailerInterface' => [['password_reset.mail.mailer']],
		'nomit\Security\PasswordReset\Mail\Mailer' => [['password_reset.mail.mailer']],
		'nomit\Security\PasswordReset\Token\Persistence\TokenPersistenceInterface' => [
			['password_reset.token.persistence.database'],
		],
		'nomit\Security\PasswordReset\Token\Persistence\DatabaseTokenPersistence' => [
			['password_reset.token.persistence.database'],
		],
		'nomit\Security\PasswordReset\Token\TokenManagerInterface' => [['password_reset.token.manager']],
		'nomit\Security\PasswordReset\Token\TokenManager' => [['password_reset.token.manager']],
		'nomit\Routing\Loader\LoaderInterface' => [['routing.loader']],
		'nomit\Routing\Loader\ConfigLoader' => [['routing.loader']],
		'nomit\Routing\ReferenceResolverInterface' => [['routing.reference_resolver']],
		'nomit\Routing\ReferenceResolver' => [['routing.reference_resolver']],
		'Psr\Http\Server\MiddlewareInterface' => [['routing.router']],
		'nomit\Routing\Router' => [['routing.router']],
		'nomit\Security\Profile\EventListener\PasswordHashingEventListener' => [
			['profile.event_listener.password_hashing'],
		],
		'nomit\Security\Profile\EventListener\UpdateProfileEventListener' => [['profile.event_listener.update_profile']],
		'nomit\Security\Profile\EventListener\ViewProfileEventListener' => [['profile.event_listener.view_profile']],
		'nomit\Security\Profile\Handler\SuccessfulProfileUpdateHandlerInterface' => [['profile.handler.success.default']],
		'nomit\Security\Profile\Handler\DefaultSuccessfulProfileUpdateHandler' => [['profile.handler.success.default']],
		'nomit\Security\Profile\Handler\FailedProfileUpdateHandlerInterface' => [['profile.handler.failure.default']],
		'nomit\Security\Profile\Handler\DefaultFailedProfileUpdateHandler' => [['profile.handler.failure.default']],
		'nomit\Security\Profile\User\ProfileUserProviderInterface' => [['profile.provider.database']],
		'nomit\Security\Profile\User\DatabaseProfileUserProvider' => [['profile.provider.database']],
		'nomit\Console\Command\CompleteCommand' => [2 => ['console.command.complete']],
		'nomit\Console\Command\DumpCompletionCommand' => [2 => ['console.command.dump_completion']],
		'nomit\Console\Command\HelpCommand' => [2 => ['console.command.help']],
		'nomit\Console\Command\ListCommand' => [2 => ['console.command.list']],
		'nomit\Console\Command\WelcomeCommand' => [2 => ['console.command.welcome']],
		'nomit\Console\Input\Reader\ReaderInterface' => [['console.input.reader.stream']],
		'nomit\Console\Input\Reader\StreamReader' => [['console.input.reader.stream']],
		'nomit\Console\Output\Writer\WriterInterface' => [
			[
				'console.output.writer.buffer',
				'console.output.writer.file',
				'console.output.writer.stderr',
				'console.output.writer.stdout',
			],
		],
		'nomit\Console\Output\Writer\BufferWriter' => [['console.output.writer.buffer']],
		'nomit\Console\Output\Writer\FileWriter' => [['console.output.writer.file']],
		'nomit\Console\Output\Writer\StreamWriter' => [['console.output.writer.stderr', 'console.output.writer.stdout']],
		'nomit\Console\Output\Writer\ErrorWriterInterface' => [['console.output.writer.stderr']],
		'nomit\Console\Output\Writer\StdErrWriter' => [['console.output.writer.stderr']],
		'nomit\Console\Output\Writer\StdOutWriter' => [['console.output.writer.stdout']],
		'nomit\Console\Command\QueueCommand' => [2 => ['console.command.queue.enqueue', 'console.command.queue.dequeue']],
		'nomit\Console\Command\EnqueueCommand' => [2 => ['console.command.queue.enqueue']],
		'nomit\Console\Command\DequeueCommand' => [2 => ['console.command.queue.dequeue']],
		'nomit\Property\Accessor\PropertyAccessorInterface' => [['property_accessor.accessor']],
		'nomit\Property\Accessor\PropertyAccessor' => [['property_accessor.accessor']],
		'nomit\Toasting\EventListener\PresetEnvelopesEventListener' => [['toasting.event_listener.preset_envelopes']],
		'nomit\Toasting\EventListener\RemoveEnvelopesEventListener' => [['toasting.event_listener.remove_envelopes']],
		'nomit\Toasting\EventListener\StampEnvelopesEventListener' => [['toasting.event_listener.stamp_envelopes']],
		'nomit\Toasting\EventListener\StoreEnvelopesEventListener' => [['toasting.event_listener.store_envelopes']],
		'nomit\Toasting\EventListener\ToastingEventListener' => [['toasting.event_listener.toasting']],
		'nomit\Toasting\Response\View\ViewInterface' => [
			['toasting.response.view.array', 'toasting.response.view.serialized'],
		],
		'nomit\Toasting\Response\View\ArrayView' => [['toasting.response.view.array', 'toasting.response.view.serialized']],
		'nomit\Toasting\Response\View\SerializedView' => [['toasting.response.view.serialized']],
		'nomit\Toasting\Storage\Bag\BagInterface' => [['toasting.storage.bag.array', 'toasting.storage.bag.session']],
		'nomit\Toasting\Storage\Bag\ArrayBag' => [['toasting.storage.bag.array']],
		'nomit\Toasting\Storage\Bag\SessionBag' => [['toasting.storage.bag.session']],
		'nomit\Toasting\ToasterInterface' => [['toasting.toaster']],
		'nomit\Toasting\Toaster' => [['toasting.toaster']],
		'nomit\RateLimiter\Storage\StorageInterface' => [['rate_limiter.storage.cache', 'rate_limiter.storage.in_memory']],
		'nomit\RateLimiter\Storage\CacheStorage' => [['rate_limiter.storage.cache']],
		'nomit\RateLimiter\Storage\InMemoryStorage' => [['rate_limiter.storage.in_memory']],
		'nomit\RateLimiter\RateLimiterFactory' => [
			[
				'rate_limiter.factory.anonymous_api',
				'rate_limiter.factory.authenticated_api',
				'rate_limiter.factory._login_local.main',
				'rate_limiter.factory._login_globalmain',
			],
		],
		'nomit\Mail\MailerInterface' => [['mail.mailer']],
		'Application\EventListener\AbstractRoutingEventListener' => [
			[
				'application.resource.event_listener',
				'application.security.authentication.routing.event_listener.routing',
				'application.security.user.routing.event_listener',
			],
		],
		'Application\Resource\EventListener\ResourceEventListener' => [['application.resource.event_listener']],
		'Application\Resource\ResourceDumperInterface' => [['application.resource.dumper']],
		'Application\Resource\ResourceDumper' => [['application.resource.dumper']],
		'Application\Security\Authentication\EventListener\RoutingAuthenticationEventListener' => [
			['application.security.authentication.routing.event_listener.routing'],
		],
		'Application\Summary\Administration\AbstractSummaryExtension' => [
			[
				'application.security.user.routing.summary.user',
				'application.security.authorization.summary.authorization',
				'application.controller.page.summary.page',
				'application.controller.photo.summary.photo',
				'application.summary.documentation.summary.documentation',
			],
		],
		'Application\Summary\Administration\SummaryExtensionInterface' => [
			[
				'application.security.user.routing.summary.user',
				'application.security.authorization.summary.authorization',
				'application.controller.page.summary.page',
				'application.controller.photo.summary.photo',
				'application.summary.documentation.summary.documentation',
			],
		],
		'Application\Security\User\Summary\UserSummaryExtension' => [['application.security.user.routing.summary.user']],
		'Application\Security\User\EventListener\RoutingUserEventListener' => [
			['application.security.user.routing.event_listener'],
		],
		'Application\Security\Authorization\Summary\AuthorizationSummaryExtension' => [
			['application.security.authorization.summary.authorization'],
		],
		'Application\RateLimiter\EventListener\ApplicationThrottlingEventListener' => [
			['application.rate_limiter.event_listener.application_throttling'],
		],
		'nomit\Security\Profile\Profile' => [['application.security.profile.profile']],
		'nomit\Security\Profile\ProfileInterface' => [['application.security.profile.profile']],
		'Application\Security\Profile\ApplicationProfile' => [['application.security.profile.profile']],
		'nomit\Security\Profile\Repository\Repository' => [
			['application.security.profile.repository.page', 'application.security.profile.repository.photo'],
		],
		'nomit\Security\Profile\Repository\RepositoryInterface' => [
			['application.security.profile.repository.page', 'application.security.profile.repository.photo'],
		],
		'Application\Security\Profile\Repository\PageRepository' => [['application.security.profile.repository.page']],
		'Application\Security\Profile\Repository\PhotoRepository' => [['application.security.profile.repository.photo']],
		'Application\Controller\Page\Summary\PageSummaryExtension' => [['application.controller.page.summary.page']],
		'Application\Controller\Photo\Summary\PhotoSummaryExtension' => [['application.controller.photo.summary.photo']],
		'Application\Summary\Administration\AdministrationSummaryInterface' => [
			['application.summary.administration.summary'],
		],
		'Application\Summary\Administration\AdministrationSummary' => [['application.summary.administration.summary']],
		'Application\Controller\Documentation\Summary\DocumentationSummaryExtension' => [
			['application.summary.documentation.summary.documentation'],
		],
		'nomit\Kernel\KernelInterface' => [['kernel', 'kernel.kernel']],
		'nomit\Kernel\Kernel' => [['kernel']],
		'nomit\Kernel\Secret\AbstractVault' => [['kernel.secret.vault']],
		'nomit\Kernel\Secret\VaultInterface' => [['kernel.secret.vault']],
		'nomit\Kernel\Secret\OpenSslVault' => [['kernel.secret.vault']],
		'nomit\Kernel\EventListener\ExceptionEventListener' => [['error.event_listener.exception']],
		'nomit\Security\Authentication\Utilities\AuthenticationUtilities' => [
			['authentication.utilities.authentication.main'],
		],
		'nomit\Security\Authentication\Utilities\WebUtilities' => [['authentication.utilities.web.main']],
		'nomit\Security\Authentication\EventListener\UserProviderEventListener' => [
			['authentication.event_listener.user_provider.main'],
		],
		'nomit\Security\Authentication\Firewall\Listener\ChannelFirewallListener' => [
			['authentication.firewall.listener.channel.main'],
		],
		'nomit\Security\Authentication\Firewall\Listener\SessionFirewallListener' => [
			['authentication.firewall.listener.session.main'],
		],
		'nomit\Web\RateLimiter\AbstractRequestRateLimiter' => [
			[
				'authentication.rate_limiter.authentication_throttling.main.limiter',
				'application.rate_limiter.application_request_rate_limiter',
			],
		],
		'nomit\Web\RateLimiter\RequestRateLimiterInterface' => [
			[
				'authentication.rate_limiter.authentication_throttling.main.limiter',
				'application.rate_limiter.application_request_rate_limiter',
			],
		],
		'nomit\Security\Authentication\RateLimiter\AuthenticationRateLimiter' => [
			['authentication.rate_limiter.authentication_throttling.main.limiter'],
		],
		'nomit\Security\Authentication\Authenticator\AbstractFormAuthenticator' => [
			['authentication.authenticator.form.main'],
		],
		'nomit\Security\Authentication\Authenticator\AbstractAuthenticator' => [
			['authentication.authenticator.form.main', 'authentication.authenticator.registration'],
		],
		'nomit\Security\Authentication\Authenticator\AuthenticatorInterface' => [
			[
				'authentication.authenticator.form.main',
				'authentication.authenticator.registration',
				'authentication.authenticator.remember_me.main',
			],
		],
		'nomit\Security\Authentication\Authenticator\FormAuthenticator' => [['authentication.authenticator.form.main']],
		'nomit\Security\Authentication\Handler\SuccessfulAuthenticationHandlerInterface' => [
			['authentication.handler.success.default', 'authentication.handler.success.main.form'],
		],
		'nomit\Security\Authentication\Handler\DefaultSuccessfulAuthenticationHandler' => [
			['authentication.handler.success.default'],
		],
		'nomit\Security\Authentication\Handler\CustomSuccessfulAuthenticationHandler' => [
			['authentication.handler.success.main.form'],
		],
		'nomit\Security\Authentication\Handler\FailedAuthenticationHandlerInterface' => [
			['authentication.handler.failure.default', 'authentication.handler.failure.main.form'],
		],
		'nomit\Security\Authentication\Handler\DefaultFailedAuthenticationHandler' => [
			['authentication.handler.failure.default'],
		],
		'nomit\Security\Authentication\Handler\CustomFailedAuthenticationHandler' => [
			['authentication.handler.failure.main.form'],
		],
		'nomit\Security\Authentication\EntryPoint\EntryPointInterface' => [['authentication.entry_point.form.main']],
		'nomit\Security\Authentication\EntryPoint\FormEntryPoint' => [['authentication.entry_point.form.main']],
		'nomit\Security\Authentication\Authenticator\RegistrationAuthenticator' => [
			['authentication.authenticator.registration'],
		],
		'nomit\Security\Authentication\RememberMe\Handler\AbstractRememberMeHandler' => [
			['authentication.remember_me.handler.main'],
		],
		'nomit\Security\Authentication\RememberMe\Handler\RememberMeHandlerInterface' => [
			['authentication.remember_me.handler.main'],
		],
		'nomit\Security\Authentication\RememberMe\Handler\PersistentRememberMeHandler' => [
			['authentication.remember_me.handler.main'],
		],
		'nomit\Security\Authentication\RememberMe\Token\Storage\RememberMeTokenStorageInterface' => [
			['authentication.remember_me.token.storage.main'],
		],
		'nomit\Security\Authentication\RememberMe\Token\Storage\MemoryRememberMeTokenStorage' => [
			['authentication.remember_me.token.storage.main'],
		],
		'nomit\Security\Authentication\RememberMe\Token\Persistence\RememberMeTokenPersistenceInterface' => [
			['authentication.remember_me.token.persistence.main'],
		],
		'nomit\Security\Authentication\RememberMe\Token\Persistence\CacheRememberMeTokenPersistence' => [
			['authentication.remember_me.token.persistence.main'],
		],
		'nomit\Security\Authentication\Authenticator\RememberMeAuthenticator' => [
			['authentication.authenticator.remember_me.main'],
		],
		'nomit\Security\Authentication\Authenticator\AuthenticatorResolverInterface' => [
			['authentication.authenticator.resolver.main'],
		],
		'nomit\Security\Authentication\Authenticator\UserAuthenticatorInterface' => [
			['authentication.authenticator.resolver.main'],
		],
		'nomit\Security\Authentication\Authenticator\AuthenticatorResolver' => [
			['authentication.authenticator.resolver.main'],
		],
		'nomit\Security\Authentication\Firewall\Listener\AuthenticatorResolverFirewallListener' => [
			['authentication.firewall.listener.authenticator_resolver'],
		],
		'nomit\Security\Authentication\EventListener\UserValidatorEventListener' => [
			['authentication.event_listener.user_validator.main'],
		],
		'nomit\Security\Authentication\User\UserValidatorInterface' => [['authentication.user.validator.main']],
		'nomit\Security\Authentication\User\UserValidator' => [['authentication.user.validator.main']],
		'nomit\Security\Authentication\Firewall\Listener\RegistrationFirewallListener' => [
			['authentication.firewall.listener.registration.main'],
		],
		'nomit\Security\Session\Firewall\Listener\AuthorizationFirewallListener' => [
			['authentication.firewall.listener.authorization.main'],
		],
		'nomit\Security\Authentication\Firewall\Listener\ExceptionFirewallListener' => [
			['authentication.firewall.listener.exception.main'],
		],
		'nomit\Security\Authentication\Session\SessionAuthenticationStrategyInterface' => [
			['authentication.session.strategy.default.main'],
		],
		'nomit\Security\Authentication\Session\DefaultSessionAuthenticationStrategy' => [
			['authentication.session.strategy.default.main'],
		],
		'nomit\Security\Authentication\Firewall\FirewallMapInterface' => [['authentication.firewall.map.main']],
		'nomit\Security\Authentication\Firewall\FirewallMap' => [['authentication.firewall.map.main']],
		'nomit\Security\Authorization\Role\RoleHierarchyInterface' => [['authorization.role.hierarchy']],
		'nomit\Security\Authorization\Role\RoleHierarchy' => [['authorization.role.hierarchy']],
		'nomit\Security\Registration\Confirmation\ConfirmationManagerInterface' => [['confirmation.manager']],
		'nomit\Security\Registration\Confirmation\ConfirmationManager' => [['confirmation.manager']],
		'nomit\Notification\Response\ResponderInterface' => [['notification.response.responder']],
		'nomit\Notification\Response\Responder' => [['notification.response.responder']],
		'nomit\Notification\Storage\StorageInterface' => [['notification.storage.bag']],
		'nomit\Notification\Storage\BagStorage' => [['notification.storage.bag']],
		'nomit\Notification\Serialization\SerializerInterface' => [['notification.serialization.serializer']],
		'nomit\Notification\Serialization\Serializer' => [['notification.serialization.serializer']],
		'nomit\Console\Command\CommandRepositoryInterface' => [['console.command.repository']],
		'nomit\Console\Command\CommandRepository' => [['console.command.repository']],
		'nomit\Console\Input\InputFactory' => [['console.input.factory']],
		'nomit\Console\Output\OutputFactory' => [['console.output.factory']],
		'nomit\Console\Console' => [['console.kernel']],
		'nomit\Console\ConsoleInterface' => [['console.kernel']],
		'nomit\DependencyInjection\ContainerAwareInterface' => [['console.kernel']],
		'nomit\Console\KernelInterface' => [['console.kernel']],
		'nomit\Console\Kernel' => [['console.kernel']],
		'nomit\Toasting\Response\ResponderInterface' => [['toasting.response.responder']],
		'nomit\Toasting\Response\Responder' => [['toasting.response.responder']],
		'nomit\Toasting\Storage\StorageManagerInterface' => [['toasting.storage.manager']],
		'nomit\Toasting\Storage\StorageManager' => [['toasting.storage.manager']],
		'nomit\Toasting\Storage\StorageInterface' => [['toasting.storage.bag']],
		'nomit\Toasting\Storage\BagStorage' => [['toasting.storage.bag']],
		'Application\RateLimiter\ApplicationRequestRateLimiter' => [
			['application.rate_limiter.application_request_rate_limiter'],
		],
	];


	public function __construct(array $params = [])
	{
		parent::__construct($params);
		$this->parameters += [
			'servers' => ['api' => '/api/'],
			'kernel' => [
				'secret' => 'gFjg3WV9sO0chayT9cRb8baq2j4MfNPJ',
				'charset' => 'UTF-8',
				'default_locale' => 'en',
				'env' => 'dev',
			],
			'resource' => ['manifest' => ['path' => '/Users/im.nomit/Sites/nomit.php/config/manifest/manifest.json']],
			'paths' => [
				'root' => '/Users/im.nomit/Sites/nomit.php/',
				'tmp' => '/Users/im.nomit/Sites/nomit.php/tmp/',
				'resources' => '/Users/im.nomit/Sites/nomit.php/public/resources/',
				'config' => '/Users/im.nomit/Sites/nomit.php/config/',
				'app' => '/Users/im.nomit/Sites/nomit.php/app/',
				'public' => '/Users/im.nomit/Sites/nomit.php/public/',
				'src' => '/Users/im.nomit/Sites/nomit.php/src/',
				'build' => '/Users/im.nomit/Sites/nomit.php/build/',
			],
			'debugMode' => true,
			'productionMode' => false,
			'consoleMode' => true,
			'env' => [
				'PATH' => '/Applications/MAMP/bin/php/php8.0.8/bin:/opt/local/bin:/opt/local/sbin:/Applications/MAMP/bin/php/php8.0.8/bin:/usr/local/bin:/System/Cryptexes/App/usr/bin:/usr/bin:/bin:/usr/sbin:/sbin:/Applications/VMware Fusion.app/Contents/Public:/usr/local/MacGPG2/bin',
				'__CFBundleIdentifier' => 'com.jetbrains.intellij',
				'SHELL' => '/bin/zsh',
				'TERM' => 'xterm-256color',
				'USER' => 'im.nomit',
				'TMPDIR' => '/var/folders/y0/zhkwyjks5vs4b7m1w3r9m4y80000gn/T/',
				'COMMAND_MODE' => 'unix2003',
				'TERMINAL_EMULATOR' => 'JetBrains-JediTerm',
				'LOGIN_SHELL' => '1',
				'__INTELLIJ_COMMAND_HISTFILE__' => '/Users/im.nomit/Library/Caches/JetBrains/IntelliJIdea2021.1/terminal/history/nomit.php-history',
				'SSH_AUTH_SOCK' => '/private/tmp/com.apple.launchd.tp0ZRLSQM2/Listeners',
				'XPC_FLAGS' => '0x0',
				'TERM_SESSION_ID' => '5bd5ad0b-4718-4954-ac22-8611ed072ff6',
				'__CF_USER_TEXT_ENCODING' => '0x1F5:0x0:0x52',
				'LOGNAME' => 'im.nomit',
				'LC_CTYPE' => 'en_CA.UTF-8',
				'XPC_SERVICE_NAME' => '0',
				'HOME' => '/Users/im.nomit',
				'SHLVL' => '1',
				'PWD' => '/Users/im.nomit/Sites/nomit.php/bin',
				'OLDPWD' => '/Users/im.nomit/Sites/nomit.php/bin',
				'MAMP_PHP' => '/Applications/MAMP/bin/php/php8.0.8/bin',
				'_' => '/Applications/MAMP/bin/php/php8.0.8/bin/php',
				'APP_ENV' => 'dev',
				'APP_DEBUG' => '1',
				'APP_SECRET' => 'gFjg3WV9sO0chayT9cRb8baq2j4MfNPJ',
				'DATABASE_DSN' => 'mysql:host=127.0.0.1;dbname=nomit2',
				'DATABASE_USERNAME' => 'nomit',
				'COLOR_CLI' => '1',
				'NOMIT_DOTENV_VARS' => 'APP_ENV,APP_DEBUG,APP_SECRET,DATABASE_DSN,DATABASE_USERNAME,COLOR_CLI',
			],
			'firewalls' => [
				'main' => (object) [
					'pattern' => '^.+/',
					'token' => (object) [
						'name' => '_security.authentication.token.session',
						'key' => null,
					],
					'anonymous' => 'lazy',
					'lazy' => true,
					'methods' => ['GET', 'POST'],
					'security' => true,
					'stateless' => false,
					'user_provider' => 'database',
					'user_validator' => 'authentication.user.validator',
					'authenticator' => 'form',
					'entry_point' => 'authentication.entry_point.form',
					'login_throttling' => (object) [
						'maximum_attempts' => 10,
						'interval' => '5 minutes',
					],
					'logout' => (object) [
						'path' => '/api/account/logout',
						'target' => '/api/account/user/?initial=1',
						'clear_session' => true,
						'delete_cookies' => true,
						'cookies' => [],
						'csrf_parameter' => null,
						'csrf_token_id' => null,
						'csrf_token_generator' => null,
					],
					'session' => (object) [
						'strategy_service' => 'default',
						'strategy' => 'migrate',
					],
					'registration' => (object) [
						'path' => '/api/account/register',
					],
					'authenticators' => (object) [
						'form' => (object) [
							'check_path' => '/api/account/login/authenticate',
							'login_path' => '/api/account/login',
							'use_forward' => false,
							'always_use_default_target_path' => true,
							'default_target_path' => '/api/account/user/?initial=1',
							'target_path_parameter' => '~',
							'use_referrer' => true,
							'failure_handler' => 'default',
							'success_handler' => 'default',
							'failure_path' => '/api/account/login/failure',
							'failure_forward' => false,
							'failure_path_parameter' => '_failure_path',
							'email_parameter' => 'email',
							'username_parameter' => 'username',
							'password_parameter' => 'password',
							'csrf_parameter' => 'authenticate.login.token',
							'csrf_token_id' => 'authenticate',
							'csrf_token_generator' => 'csrf.token.generator.uri_safe',
							'post_only' => false,
							'remember_me' => true,
							'require_previous_session' => true,
						],
						'remember_me' => (object) [
							'signature_properties' => ['password', 'last_login_datetime', 'last_login_ip_address'],
							'name' => '_casper_security_authentication_remember_me',
							'lifetime' => 31536000,
							'remember_me_parameter' => 'remember_me',
							'secret' => 'gFjg3WV9sO0chayT9cRb8baq2j4MfNPJ',
							'token_storage' => ['service' => 'memory', 'memory' => 'test'],
							'token_persistence' => [
								'service' => 'cache',
								'cache' => ['outdated_token_ttl' => 3600, 'key_prefix' => '_remember_me-stale-'],
							],
							'service' => null,
						],
						'authentication_throttling' => (object) [
							'maximum_attempts' => 10,
							'interval' => '5 minutes',
							'lock_factory' => 'lock.factory',
							'limiter' => 'authentication.rate_limiter.authentication_throttling.main.limiter',
						],
					],
					'validators' => (object) [
						'banned' => (object) [
							'database' => (object) [
								'table' => 'user_bans',
							],
						],
					],
					'host' => null,
					'port' => null,
					'ports' => (object) [
						'http' => 80,
						'https' => 443,
					],
				],
			],
			'user_providers' => ['authentication.user.provider.concrete.database'],
		];
	}


	public function createServiceApplication__controller__page__summary__page(): Application\Controller\Page\Summary\PageSummaryExtension
	{
		return new Application\Controller\Page\Summary\PageSummaryExtension(
			$this->getService('model.1'),
			$this->getService('model.4'),
			['authentication.user.provider.concrete.database'],
			$this,
		);
	}


	public function createServiceApplication__controller__photo__summary__photo(): Application\Controller\Photo\Summary\PhotoSummaryExtension
	{
		return new Application\Controller\Photo\Summary\PhotoSummaryExtension(
			$this->getService('model.15'),
			$this->getService('model.17'),
			['authentication.user.provider.concrete.database'],
			$this,
		);
	}


	public function createServiceApplication__rate_limiter__application_request_rate_limiter(): Application\RateLimiter\ApplicationRequestRateLimiter
	{
		return new Application\RateLimiter\ApplicationRequestRateLimiter($this->getService('rate_limiter.factory.anonymous_api'));
	}


	public function createServiceApplication__rate_limiter__event_listener__application_throttling(): Application\RateLimiter\EventListener\ApplicationThrottlingEventListener
	{
		return new Application\RateLimiter\EventListener\ApplicationThrottlingEventListener($this->getService('application.rate_limiter.application_request_rate_limiter'));
	}


	public function createServiceApplication__resource__dumper(): Application\Resource\ResourceDumper
	{
		return new Application\Resource\ResourceDumper($this, $this->getService('serializer'));
	}


	public function createServiceApplication__resource__event_listener(): Application\Resource\EventListener\ResourceEventListener
	{
		return new Application\Resource\EventListener\ResourceEventListener(
			$this,
			$this->getService('application.resource.dumper'),
			[
				'attribute_name' => '_resource_event_listener.route',
				'routes' => [
					'resource' => (object) [
						'path' => '/api/resource/{path<@path>}',
						'handler' => 'getResource',
					],
					'manifest' => (object) [
						'path' => '/api/resources/manifest/{path<@path>}',
						'handler' => 'getResourceManifest',
					],
				],
			],
			$this->getService('logger.logger.main.resource'),
		);
	}


	public function createServiceApplication__security__authentication__routing__event_listener__routing(): Application\Security\Authentication\EventListener\RoutingAuthenticationEventListener
	{
		return new Application\Security\Authentication\EventListener\RoutingAuthenticationEventListener(
			$this,
			$this->getService('csrf.manager'),
			$this->getService('authentication.token.storage.session'),
			$this->getService('authentication.user.provider.concrete.database'),
			$this->getService('authentication.utilities.authentication.main'),
			[
				'registration_csrf_token_parameter' => '_registration_token',
				'login_csrf_token_parameter' => '_login_token',
				'attribute_name' => '_authentication_routing_event_listener.route',
				'routes' => [
					'login' => (object) [
						'path' => '/api/account/login',
						'handler' => 'getLoginToken',
					],
					'register' => (object) [
						'path' => '/api/account/register',
						'handler' => 'getRegistrationToken',
					],
					'authentication_data' => (object) [
						'path' => '/api/account/authenticate/data',
						'handler' => 'getAuthenticationData',
					],
					'verify_user' => (object) [
						'path' => '/api/account/login/verify',
						'handler' => 'verifyUser',
					],
					'authentication_success' => (object) [
						'path' => '/api/account/login/success',
						'handler' => 'handleSuccessfulAuthentication',
					],
					'authentication_failure' => (object) [
						'path' => '/api/account/login/failure',
						'handler' => 'handleFailedAuthentication',
					],
				],
				'firewall' => null,
				'csrf_token_parameter' => 'authenticate.login.token',
			],
			$this->getService('logger.main'),
		);
	}


	public function createServiceApplication__security__authorization__summary__authorization(): Application\Security\Authorization\Summary\AuthorizationSummaryExtension
	{
		return new Application\Security\Authorization\Summary\AuthorizationSummaryExtension($this->getService('authorization.manager'));
	}


	public function createServiceApplication__security__profile__profile(): Application\Security\Profile\ApplicationProfile
	{
		$service = new Application\Security\Profile\ApplicationProfile;
		$service->setRepository('page', $this->getService('application.security.profile.repository.page'));
		$service->setRepository('photo', $this->getService('application.security.profile.repository.photo'));
		return $service;
	}


	public function createServiceApplication__security__profile__repository__page(): Application\Security\Profile\Repository\PageRepository
	{
		return new Application\Security\Profile\Repository\PageRepository(
			$this->getService('authentication.user.provider.concrete.database'),
			$this->getService('model.1'),
			$this->getService('model.3'),
			$this->getService('model.2'),
			$this->getService('model.4'),
		);
	}


	public function createServiceApplication__security__profile__repository__photo(): Application\Security\Profile\Repository\PhotoRepository
	{
		return new Application\Security\Profile\Repository\PhotoRepository(
			$this->getService('authentication.user.provider.concrete.database'),
			$this->getService('model.15'),
			$this->getService('model.16'),
			$this->getService('model.17'),
		);
	}


	public function createServiceApplication__security__user__routing__event_listener(): Application\Security\User\EventListener\RoutingUserEventListener
	{
		return new Application\Security\User\EventListener\RoutingUserEventListener(
			$this,
			['authentication.user.provider.concrete.database'],
			[
				'attribute_name' => '_user_routing_event_listener.route',
				'routes' => [
					'users' => (object) [
						'path' => '/api/users',
						'handler' => 'getUsers',
					],
					'user' => (object) [
						'path' => '/api/users/{userId}',
						'handler' => 'getUser',
					],
					'avatar' => (object) [
						'path' => '/api/users/avatar/{userId}',
						'handler' => 'getAvatar',
					],
					'banner' => (object) [
						'path' => '/api/users/banner/{userId}',
						'handler' => 'getBanner',
					],
				],
				'directory' => '/Users/im.nomit/Sites/nomit.php/public/resources/dynamic/images/Application/User/',
			],
			$this->getService('logger.main'),
		);
	}


	public function createServiceApplication__security__user__routing__summary__user(): Application\Security\User\Summary\UserSummaryExtension
	{
		return new Application\Security\User\Summary\UserSummaryExtension($this, ['authentication.user.provider.concrete.database']);
	}


	public function createServiceApplication__summary__administration__summary(): Application\Summary\Administration\AdministrationSummary
	{
		$service = new Application\Summary\Administration\AdministrationSummary;
		$service->addExtension($this->getService('application.security.user.routing.summary.user'));
		$service->addExtension($this->getService('application.security.authorization.summary.authorization'));
		$service->addExtension($this->getService('application.controller.page.summary.page'));
		$service->addExtension($this->getService('application.controller.photo.summary.photo'));
		$service->addExtension($this->getService('application.summary.documentation.summary.documentation'));
		return $service;
	}


	public function createServiceApplication__summary__documentation__summary__documentation(): Application\Controller\Documentation\Summary\DocumentationSummaryExtension
	{
		return new Application\Controller\Documentation\Summary\DocumentationSummaryExtension(
			$this->getService('model.5'),
			$this->getService('model.8'),
			$this->getService('model.7'),
			$this->getService('model.6'),
			$this->getService('serializer.resolver'),
			['authentication.user.provider.concrete.database'],
			$this,
			'json',
		);
	}


	public function createServiceAuthentication__authenticator__form__main(): nomit\Security\Authentication\Authenticator\FormAuthenticator
	{
		return new nomit\Security\Authentication\Authenticator\FormAuthenticator(
			$this->getService('authentication.utilities.web.main'),
			$this->getService('authentication.user.provider.concrete.database'),
			$this->getService('authentication.handler.success.main.form'),
			$this->getService('authentication.handler.failure.main.form'),
			[
				'check_path' => '/api/account/login/authenticate',
				'login_path' => '/api/account/login',
				'use_forward' => false,
				'username_parameter' => 'username',
				'password_parameter' => 'password',
				'csrf_parameter' => 'authenticate.login.token',
				'csrf_token_id' => 'authenticate',
				'post_only' => false,
				'require_previous_session' => true,
			],
		);
	}


	public function createServiceAuthentication__authenticator__registration(): nomit\Security\Authentication\Authenticator\RegistrationAuthenticator
	{
		return new nomit\Security\Authentication\Authenticator\RegistrationAuthenticator(
			$this->getService('registration.token.storage.session'),
			$this->getService('authentication.user.provider.concrete.database'),
			$this->getService('authentication.handler.success.default'),
			$this->getService('authentication.handler.failure.default'),
			(object) [
			],
			$this->getService('event_dispatcher.dispatcher'),
		);
	}


	public function createServiceAuthentication__authenticator__remember_me__main(): nomit\Security\Authentication\Authenticator\RememberMeAuthenticator
	{
		return new nomit\Security\Authentication\Authenticator\RememberMeAuthenticator(
			$this->getService('authentication.remember_me.handler.main'),
			'gFjg3WV9sO0chayT9cRb8baq2j4MfNPJ',
			$this->getService('authentication.token.storage.session'),
			'_casper_security_authentication_remember_me',
			$this->getService('logger.logger.main.security.authentication.authenticator.remember_me.main'),
		);
	}


	public function createServiceAuthentication__authenticator__resolver__main(): nomit\Security\Authentication\Authenticator\AuthenticatorResolver
	{
		return new nomit\Security\Authentication\Authenticator\AuthenticatorResolver(
			[
				$this->getService('authentication.authenticator.form.main'),
				$this->getService('authentication.authenticator.registration'),
				$this->getService('authentication.authenticator.remember_me.main'),
			],
			$this->getService('authentication.token.storage.session'),
			$this->getService('authentication.event_dispatcher.main'),
			'main',
			$this->getService('logger.logger.main.security.authentication.firewall.main'),
			true,
			true,
			[],
		);
	}


	public function createServiceAuthentication__command__collect_login_garbage(): nomit\Security\Authentication\Command\CollectLoginGarbageAuthenticationCommand
	{
		$service = new nomit\Security\Authentication\Command\CollectLoginGarbageAuthenticationCommand($this->getService('authentication.token.persistence.filesystemmain'));
		$service->setName('auth:gc');
		$service->setName('auth:gc');
		return $service;
	}


	public function createServiceAuthentication__command__logout_user(): nomit\Security\Authentication\Command\LogoutUserAuthenticationCommand
	{
		$service = new nomit\Security\Authentication\Command\LogoutUserAuthenticationCommand(
			$this->getService('authentication.token.persistence.filesystemmain'),
			[
				$this->getService('authentication.user.provider.concrete.database'),
				'authentication.user.provider.concrete.database',
			],
		);
		$service->setName('auth:logout');
		$service->setName('auth:logout');
		return $service;
	}


	public function createServiceAuthentication__entry_point__form__main(): nomit\Security\Authentication\EntryPoint\FormEntryPoint
	{
		return new nomit\Security\Authentication\EntryPoint\FormEntryPoint(
			$this->getService('kernel'),
			$this->getService('authentication.utilities.web.main'),
			'/api/account/login',
			false,
		);
	}


	public function createServiceAuthentication__event_dispatcher__main(): nomit\EventDispatcher\LazyEventDispatcher
	{
		$service = new nomit\EventDispatcher\LazyEventDispatcher($this);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\CheckPassportAuthenticationEvent',
			'authentication.event_listener.evaluate_credentials.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\SuccessfulLoginAuthenticationEvent',
			'authentication.event_listener.update_login_record.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\SuccessfulLoginAuthenticationEvent',
			'authentication.event_listener.session_authentication_strategy.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\LogoutAuthenticationEvent',
			'authentication.event_listener.session_authentication_strategy.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\SuccessfulLoginAuthenticationEvent',
			'authentication.event_listener.token_persistence.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\AutomaticLoginAuthenticationEvent',
			'authentication.event_listener.token_persistence.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\LogoutAuthenticationEvent',
			'authentication.event_listener.token_persistence.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\SuccessfulLoginAuthenticationEvent',
			'authentication.event_listener.password_migrating.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\LogoutAuthenticationEvent',
			'authentication.event_listener.logout.redirect.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\LogoutAuthenticationEvent',
			'authentication.event_listener.logout.session.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\LogoutAuthenticationEvent',
			'authentication.event_listener.logout.cookie_clearing.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\CheckPassportAuthenticationEvent',
			'authentication.event_listener.authentication_throttling.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\SuccessfulLoginAuthenticationEvent',
			'authentication.event_listener.authentication_throttling.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\SuccessfulLoginAuthenticationEvent',
			'authentication.remember_me.event_listener.evaluate_conditions.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\SuccessfulLoginAuthenticationEvent',
			'authentication.remember_me.event_listener.remember_me.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\FailedLoginAuthenticationEvent',
			'authentication.remember_me.event_listener.remember_me.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\LogoutAuthenticationEvent',
			'authentication.remember_me.event_listener.remember_me.main',
		);
		$service->addLazySubscriber(
			'nomit\Security\Authentication\Event\DeAuthenticatedTokenAuthenticationEvent',
			'authentication.remember_me.event_listener.remember_me.main',
		);
		$service->addLazySubscriber('kernel.response', 'authentication.remember_me.event_listener.response');
		return $service;
	}


	public function createServiceAuthentication__event_listener__account_information(): nomit\Security\Authentication\EventListener\AccountInformationEventListener
	{
		return new nomit\Security\Authentication\EventListener\AccountInformationEventListener(
			$this->getService('authentication.token.storage.session'),
			$this->getService('session.session.main'),
			(object) [
				'path' => '/api/account/user/',
				'session_key' => '_authentication.account_information.is_logged_in',
			],
			$this->getService('logger.logger.main.security.authentication'),
		);
	}


	public function createServiceAuthentication__event_listener__authentication_throttling__main(): nomit\Security\Authentication\EventListener\AuthenticationThrottlingEventListener
	{
		return new nomit\Security\Authentication\EventListener\AuthenticationThrottlingEventListener(
			$this->getService('web.requests'),
			$this->getService('authentication.rate_limiter.authentication_throttling.main.limiter'),
			$this->getService('logger.logger.main.security.authentication.rate_limiter'),
		);
	}


	public function createServiceAuthentication__event_listener__evaluate_credentials__main(): nomit\Security\Authentication\EventListener\EvaluateCredentialsEventListener
	{
		return new nomit\Security\Authentication\EventListener\EvaluateCredentialsEventListener(
			$this->getService('cryptography.password.factory'),
			$this->getService('authentication.user.provider.concrete.database'),
		);
	}


	public function createServiceAuthentication__event_listener__logout__cookie_clearing__main(): nomit\Security\Authentication\EventListener\Logout\CookieClearingLogoutEventListener
	{
		return new nomit\Security\Authentication\EventListener\Logout\CookieClearingLogoutEventListener([]);
	}


	public function createServiceAuthentication__event_listener__logout__redirect__main(): nomit\Security\Authentication\EventListener\Logout\RedirectingLogoutEventListener
	{
		return new nomit\Security\Authentication\EventListener\Logout\RedirectingLogoutEventListener(
			$this->getService('authentication.utilities.web.main'),
			'/api/account/user/?initial=1',
			$this->getService('logger.logger.main.security.authentication.firewall.logout.main'),
		);
	}


	public function createServiceAuthentication__event_listener__logout__session__main(): nomit\Security\Authentication\EventListener\Logout\SessionClearingLogoutEventListener
	{
		return new nomit\Security\Authentication\EventListener\Logout\SessionClearingLogoutEventListener;
	}


	public function createServiceAuthentication__event_listener__password_migrating__main(): nomit\Security\Authentication\EventListener\PasswordMigratingEventListener
	{
		return new nomit\Security\Authentication\EventListener\PasswordMigratingEventListener(
			$this->getService('cryptography.password.factory'),
			$this->getService('logger.logger.main.security.authentication.firewall.main'),
		);
	}


	public function createServiceAuthentication__event_listener__session_authentication_strategy__main(): nomit\Security\Authentication\EventListener\SessionAuthenticationStrategyEventListener
	{
		return new nomit\Security\Authentication\EventListener\SessionAuthenticationStrategyEventListener($this->getService('authentication.session.strategy.default.main'));
	}


	public function createServiceAuthentication__event_listener__token_persistence__main(): nomit\Security\Authentication\EventListener\TokenPersistenceEventListener
	{
		return new nomit\Security\Authentication\EventListener\TokenPersistenceEventListener(
			$this->getService('authentication.token.persistence.filesystemmain'),
			$this->getService('logger.logger.main.security.authentication.firewall.main'),
		);
	}


	public function createServiceAuthentication__event_listener__update_login_record__main(): nomit\Security\Authentication\EventListener\UpdateLoginRecordAuthenticationEventListener
	{
		return new nomit\Security\Authentication\EventListener\UpdateLoginRecordAuthenticationEventListener(
			$this->getService('authentication.user.provider.concrete.database'),
			$this->getService('logger.logger.main.security.authentication.firewall.main'),
		);
	}


	public function createServiceAuthentication__event_listener__user_provider__main(): nomit\Security\Authentication\EventListener\UserProviderEventListener
	{
		return new nomit\Security\Authentication\EventListener\UserProviderEventListener($this->getService('authentication.user.provider.concrete.database'));
	}


	public function createServiceAuthentication__event_listener__user_validator__main(): nomit\Security\Authentication\EventListener\UserValidatorEventListener
	{
		return new nomit\Security\Authentication\EventListener\UserValidatorEventListener($this->getService('authentication.user.validator.main'));
	}


	public function createServiceAuthentication__firewall__listener__authenticator_resolver(): nomit\Security\Authentication\Firewall\Listener\AuthenticatorResolverFirewallListener
	{
		return new nomit\Security\Authentication\Firewall\Listener\AuthenticatorResolverFirewallListener($this->getService('authentication.authenticator.resolver.main'));
	}


	public function createServiceAuthentication__firewall__listener__authorization__main(): nomit\Security\Session\Firewall\Listener\AuthorizationFirewallListener
	{
		return new nomit\Security\Session\Firewall\Listener\AuthorizationFirewallListener(
			$this->getService('authentication.token.storage.session'),
			$this->getService('authorization.decision_manager'),
			$this->getService('authorization.access_map'),
			false,
		);
	}


	public function createServiceAuthentication__firewall__listener__channel__main(): nomit\Security\Authentication\Firewall\Listener\ChannelFirewallListener
	{
		return new nomit\Security\Authentication\Firewall\Listener\ChannelFirewallListener(
			$this->getService('authorization.access_map'),
			$this->getService('logger.logger.main.security.authentication.firewall.main'),
			80,
			443,
		);
	}


	public function createServiceAuthentication__firewall__listener__exception__main(): nomit\Security\Authentication\Firewall\Listener\ExceptionFirewallListener
	{
		return new nomit\Security\Authentication\Firewall\Listener\ExceptionFirewallListener(
			$this->getService('authentication.token.storage.session'),
			$this->getService('authentication.trust.resolver'),
			$this->getService('authentication.utilities.web.main'),
			'main',
			$this->getService('authentication.entry_point.form.main'),
			null,
			$this->getService('logger.logger.main.security.authentication.firewall.main'),
			false,
		);
	}


	public function createServiceAuthentication__firewall__listener__logout__main(): nomit\Security\Authentication\Firewall\Listener\LogoutFirewallListener
	{
		return new nomit\Security\Authentication\Firewall\Listener\LogoutFirewallListener(
			$this->getService('authentication.utilities.web.main'),
			$this->getService('authentication.token.storage.session'),
			$this->getService('authentication.event_dispatcher.main'),
			tokenManager: $this->getService('csrf.manager'),
			logger: $this->getService('logger.logger.main.security.authentication.firewall.logout.main'),
		);
	}


	public function createServiceAuthentication__firewall__listener__registration__main(): nomit\Security\Authentication\Firewall\Listener\RegistrationFirewallListener
	{
		return new nomit\Security\Authentication\Firewall\Listener\RegistrationFirewallListener(
			$this->getService('authentication.utilities.web.main'),
			$this->getService('event_dispatcher.dispatcher'),
			['path' => '/api/account/register'],
			$this->getService('csrf.manager'),
		);
	}


	public function createServiceAuthentication__firewall__listener__session__main(): nomit\Security\Authentication\Firewall\Listener\SessionFirewallListener
	{
		return new nomit\Security\Authentication\Firewall\Listener\SessionFirewallListener(
			$this->getService('authentication.token.storage.session'),
			[
				$this->getService('authentication.user.provider.concrete.database'),
				$this->getService('authentication.user.provider.concrete.database'),
			],
			'main',
			$this->getService('logger.logger.main.security.authentication.firewall.main'),
			$this->getService('authentication.event_dispatcher.main'),
			$this->getService('authentication.trust.resolver'),
		);
	}


	public function createServiceAuthentication__firewall__main(): nomit\Security\Authentication\Firewall\Firewall
	{
		return new nomit\Security\Authentication\Firewall\Firewall(
			$this->getService('authentication.firewall.map.main'),
			$this->getService('authentication.event_dispatcher.main'),
		);
	}


	public function createServiceAuthentication__firewall__map__main(): nomit\Security\Authentication\Firewall\FirewallMap
	{
		$service = new nomit\Security\Authentication\Firewall\FirewallMap;
		$service->add(
			$this->getService('authentication.request_matcher.uXMEbhd'),
			[
				$this->getService('authentication.firewall.listener.channel.main'),
				$this->getService('authentication.firewall.listener.session.main'),
				$this->getService('authentication.firewall.listener.registration.main'),
				$this->getService('authentication.firewall.listener.authenticator_resolver'),
				$this->getService('authentication.firewall.listener.authorization.main'),
			],
			$this->getService('authentication.firewall.listener.exception.main'),
			$this->getService('authentication.firewall.listener.logout.main'),
		);
		return $service;
	}


	public function createServiceAuthentication__handler__failure__default(): nomit\Security\Authentication\Handler\DefaultFailedAuthenticationHandler
	{
		return new nomit\Security\Authentication\Handler\DefaultFailedAuthenticationHandler(
			$this->getService('kernel'),
			$this->getService('authentication.utilities.web.main'),
			[
				'check_path' => '/api/account/login/authenticate',
				'login_path' => '/api/account/login',
				'use_forward' => false,
				'always_use_default_target_path' => true,
				'default_target_path' => '/api/account/user/?initial=1',
				'target_path_parameter' => '~',
				'use_referrer' => true,
				'failure_handler' => 'default',
				'success_handler' => 'default',
				'failure_path' => '/api/account/login/failure',
				'failure_forward' => false,
				'failure_path_parameter' => '_failure_path',
				'email_parameter' => 'email',
				'username_parameter' => 'username',
				'password_parameter' => 'password',
				'csrf_parameter' => 'authenticate.login.token',
				'csrf_token_id' => 'authenticate',
				'csrf_token_generator' => 'csrf.token.generator.uri_safe',
				'post_only' => false,
				'remember_me' => true,
				'require_previous_session' => true,
			],
			$this->getService('logger.logger.main.security.authentication.handler.failure'),
		);
	}


	public function createServiceAuthentication__handler__failure__main__form(): nomit\Security\Authentication\Handler\CustomFailedAuthenticationHandler
	{
		return new nomit\Security\Authentication\Handler\CustomFailedAuthenticationHandler(
			$this->getService('authentication.handler.failure.default'),
			[
				'login_path' => '/api/account/login',
				'failure_path' => '/api/account/login/failure',
				'failure_forward' => false,
				'failure_path_parameter' => '_failure_path',
			],
		);
	}


	public function createServiceAuthentication__handler__success__default(): nomit\Security\Authentication\Handler\DefaultSuccessfulAuthenticationHandler
	{
		$service = new nomit\Security\Authentication\Handler\DefaultSuccessfulAuthenticationHandler(
			$this->getService('authentication.utilities.web.main'),
			[
				'login_path' => '/api/account/login',
				'always_use_default_target_path' => true,
				'default_target_path' => '/api/account/user/?initial=1',
				'target_path_parameter' => '~',
			],
		);
		$service->setFirewallName('main');
		return $service;
	}


	public function createServiceAuthentication__handler__success__main__form(): nomit\Security\Authentication\Handler\CustomSuccessfulAuthenticationHandler
	{
		return new nomit\Security\Authentication\Handler\CustomSuccessfulAuthenticationHandler(
			$this->getService('authentication.handler.success.default'),
			[
				'login_path' => '/api/account/login',
				'always_use_default_target_path' => true,
				'default_target_path' => '/api/account/user/?initial=1',
				'target_path_parameter' => '~',
			],
			'main',
		);
	}


	public function createServiceAuthentication__rate_limiter__authentication_throttling__main__limiter(): nomit\Security\Authentication\RateLimiter\AuthenticationRateLimiter
	{
		return new nomit\Security\Authentication\RateLimiter\AuthenticationRateLimiter(
			$this->getService('rate_limiter.factory._login_local.main'),
			$this->getService('rate_limiter.factory._login_globalmain'),
		);
	}


	public function createServiceAuthentication__remember_me__event_listener__evaluate_conditions__main(): nomit\Security\Authentication\EventListener\EvaluateRememberMeConditionsEventListener
	{
		return new nomit\Security\Authentication\EventListener\EvaluateRememberMeConditionsEventListener(
			[
				'name' => '_casper_security_authentication_remember_me',
				'lifetime' => 31536000,
				'path' => '/',
				'domain' => null,
				'secure' => false,
				'httponly' => true,
				'samesite' => null,
				'always_remember_me' => false,
				'remember_me_parameter' => 'remember_me',
				'signature_properties' => ['password', 'last_login_datetime', 'last_login_ip_address'],
				'secret' => 'gFjg3WV9sO0chayT9cRb8baq2j4MfNPJ',
				'token_storage' => ['service' => 'memory', 'memory' => 'test'],
				'token_persistence' => [
					'service' => 'cache',
					'cache' => ['outdated_token_ttl' => 3600, 'key_prefix' => '_remember_me-stale-'],
				],
				'service' => null,
			],
			$this->getService('logger.logger.main.security.authentication.authenticator.remember_me.main'),
		);
	}


	public function createServiceAuthentication__remember_me__event_listener__remember_me__main(): nomit\Security\Authentication\EventListener\RememberMeEventListener
	{
		return new nomit\Security\Authentication\EventListener\RememberMeEventListener(
			$this->getService('authentication.remember_me.handler.main'),
			$this->getService('logger.logger.main.security.authentication.authenticator.remember_me.main'),
		);
	}


	public function createServiceAuthentication__remember_me__event_listener__response(): nomit\Security\Authentication\RememberMe\EventListener\RememberMeResponseEventListener
	{
		return new nomit\Security\Authentication\RememberMe\EventListener\RememberMeResponseEventListener($this->getService('logger.logger.main.security.authentication.authenticator.remember_me'));
	}


	public function createServiceAuthentication__remember_me__handler__main(): nomit\Security\Authentication\RememberMe\Handler\PersistentRememberMeHandler
	{
		return new nomit\Security\Authentication\RememberMe\Handler\PersistentRememberMeHandler(
			$this->getService('authentication.remember_me.token.storage.main'),
			'gFjg3WV9sO0chayT9cRb8baq2j4MfNPJ',
			$this->getService('cryptography.entropy_factory'),
			$this->getService('authentication.user.provider.concrete.database'),
			$this->getService('web.requests'),
			[
				'name' => '_casper_security_authentication_remember_me',
				'lifetime' => 31536000,
				'path' => '/',
				'domain' => null,
				'secure' => false,
				'httponly' => true,
				'samesite' => null,
				'always_remember_me' => false,
				'remember_me_parameter' => 'remember_me',
				'signature_properties' => ['password', 'last_login_datetime', 'last_login_ip_address'],
				'secret' => 'gFjg3WV9sO0chayT9cRb8baq2j4MfNPJ',
				'token_storage' => ['service' => 'memory', 'memory' => 'test'],
				'token_persistence' => [
					'service' => 'cache',
					'cache' => ['outdated_token_ttl' => 3600, 'key_prefix' => '_remember_me-stale-'],
				],
				'service' => null,
			],
			$this->getService('logger.logger.main.security.authentication.remember_me'),
			$this->getService('authentication.remember_me.token.persistence.main'),
		);
	}


	public function createServiceAuthentication__remember_me__token__persistence__main(): nomit\Security\Authentication\RememberMe\Token\Persistence\CacheRememberMeTokenPersistence
	{
		return new nomit\Security\Authentication\RememberMe\Token\Persistence\CacheRememberMeTokenPersistence(
			$this->getService('cache.cache.application.application'),
			3600,
			'_remember_me-stale-',
		);
	}


	public function createServiceAuthentication__remember_me__token__storage__main(): nomit\Security\Authentication\RememberMe\Token\Storage\MemoryRememberMeTokenStorage
	{
		return new nomit\Security\Authentication\RememberMe\Token\Storage\MemoryRememberMeTokenStorage;
	}


	public function createServiceAuthentication__request_matcher__uXMEbhd(): nomit\Web\Request\RequestMatcher
	{
		return new nomit\Web\Request\RequestMatcher('^.+/', null, ['GET', 'POST']);
	}


	public function createServiceAuthentication__session__strategy__default__main(): nomit\Security\Authentication\Session\DefaultSessionAuthenticationStrategy
	{
		return new nomit\Security\Authentication\Session\DefaultSessionAuthenticationStrategy('migrate');
	}


	public function createServiceAuthentication__token__persistence__filesystemmain(): nomit\Security\Authentication\Token\Persistence\FileSystemTokenPersistence
	{
		return new nomit\Security\Authentication\Token\Persistence\FileSystemTokenPersistence(
			$this->getService('filesystem.filesystem'),
			$this->getService('serializer.resolver'),
			$this->getService('cryptography.encrypter'),
			$this->getService('cryptography.hasher.sha256'),
			$this->getService('lock.factory'),
			'/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.security/login/',
			'json',
			86400,
			'_security.authentication.token.persistence',
			$this->getService('logger.logger.main.security.authentication.firewall.main'),
		);
	}


	public function createServiceAuthentication__token__storage__session(): nomit\Security\Authentication\Token\Storage\SessionTokenStorage
	{
		return new nomit\Security\Authentication\Token\Storage\SessionTokenStorage(
			$this->getService('session'),
			'_security.authentication.token.session',
		);
	}


	public function createServiceAuthentication__trust__resolver(): nomit\Security\Authentication\Trust\TrustResolver
	{
		return new nomit\Security\Authentication\Trust\TrustResolver;
	}


	public function createServiceAuthentication__user__provider__concrete__database(): nomit\Security\Authentication\User\DatabaseUserProvider
	{
		return new nomit\Security\Authentication\User\DatabaseUserProvider(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('event_dispatcher.dispatcher'),
			$this->getService('logger.logger.main.security.authentication.user.provider'),
			'users',
			'user_bans',
		);
	}


	public function createServiceAuthentication__user__validator__main(): nomit\Security\Authentication\User\UserValidator
	{
		return new nomit\Security\Authentication\User\UserValidator([]);
	}


	public function createServiceAuthentication__utilities__authentication__main(): nomit\Security\Authentication\Utilities\AuthenticationUtilities
	{
		return new nomit\Security\Authentication\Utilities\AuthenticationUtilities($this->getService('web.requests'));
	}


	public function createServiceAuthentication__utilities__web__main(): nomit\Security\Authentication\Utilities\WebUtilities
	{
		return new nomit\Security\Authentication\Utilities\WebUtilities(
			$this->getService('routing.router'),
			$this->getService('authentication.request_matcher.uXMEbhd'),
			null,
			null,
		);
	}


	public function createServiceAuthorization__access_map(): nomit\Security\Authorization\AccessMap
	{
		$service = new nomit\Security\Authorization\AccessMap;
		$service->add($this->getService('authorization.request_matcher.HJam.mY'), ['ADMINISTRATOR'], false);
		$service->add($this->getService('authorization.request_matcher.zle3ai8'), ['IS_AUTHENTICATED_FULLY'], false);
		$service->add($this->getService('authorization.request_matcher.VCweyO8'), ['IS_AUTHENTICATED_FULLY'], false);
		$service->add($this->getService('authorization.request_matcher.hzV4IU4'), ['IS_AUTHENTICATED_FULLY'], false);
		$service->add($this->getService('authorization.request_matcher.oOko5Z9'), ['IS_AUTHENTICATED_ANONYMOUSLY'], false);
		$service->add($this->getService('authorization.request_matcher.OaNeQQr'), ['IS_AUTHENTICATED_FULLY'], false);
		return $service;
	}


	public function createServiceAuthorization__checker(): nomit\Security\Authorization\AuthorizationChecker
	{
		return new nomit\Security\Authorization\AuthorizationChecker(
			$this->getService('authentication.token.storage.session'),
			$this->getService('authorization.decision_manager'),
			false,
		);
	}


	public function createServiceAuthorization__command__create_permission(): nomit\Security\Authorization\Command\CreatePermissionCommand
	{
		$service = new nomit\Security\Authorization\Command\CreatePermissionCommand($this->getService('authorization.manager'));
		$service->setName('authorization:create_permission');
		$service->setName('authorization:create_permission');
		return $service;
	}


	public function createServiceAuthorization__command__create_role(): nomit\Security\Authorization\Command\CreateRoleCommand
	{
		$service = new nomit\Security\Authorization\Command\CreateRoleCommand($this->getService('authorization.manager'));
		$service->setName('authorization:create_role');
		$service->setName('authorization:create_role');
		return $service;
	}


	public function createServiceAuthorization__decision_manager(): nomit\Security\Authorization\AccessDecisionManager
	{
		return new nomit\Security\Authorization\AccessDecisionManager([
			$this->getService('authorization.voter.simple'),
			$this->getService('authorization.voter.authenticated'),
			$this->getService('authorization.voter.role_hierarchy'),
		]);
	}


	public function createServiceAuthorization__event_listener__user_role_provider(): nomit\Security\Authorization\EventListener\UserRoleProviderEventListener
	{
		return new nomit\Security\Authorization\EventListener\UserRoleProviderEventListener($this->getService('authorization.user.provider'));
	}


	public function createServiceAuthorization__manager(): nomit\Security\Authorization\AuthorizationManager
	{
		return new nomit\Security\Authorization\AuthorizationManager(
			$this->getService('authorization.user.provider.database'),
			$this->getService('logger.logger.main.security.authorization'),
		);
	}


	public function createServiceAuthorization__request_matcher__HJam__mY(): nomit\Web\Request\RequestMatcher
	{
		return new nomit\Web\Request\RequestMatcher('^/api/administration', null, ['GET', 'POST']);
	}


	public function createServiceAuthorization__request_matcher__OaNeQQr(): nomit\Web\Request\RequestMatcher
	{
		return new nomit\Web\Request\RequestMatcher('/api/account/logout', null, ['GET']);
	}


	public function createServiceAuthorization__request_matcher__VCweyO8(): nomit\Web\Request\RequestMatcher
	{
		return new nomit\Web\Request\RequestMatcher('^/api/photos', null, ['GET', 'POST']);
	}


	public function createServiceAuthorization__request_matcher__hzV4IU4(): nomit\Web\Request\RequestMatcher
	{
		return new nomit\Web\Request\RequestMatcher('^/api/notifications', null, ['GET']);
	}


	public function createServiceAuthorization__request_matcher__oOko5Z9(): nomit\Web\Request\RequestMatcher
	{
		return new nomit\Web\Request\RequestMatcher('^/api/account/login', null, ['POST']);
	}


	public function createServiceAuthorization__request_matcher__zle3ai8(): nomit\Web\Request\RequestMatcher
	{
		return new nomit\Web\Request\RequestMatcher('^/api/pages', null, ['GET', 'POST']);
	}


	public function createServiceAuthorization__role__hierarchy(): nomit\Security\Authorization\Role\RoleHierarchy
	{
		return new nomit\Security\Authorization\Role\RoleHierarchy([
			'ADMINISTRATOR' => ['MODERATOR', 'VERIFIED_USER'],
			'MODERATOR' => ['VERIFIED_USER'],
		]);
	}


	public function createServiceAuthorization__user__provider__database(): nomit\Security\Authorization\User\DatabaseUserAuthorizationProvider
	{
		return new nomit\Security\Authorization\User\DatabaseUserAuthorizationProvider(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			'auth_roles',
			'auth_user_roles',
			'auth_permissions',
			'auth_role_permissions',
			$this->getService('logger.logger.main.security.authorization'),
		);
	}


	public function createServiceAuthorization__voter__authenticated(): nomit\Security\Authorization\Voter\AuthenticatedVoter
	{
		return new nomit\Security\Authorization\Voter\AuthenticatedVoter($this->getService('authentication.trust.resolver'));
	}


	public function createServiceAuthorization__voter__role_hierarchy(): nomit\Security\Authorization\Voter\RoleHierarchyVoter
	{
		return new nomit\Security\Authorization\Voter\RoleHierarchyVoter($this->getService('authorization.role.hierarchy'));
	}


	public function createServiceAuthorization__voter__simple(): nomit\Security\Authorization\Voter\RoleVoter
	{
		return new nomit\Security\Authorization\Voter\RoleVoter;
	}


	public function createServiceCache__cache__application(): nomit\Cache\Cache
	{
		return new nomit\Cache\Cache($this->getService('cache.storage.file.application'));
	}


	public function createServiceCache__cache__application__application(): nomit\Cache\Cache
	{
		return new nomit\Cache\Cache($this->getService('cache.storage.file.application.application'));
	}


	public function createServiceCache__cache__application__application__controller(): nomit\Cache\Cache
	{
		return new nomit\Cache\Cache($this->getService('cache.storage.file.application.application.controller'));
	}


	public function createServiceCache__cache__application__application__model(): nomit\Cache\Cache
	{
		return new nomit\Cache\Cache($this->getService('cache.storage.file.application.application.model'));
	}


	public function createServiceCache__cache__application__database(): nomit\Cache\Cache
	{
		return new nomit\Cache\Cache($this->getService('cache.storage.file.application.database'));
	}


	public function createServiceCache__cache__database(): nomit\Cache\Cache
	{
		return new nomit\Cache\Cache($this->getService('cache.storage.file.database'));
	}


	public function createServiceCache__journal__application(): nomit\Cache\Storages\JournalInterface
	{
		return new nomit\Cache\Storages\SQLiteJournal('/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/application/journal//journal.s3db');
	}


	public function createServiceCache__journal__application__application(): nomit\Cache\Storages\JournalInterface
	{
		return new nomit\Cache\Storages\SQLiteJournal('/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/application/journal//journal.s3db');
	}


	public function createServiceCache__journal__application__application__controller(): nomit\Cache\Storages\JournalInterface
	{
		return new nomit\Cache\Storages\SQLiteJournal('/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/application/journal//journal.s3db');
	}


	public function createServiceCache__journal__application__application__model(): nomit\Cache\Storages\JournalInterface
	{
		return new nomit\Cache\Storages\SQLiteJournal('/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/application/journal//journal.s3db');
	}


	public function createServiceCache__journal__application__database(): nomit\Cache\Storages\JournalInterface
	{
		return new nomit\Cache\Storages\SQLiteJournal('/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/application/journal//journal.s3db');
	}


	public function createServiceCache__journal__database(): nomit\Cache\Storages\JournalInterface
	{
		return new nomit\Cache\Storages\SQLiteJournal('/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/database/journal//journal.s3db');
	}


	public function createServiceCache__storage__file__application(): nomit\Cache\Storages\FileStorage
	{
		return new nomit\Cache\Storages\FileStorage(
			'/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/application/',
			$this->getService('cache.journal.application'),
		);
	}


	public function createServiceCache__storage__file__application__application(): nomit\Cache\Storages\FileStorage
	{
		return new nomit\Cache\Storages\FileStorage(
			'/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/application/',
			$this->getService('cache.journal.application.application'),
		);
	}


	public function createServiceCache__storage__file__application__application__controller(): nomit\Cache\Storages\FileStorage
	{
		return new nomit\Cache\Storages\FileStorage(
			'/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/application/',
			$this->getService('cache.journal.application.application.controller'),
		);
	}


	public function createServiceCache__storage__file__application__application__model(): nomit\Cache\Storages\FileStorage
	{
		return new nomit\Cache\Storages\FileStorage(
			'/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/application/',
			$this->getService('cache.journal.application.application.model'),
		);
	}


	public function createServiceCache__storage__file__application__database(): nomit\Cache\Storages\FileStorage
	{
		return new nomit\Cache\Storages\FileStorage(
			'/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/application/',
			$this->getService('cache.journal.application.database'),
		);
	}


	public function createServiceCache__storage__file__database(): nomit\Cache\Storages\FileStorage
	{
		return new nomit\Cache\Storages\FileStorage(
			'/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.cache/database/',
			$this->getService('cache.journal.database'),
		);
	}


	public function createServiceClient__client(): nomit\Client\ClientInterface
	{
		$service = nomit\Client\Client::create(
			[
				'maximum_redirects' => 7,
				'base_uri' => null,
				'auth_basic' => null,
				'auth_bearer' => null,
				'auth_ntlm' => null,
				'query' => [],
				'headers' => [],
				'http_version' => null,
				'resolve' => [],
				'proxy' => null,
				'no_proxy' => null,
				'timeout' => null,
				'bind_to' => null,
				'verify_peer' => null,
				'verify_host' => null,
				'cafile' => null,
				'capath' => null,
				'local_cert' => null,
				'local_pk' => null,
				'passphrase' => null,
				'ciphers' => null,
				'peer_fingerprint' => ['sha1' => null, 'pin-sha256' => null, 'md5' => null],
			],
			6,
		);
		$service->setLogger($this->getService('logger'));
		return $service;
	}


	public function createServiceCommand__collect_garbage(): nomit\Security\Session\Command\CollectGarbageSessionCommand
	{
		$service = new nomit\Security\Session\Command\CollectGarbageSessionCommand(
			$this->getService('session.token.persistence.main'),
			345600,
		);
		$service->setName('session:gc');
		$service->setName('session:gc');
		return $service;
	}


	public function createServiceConfig(): nomit\Messenger\Configuration\Configuration
	{
		return new nomit\Messenger\Configuration\Configuration([
			'delay' => 3600,
			'consumer' => (object) [
				'max_runtime' => 9223372036854775807,
				'max_attempts' => 10,
				'stop_when_empty' => true,
				'catch_exceptions' => false,
			],
			'queues' => [
				'system' => (object) [
					'driver' => 'filesystem',
					'drivers' => (object) [
						'filesystem' => (object) [
							'directory' => '/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.messenger/',
							'permissions' => 740,
						],
					],
				],
			],
			'serialization' => (object) [
				'format' => 'json',
				'context' => [],
			],
		]);
	}


	public function createServiceConfirmation__event_listener__confirmation(): nomit\Security\Registration\Confirmation\EventListener\ConfirmationEventListener
	{
		return new nomit\Security\Registration\Confirmation\EventListener\ConfirmationEventListener(
			$this->getService('confirmation.manager'),
			$this->getService('registration.user.provider.database'),
			$this->getService('confirmation.handler.success.default'),
			$this->getService('confirmation.handler.failure.default'),
			$this->getService('confirmation.mail.mailer'),
			'im.nomit@me.com',
			'token_confirm',
			'_security.registration.confirmation.token',
			$this->getService('event_dispatcher.dispatcher'),
			$this->getService('logger.main'),
		);
	}


	public function createServiceConfirmation__handler__failure__default(): nomit\Security\Registration\Confirmation\Handler\DefaultFailedConfirmationHandler
	{
		return new nomit\Security\Registration\Confirmation\Handler\DefaultFailedConfirmationHandler;
	}


	public function createServiceConfirmation__handler__success__default(): nomit\Security\Registration\Confirmation\Handler\DefaultSuccessfulConfirmationHandler
	{
		return new nomit\Security\Registration\Confirmation\Handler\DefaultSuccessfulConfirmationHandler;
	}


	public function createServiceConfirmation__mail__mailer(): nomit\Security\Registration\Confirmation\Mail\Mailer
	{
		return new nomit\Security\Registration\Confirmation\Mail\Mailer(
			$this->getService('mail.mailer'),
			'/Users/im.nomit/Sites/nomit.php/public/resources/',
		);
	}


	public function createServiceConfirmation__manager(): nomit\Security\Registration\Confirmation\ConfirmationManager
	{
		return new nomit\Security\Registration\Confirmation\ConfirmationManager(
			$this->getService('confirmation.token.persistence.database'),
			$this->getService('cryptography.encrypter'),
			86400,
			$this->getService('logger.main'),
		);
	}


	public function createServiceConfirmation__token__persistence__database(): nomit\Security\Registration\Confirmation\Token\Persistence\DatabaseTokenPersistence
	{
		return new nomit\Security\Registration\Confirmation\Token\Persistence\DatabaseTokenPersistence(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			'user_verifications',
			$this->getService('logger.main'),
		);
	}


	public function createServiceConsole__command__complete(): nomit\Console\Command\CompleteCommand
	{
		$service = new nomit\Console\Command\CompleteCommand;
		$service->setName('complete');
		$service->setName('complete');
		return $service;
	}


	public function createServiceConsole__command__dump_completion(): nomit\Console\Command\DumpCompletionCommand
	{
		$service = new nomit\Console\Command\DumpCompletionCommand;
		$service->setName('dump');
		$service->setName('dump');
		return $service;
	}


	public function createServiceConsole__command__help(): nomit\Console\Command\HelpCommand
	{
		$service = new nomit\Console\Command\HelpCommand;
		$service->setName('help');
		$service->setName('help');
		return $service;
	}


	public function createServiceConsole__command__list(): nomit\Console\Command\ListCommand
	{
		$service = new nomit\Console\Command\ListCommand;
		$service->setName('list');
		$service->setName('list');
		return $service;
	}


	public function createServiceConsole__command__queue__dequeue(): nomit\Console\Command\DequeueCommand
	{
		$service = new nomit\Console\Command\DequeueCommand([]);
		$service->setName('queue:dequeue');
		$service->setName('queue:dequeue');
		return $service;
	}


	public function createServiceConsole__command__queue__enqueue(): nomit\Console\Command\EnqueueCommand
	{
		$service = new nomit\Console\Command\EnqueueCommand([]);
		$service->setName('queue:enqueue');
		$service->setName('queue:enqueue');
		return $service;
	}


	public function createServiceConsole__command__repository(): nomit\Console\Command\CommandRepository
	{
		return new nomit\Console\Command\CommandRepository($this);
	}


	public function createServiceConsole__command__welcome(): nomit\Console\Command\WelcomeCommand
	{
		$service = new nomit\Console\Command\WelcomeCommand;
		$service->setName('welcome');
		$service->setName('welcome');
		return $service;
	}


	public function createServiceConsole__input__factory(): nomit\Console\Input\InputFactory
	{
		return new nomit\Console\Input\InputFactory($this->getService('console.input.reader.stream'));
	}


	public function createServiceConsole__input__reader__stream(): nomit\Console\Input\Reader\StreamReader
	{
		return new nomit\Console\Input\Reader\StreamReader;
	}


	public function createServiceConsole__kernel(): nomit\Console\Kernel
	{
		$service = new nomit\Console\Kernel($this);
		$service->setName('nomit');
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->add($this->getService('kernel.command.about'));
		$service->add($this->getService('kernel.command.secret.decrypt_to_local'));
		$service->add($this->getService('kernel.command.secret.encrypt_secrets_from_local'));
		$service->add($this->getService('kernel.command.secret.list'));
		$service->add($this->getService('kernel.command.secret.remove'));
		$service->add($this->getService('kernel.command.secret.set'));
		$service->add($this->getService('authentication.command.collect_login_garbage'));
		$service->add($this->getService('authentication.command.logout_user'));
		$service->add($this->getService('authorization.command.create_role'));
		$service->add($this->getService('authorization.command.create_permission'));
		$service->add($this->getService('logger.command.collect_garbage'));
		$service->add($this->getService('command.collect_garbage'));
		$service->add($this->getService('resource.command.clear_cache'));
		$service->add($this->getService('notification.command.push_to_everyone'));
		$service->add($this->getService('notification.command.get_user'));
		$service->add($this->getService('notification.command.clear_user'));
		$service->add($this->getService('messenger.command.consume'));
		$service->add($this->getService('console.command.complete'));
		$service->add($this->getService('console.command.dump_completion'));
		$service->add($this->getService('console.command.help'));
		$service->add($this->getService('console.command.list'));
		$service->add($this->getService('console.command.welcome'));
		$service->add($this->getService('console.command.queue.enqueue'));
		$service->add($this->getService('console.command.queue.dequeue'));
		return $service;
	}


	public function createServiceConsole__output__factory(): nomit\Console\Output\OutputFactory
	{
		return new nomit\Console\Output\OutputFactory([
			'console.output.writer.file' => $this->getService('console.output.writer.file'),
			'console.output.writer.stdout' => $this->getService('console.output.writer.stdout'),
			'console.output.writer.buffer' => $this->getService('console.output.writer.buffer'),
		]);
	}


	public function createServiceConsole__output__writer__buffer(): nomit\Console\Output\Writer\BufferWriter
	{
		return new nomit\Console\Output\Writer\BufferWriter;
	}


	public function createServiceConsole__output__writer__file(): nomit\Console\Output\Writer\FileWriter
	{
		return new nomit\Console\Output\Writer\FileWriter('/Users/im.nomit/Sites/nomit.php/tmp//nomit.console/output.stream');
	}


	public function createServiceConsole__output__writer__stderr(): nomit\Console\Output\Writer\StdErrWriter
	{
		return new nomit\Console\Output\Writer\StdErrWriter;
	}


	public function createServiceConsole__output__writer__stdout(): nomit\Console\Output\Writer\StdOutWriter
	{
		return new nomit\Console\Output\Writer\StdOutWriter;
	}


	public function createServiceContainer(): Container_ad1a675328
	{
		return $this;
	}


	public function createServiceController__1(): Application\Controller\Page\DiscussionController
	{
		$service = new Application\Controller\Page\DiscussionController(
			$this->getService('serializer.resolver'),
			$this->getService('model.1'),
			$this->getService('model.3'),
			$this->getService('model.2'),
			$this->getService('model.4'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__10(): Application\Controller\Administration\SummaryController
	{
		$service = new Application\Controller\Administration\SummaryController($this->getService('application.summary.administration.summary'));
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__11(): Application\Controller\Administration\TagController
	{
		$service = new Application\Controller\Administration\TagController(
			$this->getService('model.5'),
			$this->getService('model.8'),
			$this->getService('model.7'),
			$this->getService('model.6'),
			$this->getService('serializer.resolver'),
			$this->getService('logger.logger.main.application.controller'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__12(): Application\Controller\Administration\DocumentationController
	{
		$service = new Application\Controller\Administration\DocumentationController(
			$this->getService('model.5'),
			$this->getService('model.8'),
			$this->getService('model.7'),
			$this->getService('model.6'),
			$this->getService('serializer.resolver'),
			logger: $this->getService('logger.logger.main.application.controller'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__13(): Application\Controller\Administration\AuthorizationController
	{
		$service = new Application\Controller\Administration\AuthorizationController($this->getService('authorization.manager'));
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__14(): Application\Controller\Article\ArticleController
	{
		$service = new Application\Controller\Article\ArticleController(
			$this->getService('model.13'),
			$this->getService('model.14'),
			$this->getService('model.12'),
			logger: $this->getService('logger.logger.main.application.controller'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__15(): Application\Controller\Photo\PhotoController
	{
		$service = new Application\Controller\Photo\PhotoController(
			$this->getService('model.15'),
			$this->getService('model.16'),
			$this->getService('model.17'),
			logger: $this->getService('logger.logger.main.application.controller'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__16(): Application\Controller\Editor\LinkController
	{
		$service = new Application\Controller\Editor\LinkController($this->getService('client.client'));
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__17(): Application\Controller\Editor\ImageController
	{
		$service = new Application\Controller\Editor\ImageController(
			$this->getService('model.18'),
			$this->getService('logger.logger.main.application.controller'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__18(): Application\Controller\Editor\PageController
	{
		$service = new Application\Controller\Editor\PageController(
			$this->getService('serializer.resolver'),
			$this->getService('model.1'),
			$this->getService('model.3'),
			$this->getService('model.2'),
			$this->getService('model.4'),
			logger: $this->getService('logger.logger.main.application.controller'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__19(): Application\Controller\Summary\SummaryController
	{
		$service = new Application\Controller\Summary\SummaryController($this->getService('serializer.resolver'), $this);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__2(): Application\Controller\Page\PageController
	{
		$service = new Application\Controller\Page\PageController(
			$this->getService('serializer.resolver'),
			$this->getService('model.1'),
			$this->getService('model.3'),
			$this->getService('model.2'),
			$this->getService('model.4'),
			logger: $this->getService('logger.logger.main.application.controller'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__3(): Application\Controller\Documentation\DocumentationController
	{
		$service = new Application\Controller\Documentation\DocumentationController(
			$this->getService('model.5'),
			$this->getService('model.8'),
			$this->getService('model.7'),
			$this->getService('model.6'),
			$this->getService('serializer.resolver'),
			$this->getService('logger.logger.main.application.controller'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__4(): Application\Controller\Documentation\TagController
	{
		$service = new Application\Controller\Documentation\TagController(
			$this->getService('model.5'),
			$this->getService('model.8'),
			$this->getService('model.7'),
			$this->getService('model.6'),
			$this->getService('serializer.resolver'),
			$this->getService('logger.logger.main.application.controller'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__5(): Application\Controller\Forum\ForumController
	{
		$service = new Application\Controller\Forum\ForumController(
			$this->getService('model.9'),
			$this->getService('model.11'),
			$this->getService('model.10'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__6(): Application\Controller\Forum\PostController
	{
		$service = new Application\Controller\Forum\PostController(
			$this->getService('model.9'),
			$this->getService('model.11'),
			$this->getService('model.10'),
			$this->getService('logger.logger.main.application.controller'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__7(): Application\Controller\Forum\ThreadController
	{
		$service = new Application\Controller\Forum\ThreadController(
			$this->getService('model.9'),
			$this->getService('model.11'),
			$this->getService('model.10'),
			$this->getService('logger.logger.main.application.controller'),
		);
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__8(): Application\Controller\Index\IndexController
	{
		$service = new Application\Controller\Index\IndexController;
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__9(): Application\Controller\Administration\UserController
	{
		$service = new Application\Controller\Administration\UserController($this->getService('application.security.profile.profile'));
		$service->injectPrimary(
			$this,
			$this->getService('web.request'),
			$this->getService('web.response'),
			$this->getService('session.session.main'),
			templateFactory: $this->getService('template.templateFactory'),
			controllerFactory: $this->getService('controller.factory'),
			toaster: $this->getService('toasting.toaster'),
			notifier: $this->getService('notification.notifier'),
			tokenStorage: $this->getService('authentication.token.storage.session'),
			serializer: $this->getService('serializer.resolver'),
		);
		$service->setEventDispatcher($this->getService('event_dispatcher.dispatcher'));
		$service->setCache($this->getService('cache.cache.application.application.controller'));
		return $service;
	}


	public function createServiceController__event_listener__controller(): nomit\Kernel\EventListener\ControllerEventListener
	{
		return new nomit\Kernel\EventListener\ControllerEventListener(['authentication.user.provider.concrete.database']);
	}


	public function createServiceController__factory(): nomit\Kernel\Component\ControllerFactoryInterface
	{
		$service = new nomit\Kernel\Component\ControllerFactory(
			$this,
			new nomit\Kernel\Component\ControllerFactoryCallback(
				$this,
				'/Users/im.nomit/Sites/nomit.php/tmp//cache/nomit.kernel/touch',
			),
		);
		$service->setMapping([
			'Page' => 'Application\Controller\Page\*\*Controller',
			'Editor' => 'Application\Controller\Editor\*\*Controller',
			'Administration' => 'Application\Controller\Administration\*\*Controller',
			'Documentation' => 'Application\Controller\Documentation\*Controller',
			'Forum' => 'Application\Controller\Forum\*Controller',
		]);
		return $service;
	}


	public function createServiceCryptography__encrypter(): nomit\Cryptography\Encrypter
	{
		return new nomit\Cryptography\Encrypter('A4KLDu_3dfD3KX51', 'AES-128-CBC');
	}


	public function createServiceCryptography__entropy_factory(): nomit\Cryptography\Entropy\EntropyFactory
	{
		return new nomit\Cryptography\Entropy\EntropyFactory;
	}


	public function createServiceCryptography__hasher__sha256(): nomit\Cryptography\Hasher\Sha256Hasher
	{
		return new nomit\Cryptography\Hasher\Sha256Hasher(['verify' => false, 'algorithm' => 'native']);
	}


	public function createServiceCryptography__password__factory(): nomit\Cryptography\Password\PasswordHasherFactory
	{
		return new nomit\Cryptography\Password\PasswordHasherFactory([
			'nomit\Security\User\User' => [
				'class' => 'nomit\Cryptography\Password\NativePasswordHasher',
				'arguments' => [3, 10240, 4],
			],
		]);
	}


	public function createServiceCryptography__password__hasher__message_digest(): nomit\Cryptography\Password\MessageDigestPasswordHasher
	{
		return new nomit\Cryptography\Password\MessageDigestPasswordHasher;
	}


	public function createServiceCryptography__password__hasher__native(): nomit\Cryptography\Password\NativePasswordHasher
	{
		return new nomit\Cryptography\Password\NativePasswordHasher(3, 10240, 4);
	}


	public function createServiceCryptography__password__hasher__pbkdf2(): nomit\Cryptography\Password\Pbkdf2PasswordHasher
	{
		return new nomit\Cryptography\Password\Pbkdf2PasswordHasher;
	}


	public function createServiceCryptography__password__hasher__plaintext(): nomit\Cryptography\Password\PlaintextPasswordHasher
	{
		return new nomit\Cryptography\Password\PlaintextPasswordHasher;
	}


	public function createServiceCryptography__password__hasher__sodium(): nomit\Cryptography\Password\SodiumPasswordHasher
	{
		return new nomit\Cryptography\Password\SodiumPasswordHasher;
	}


	public function createServiceCryptography__password__hasher__user(): nomit\Cryptography\Password\UserPasswordHasher
	{
		return new nomit\Cryptography\Password\UserPasswordHasher($this->getService('cryptography.password.factory'));
	}


	public function createServiceCsrf__manager(): nomit\Security\Csrf\TokenManager
	{
		return new nomit\Security\Csrf\TokenManager(
			$this->getService('csrf.token.storage'),
			$this->getService('cryptography.hasher'),
			$this->getService('csrf.token.generator'),
			'_nomit_security_csrf',
		);
	}


	public function createServiceCsrf__token__generator__uri_safe(): nomit\Security\Csrf\Token\Generator\UriSafeTokenGenerator
	{
		return new nomit\Security\Csrf\Token\Generator\UriSafeTokenGenerator;
	}


	public function createServiceCsrf__token__storage__session(): nomit\Security\Csrf\Token\Storage\SessionTokenStorage
	{
		return new nomit\Security\Csrf\Token\Storage\SessionTokenStorage($this->getService('session'));
	}


	public function createServiceDatabase__default__connection(): nomit\Database\ConnectionInterface
	{
		return new nomit\Database\Connection(
			'mysql:host=127.0.0.1:3306;dbname=nomit;charset=utf8',
			'root',
			$this->getService('secret')->get('DATABASE_PASSWORD'),
			[],
		);
	}


	public function createServiceDatabase__default__conventions(): nomit\Database\Conventions\DiscoveredConventions
	{
		return new nomit\Database\Conventions\DiscoveredConventions($this->getService('database.default.structure'));
	}


	public function createServiceDatabase__default__explorer(): nomit\Database\ExplorerInterface
	{
		return new nomit\Database\Explorer(
			$this->getService('database.default.connection'),
			$this->getService('database.default.structure'),
			$this->getService('database.default.conventions'),
			$this->getService('cache.cache.application'),
		);
	}


	public function createServiceDatabase__default__structure(): nomit\Database\Structure
	{
		return new nomit\Database\Structure(
			$this->getService('database.default.connection'),
			$this->getService('cache.cache.application.database'),
		);
	}


	public function createServiceDependency_injection__di__bridge__error__extension(): nomit\DependencyInjection\Bridge\Error\DependencyInjectionExtension
	{
		return new nomit\DependencyInjection\Bridge\Error\DependencyInjectionExtension($this);
	}


	public function createServiceError__event_listener__exception(): nomit\Kernel\EventListener\ExceptionEventListener
	{
		return new nomit\Kernel\EventListener\ExceptionEventListener(
			$this->getService('error.handler'),
			$this->getService('logger.main'),
			true,
			[],
			'json',
		);
	}


	public function createServiceError__handler(): nomit\Error\ErrorHandler
	{
		return new nomit\Error\ErrorHandler(
			true,
			[$this->getService('error.view.serialized')],
			[$this->getService('error.handler.log')],
			[
				$this->getService('dependency_injection.di.bridge.error.extension'),
				$this->getService('session.bridge.error.extension'),
			],
			$this->getService('error.view.serialized'),
			$this->getService('logger.main'),
		);
	}


	public function createServiceError__handler__log(): nomit\Error\Handler\LogHandler
	{
		return new nomit\Error\Handler\LogHandler($this->getService('logger.logger.main.error'));
	}


	public function createServiceError__view__console(): nomit\Error\View\ConsoleView
	{
		return new nomit\Error\View\ConsoleView;
	}


	public function createServiceError__view__html(): nomit\Error\View\HtmlView
	{
		$service = new nomit\Error\View\HtmlView(
			true,
			$this,
			null,
			'/Users/im.nomit/Sites/nomit.php/config/manifest/manifest.json',
			'/Users/im.nomit/Sites/nomit.php/public/resources/',
			'/Users/im.nomit/Sites/nomit.php/tmp/cache/',
			'UTF-8',
		);
		$service->setRequest($this->getService('web.request'));
		$service->setRequest($this->getService('web.request'));
		return $service;
	}


	public function createServiceError__view__serialized(): nomit\Error\View\SerializedView
	{
		$service = new nomit\Error\View\SerializedView(
			$this->getService('serializer.resolver'),
			nomit\Error\View\SerializedView::getPreferredFormat($this->getService('web.requests'), 'json'),
			$this->getService('error.view.html'),
			'UTF-8',
			nomit\Error\View\HtmlView::isDebug($this->getService('web.requests'), true),
		);
		$service->setRequest($this->getService('web.request'));
		$service->setRequest($this->getService('web.request'));
		return $service;
	}


	public function createServiceEvent_dispatcher__dispatcher(): nomit\EventDispatcher\LazyEventDispatcher
	{
		$service = new nomit\EventDispatcher\LazyEventDispatcher($this);
		$service->addLazySubscriber('kernel.request', 'authentication.event_listener.account_information');
		$service->addLazySubscriber('kernel.response', 'authentication.remember_me.event_listener.response');
		$service->addLazySubscriber('kernel.request', 'authentication.firewall.main');
		$service->addLazySubscriber('kernel.finish_request', 'authentication.firewall.main');
		$service->addLazySubscriber(
			'nomit\Security\Authentication\User\Event\ProvideUserEvent',
			'authorization.event_listener.user_role_provider',
		);
		$service->addLazySubscriber('kernel.controller', 'controller.event_listener.controller');
		$service->addLazySubscriber('kernel.response', 'session.event_listener.closing.main');
		$service->addLazySubscriber('nomit\Security\Session\Event\SavingSessionEvent', 'session.event_listener.closing.main');
		$service->addLazySubscriber('nomit\Security\Session\Event\ClosingSessionEvent', 'session.event_listener.closing.main');
		$service->addLazySubscriber('nomit\Security\Session\Event\ClosedSessionEvent', 'session.event_listener.closing.main');
		$service->addLazySubscriber(
			'nomit\Security\Session\Event\InvalidateSessionEvent',
			'session.event_listener.invalidating.main',
		);
		$service->addLazySubscriber('nomit\Security\Session\Event\OpeningSessionEvent', 'session.event_listener.opening.main');
		$service->addLazySubscriber('nomit\Security\Session\Event\OpenedSessionEvent', 'session.event_listener.opening.main');
		$service->addLazySubscriber(
			'nomit\Security\Session\Event\RegeneratingSessionEvent',
			'session.event_listener.regenerating.main',
		);
		$service->addLazySubscriber('kernel.request', 'session.firewall.main');
		$service->addLazySubscriber('kernel.finish_request', 'session.firewall.main');
		$service->addLazySubscriber('kernel.response', 'web.event_listener.access_control');
		$service->addLazySubscriber('kernel.response', 'web.event_listener.response');
		$service->addLazySubscriber('security.registration.hash_password', 'registration.event_listener.password_hashing');
		$service->addLazySubscriber(
			'nomit\Security\Registration\Event\ConsumeRegistrationEvent',
			'registration.event_listener.registration',
		);
		$service->addLazySubscriber('kernel.request', 'registration.event_listener.registration');
		$service->addLazySubscriber('security.registration', 'registration.event_listener.registration');
		$service->addLazySubscriber('kernel.response', 'registration.event_listener.registration');
		$service->addLazySubscriber('kernel.request', 'confirmation.event_listener.confirmation');
		$service->addLazySubscriber('security.registration.confirm', 'confirmation.event_listener.confirmation');
		$service->addLazySubscriber('security.registration', 'confirmation.event_listener.confirmation');
		$service->addLazySubscriber('kernel.request', 'notification.event_listener.notifying');
		$service->addLazySubscriber(
			'nomit\Notification\Event\RemoveNotificationsEvent',
			'notification.event_listener.remove_notifications',
		);
		$service->addLazySubscriber(
			'nomit\Notification\Event\AddNotificationsEvent',
			'notification.event_listener.stamp_notifications',
		);
		$service->addLazySubscriber(
			'nomit\Notification\Event\UpdateNotificationsEvent',
			'notification.event_listener.stamp_notifications',
		);
		$service->addLazySubscriber(
			'nomit\Notification\Event\AddNotificationsEvent',
			'notification.event_listener.store_notifications',
		);
		$service->addLazySubscriber('nomit\Notification\Event\ResponseEvent', 'notification.event_listener.store_notifications');
		$service->addLazySubscriber(
			'nomit\Security\PasswordReset\Event\HashingPasswordResetEvent',
			'password_reset.event_listener.password_hashing',
		);
		$service->addLazySubscriber('kernel.request', 'password_reset.event_listener.perform');
		$service->addLazySubscriber('kernel.request', 'password_reset.event_listener.request');
		$service->addLazySubscriber('kernel.request', 'password_reset.event_listener.validate');
		$service->addLazySubscriber('profile.hash_password', 'profile.event_listener.password_hashing');
		$service->addLazySubscriber('kernel.request', 'profile.event_listener.update_profile');
		$service->addLazySubscriber('kernel.request', 'profile.event_listener.view_profile');
		$service->addLazySubscriber('nomit\Toasting\Event\StoreEnvelopesEvent', 'toasting.event_listener.preset_envelopes');
		$service->addLazySubscriber('nomit\Toasting\Event\RemoveEnvelopesEvent', 'toasting.event_listener.remove_envelopes');
		$service->addLazySubscriber('nomit\Toasting\Event\StoreEnvelopesEvent', 'toasting.event_listener.stamp_envelopes');
		$service->addLazySubscriber('nomit\Toasting\Event\UpdateEnvelopesEvent', 'toasting.event_listener.stamp_envelopes');
		$service->addLazySubscriber('nomit\Toasting\Event\StoreEnvelopesEvent', 'toasting.event_listener.store_envelopes');
		$service->addLazySubscriber('kernel.request', 'toasting.event_listener.toasting');
		$service->addLazySubscriber('kernel.request', 'application.resource.event_listener');
		$service->addLazySubscriber('kernel.request', 'application.security.authentication.routing.event_listener.routing');
		$service->addLazySubscriber('kernel.request', 'application.security.user.routing.event_listener');
		$service->addLazySubscriber('kernel.request', 'application.rate_limiter.event_listener.application_throttling');
		$service->addLazySubscriber('kernel.request', 'error.event_listener.exception');
		$service->addLazySubscriber('kernel.exception', 'error.event_listener.exception');
		$service->addLazySubscriber('kernel.exception', 'error.event_listener.exception');
		$service->addLazySubscriber('kernel.response', 'error.event_listener.exception');
		return $service;
	}


	public function createServiceFilesystem__filesystem(): nomit\FileSystem\FileSystem
	{
		return new nomit\FileSystem\FileSystem;
	}


	public function createServiceKernel(): nomit\Kernel\Kernel
	{
		return new nomit\Kernel\Kernel(
			$this->getService('routing.router'),
			$this->getService('web.requests'),
			$this->getService('event_dispatcher'),
		);
	}


	public function createServiceKernel__command__about(): nomit\Kernel\Command\AboutCommand
	{
		$service = new nomit\Kernel\Command\AboutCommand;
		$service->setName('about');
		$service->setName('about');
		return $service;
	}


	public function createServiceKernel__command__secret__decrypt_to_local(): nomit\Kernel\Command\DecryptSecretsToLocalCommand
	{
		$service = new nomit\Kernel\Command\DecryptSecretsToLocalCommand(
			$this->getService('kernel.secret.vault'),
			$this->getService('kernel.secret.vault'),
		);
		$service->setName('secrets:decrypt-to-local');
		$service->setName('secrets:decrypt-to-local');
		return $service;
	}


	public function createServiceKernel__command__secret__encrypt_secrets_from_local(): nomit\Kernel\Command\EncryptSecretsFromLocalCommand
	{
		$service = new nomit\Kernel\Command\EncryptSecretsFromLocalCommand(
			$this->getService('kernel.secret.vault'),
			$this->getService('kernel.secret.vault'),
		);
		$service->setName('secrets:encrypt-from-local');
		$service->setName('secrets:encrypt-from-local');
		return $service;
	}


	public function createServiceKernel__command__secret__list(): nomit\Kernel\Command\ListSecretsCommand
	{
		$service = new nomit\Kernel\Command\ListSecretsCommand(
			$this->getService('kernel.secret.vault'),
			$this->getService('kernel.secret.vault'),
		);
		$service->setName('secrets:list');
		$service->setName('secrets:list');
		return $service;
	}


	public function createServiceKernel__command__secret__remove(): nomit\Kernel\Command\RemoveSecretsCommand
	{
		$service = new nomit\Kernel\Command\RemoveSecretsCommand(
			$this->getService('kernel.secret.vault'),
			$this->getService('kernel.secret.vault'),
		);
		$service->setName('secrets:remove');
		$service->setName('secrets:remove');
		return $service;
	}


	public function createServiceKernel__command__secret__set(): nomit\Kernel\Command\SetSecretsCommand
	{
		$service = new nomit\Kernel\Command\SetSecretsCommand(
			$this->getService('kernel.secret.vault'),
			$this->getService('kernel.secret.vault'),
		);
		$service->setName('secrets:set');
		$service->setName('secrets:set');
		return $service;
	}


	public function createServiceKernel__kernel(): nomit\Kernel\KernelInterface
	{
		$service = new nomit\Kernel\Kernel(
			$this->getService('routing.router'),
			$this->getService('web.requests'),
			$this->getService('event_dispatcher.dispatcher'),
		);
		$service->catchExceptions = false;
		return $service;
	}


	public function createServiceKernel__secret__vault(): nomit\Kernel\Secret\OpenSslVault
	{
		return new nomit\Kernel\Secret\OpenSslVault(
			'/Users/im.nomit/Sites/nomit.php/tmp/secrets/',
			[],
			'nomit\DependencyInjection\Utilities'::convertType(getenv('APP_SECRET'), 'string'),
		);
	}


	public function createServiceLock__factory(): nomit\Lock\LockFactory
	{
		return new nomit\Lock\LockFactory($this->getService('lock.store.database'), $this->getService('logger.logger.main.lock'));
	}


	public function createServiceLock__store__database(): nomit\Lock\Store\DatabaseStore
	{
		return new nomit\Lock\Store\DatabaseStore($this->getService('database.default'), $this->getService('database.default.explorer'));
	}


	public function createServiceLock__strategy__majority(): nomit\Lock\Strategy\ConsensusStrategy
	{
		return new nomit\Lock\Strategy\ConsensusStrategy;
	}


	public function createServiceLock__strategy__unanimous(): nomit\Lock\Strategy\UnanimousStrategy
	{
		return new nomit\Lock\Strategy\UnanimousStrategy;
	}


	public function createServiceLogger__command__collect_garbage(): nomit\Logger\Command\CollectGarbageLoggerCommand
	{
		$service = new nomit\Logger\Command\CollectGarbageLoggerCommand(
			$this->getService('filesystem.filesystem'),
			'/Users/im.nomit/Sites/nomit.php/tmp//logs/',
			$this->getService('lock.factory'),
		);
		$service->setName('logger:gc');
		$service->setName('logger:gc');
		return $service;
	}


	public function createServiceLogger__handler__stream(): nomit\Logger\Handler\StreamHandler
	{
		return new nomit\Logger\Handler\StreamHandler('/Users/im.nomit/Sites/nomit.php/tmp/logs/logs.log');
	}


	public function createServiceLogger__handler__stream__main(): nomit\Logger\Handler\StreamHandler
	{
		return new nomit\Logger\Handler\StreamHandler('/Users/im.nomit/Sites/nomit.php/tmp/logs//main.log', 100, false);
	}


	public function createServiceLogger__logger__main__application__controller(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'application.controller',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__application__model(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'application.model',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__application__security__user(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'application.security.user',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__error(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'error',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__lock(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'lock',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__messenger(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'messenger',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__notification__event_listener(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'notification.event_listener',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__notification__storage(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'notification.storage',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__resource(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'resource',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__authentication(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.authentication',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__authentication__authenticator__remember_me(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.authentication.authenticator.remember_me',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__authentication__authenticator__remember_me__main(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.authentication.authenticator.remember_me.main',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__authentication__firewall__logout__main(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.authentication.firewall.logout.main',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__authentication__firewall__main(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.authentication.firewall.main',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__authentication__handler__failure(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.authentication.handler.failure',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__authentication__rate_limiter(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.authentication.rate_limiter',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__authentication__remember_me(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.authentication.remember_me',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__authentication__routing(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.authentication.routing',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__authentication__user__provider(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.authentication.user.provider',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__authorization(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.authorization',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__password_hashing(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.password_hashing',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__password_reset(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.password_reset',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__password_reset__token__persistence(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.password_reset.token.persistence',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__profile(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.profile',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__registration(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.registration',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__registration__confirmation(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.registration.confirmation',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__session(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.session',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__session__firewall__main(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.session.firewall.main',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__logger__main__security__session__token__persistence(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'security.session.token.persistence',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__main(): nomit\Logger\Logger
	{
		return new nomit\Logger\Logger(
			'main',
			[$this->getService('logger.handler.stream.main')],
			[$this->getService('logger.processor.tag.main'), $this->getService('logger.processor.psr_log_message.main')],
		);
	}


	public function createServiceLogger__processor__git(): nomit\Logger\Processor\GitProcessor
	{
		return new nomit\Logger\Processor\GitProcessor;
	}


	public function createServiceLogger__processor__hostname(): nomit\Logger\Processor\HostnameProcessor
	{
		return new nomit\Logger\Processor\HostnameProcessor;
	}


	public function createServiceLogger__processor__instrospection(): nomit\Logger\Processor\IntrospectionProcessor
	{
		return new nomit\Logger\Processor\IntrospectionProcessor;
	}


	public function createServiceLogger__processor__memory__peak_usage(): nomit\Logger\Processor\MemoryPeakUsageProcessor
	{
		return new nomit\Logger\Processor\MemoryPeakUsageProcessor;
	}


	public function createServiceLogger__processor__memory__usage(): nomit\Logger\Processor\MemoryUsageProcessor
	{
		return new nomit\Logger\Processor\MemoryUsageProcessor;
	}


	public function createServiceLogger__processor__mercurial(): nomit\Logger\Processor\MercurialProcessor
	{
		return new nomit\Logger\Processor\MercurialProcessor;
	}


	public function createServiceLogger__processor__process_id(): nomit\Logger\Processor\ProcessIdProcessor
	{
		return new nomit\Logger\Processor\ProcessIdProcessor;
	}


	public function createServiceLogger__processor__psr_log_message(): nomit\Logger\Processor\PsrLogMessageProcessor
	{
		return new nomit\Logger\Processor\PsrLogMessageProcessor;
	}


	public function createServiceLogger__processor__psr_log_message__main(): nomit\Logger\Processor\PsrLogMessageProcessor
	{
		return new nomit\Logger\Processor\PsrLogMessageProcessor;
	}


	public function createServiceLogger__processor__tag(): nomit\Logger\Processor\TagProcessor
	{
		return new nomit\Logger\Processor\TagProcessor;
	}


	public function createServiceLogger__processor__tag__main(): nomit\Logger\Processor\TagProcessor
	{
		return new nomit\Logger\Processor\TagProcessor;
	}


	public function createServiceLogger__processor__uid(): nomit\Logger\Processor\UidProcessor
	{
		return new nomit\Logger\Processor\UidProcessor;
	}


	public function createServiceLogger__processor__web(): nomit\Logger\Processor\WebProcessor
	{
		return new nomit\Logger\Processor\WebProcessor;
	}


	public function createServiceMail__mailer(): nomit\Mail\MailerInterface
	{
		return new nomit\Mail\NativeMailer;
	}


	public function createServiceMarkdown__parser(): nomit\Utility\Serialization\Markdown\ParserInterface
	{
		return new nomit\Utility\Serialization\Markdown\Markdown;
	}


	public function createServiceMessenger__bus(): nomit\Messenger\MessageBus
	{
		return new nomit\Messenger\MessageBus(
			$this->getService('messenger.handler.router'),
			$this->getService('messenger.serializer'),
			$this->getService('messenger.queue.factory'),
			$this->getService('messenger.producer'),
			$this->getService('messenger.consumer'),
			$this->getService('config'),
		);
	}


	public function createServiceMessenger__command__consume(): nomit\Messenger\Command\ConsumeMessagesCommand
	{
		$service = new nomit\Messenger\Command\ConsumeMessagesCommand($this->getService('messenger.bus'));
		$service->setName('messenger:consume');
		$service->setName('messenger:consume');
		return $service;
	}


	public function createServiceMessenger__consumer(): nomit\Messenger\Consumer\Consumer
	{
		return new nomit\Messenger\Consumer\Consumer(
			$this->getService('messenger.worker'),
			$this->getService('messenger.handler.router'),
			[
				'max_runtime' => 9223372036854775807,
				'max_attempts' => 10,
				'stop_when_empty' => true,
				'catch_exceptions' => false,
			],
			$this->getService('event_dispatcher.dispatcher'),
			$this->getService('logger.logger.main.messenger'),
		);
	}


	public function createServiceMessenger__driver__filesystem__system(): nomit\Messenger\Driver\FileSystemDriver
	{
		return new nomit\Messenger\Driver\FileSystemDriver('/Users/im.nomit/Sites/nomit.php/tmp/cache/nomit.messenger/', 740);
	}


	public function createServiceMessenger__handler__callback(): nomit\Messenger\Handler\CallbackHandlerFactory
	{
		return new nomit\Messenger\Handler\CallbackHandlerFactory;
	}


	public function createServiceMessenger__handler__closure(): nomit\Messenger\Handler\ClosureHandlerFactory
	{
		return new nomit\Messenger\Handler\ClosureHandlerFactory;
	}


	public function createServiceMessenger__handler__invokable(): nomit\Messenger\Handler\InvokableHandlerFactory
	{
		return new nomit\Messenger\Handler\InvokableHandlerFactory;
	}


	public function createServiceMessenger__handler__repository(): nomit\Messenger\Repository\HandlerRepository
	{
		return new nomit\Messenger\Repository\HandlerRepository([]);
	}


	public function createServiceMessenger__handler__resolver(): nomit\Messenger\Handler\MapHandlerResolver
	{
		return new nomit\Messenger\Handler\MapHandlerResolver([
			$this->getService('messenger.handler.callback'),
			$this->getService('messenger.handler.closure'),
			$this->getService('messenger.handler.invokable'),
		]);
	}


	public function createServiceMessenger__handler__router(): nomit\Messenger\Router\ContainerHandlerRouter
	{
		return new nomit\Messenger\Router\ContainerHandlerRouter(
			$this->getService('messenger.producer'),
			$this,
			$this->getService('messenger.handler.repository'),
			$this->getService('messenger.handler.resolver'),
		);
	}


	public function createServiceMessenger__producer(): nomit\Messenger\Producer\Producer
	{
		return new nomit\Messenger\Producer\Producer(
			$this->getService('messenger.queue.factory'),
			$this->getService('event_dispatcher.dispatcher'),
		);
	}


	public function createServiceMessenger__queue__factory(): nomit\Messenger\Queue\QueueFactory
	{
		return new nomit\Messenger\Queue\QueueFactory(
			['system' => $this->getService('messenger.queue.system')],
			$this->getService('messenger.serializer'),
		);
	}


	public function createServiceMessenger__queue__system(): nomit\Messenger\Queue\PersistentQueue
	{
		return new nomit\Messenger\Queue\PersistentQueue(
			'system',
			$this->getService('messenger.driver.filesystem.system'),
			$this->getService('messenger.serializer'),
		);
	}


	public function createServiceMessenger__serializer(): nomit\Messenger\Serialization\Serializer
	{
		return new nomit\Messenger\Serialization\Serializer($this->getService('serializer'), 'json', []);
	}


	public function createServiceMessenger__worker(): nomit\Messenger\Worker\Worker
	{
		return new nomit\Messenger\Worker\Worker(
			$this->getService('event_dispatcher.dispatcher'),
			$this->getService('logger.logger.main.messenger'),
		);
	}


	public function createServiceModel__1(): Application\Model\Page\PageModel
	{
		$service = new Application\Model\Page\PageModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__10(): Application\Model\Forum\Thread\PostModel
	{
		$service = new Application\Model\Forum\Thread\PostModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__11(): Application\Model\Forum\Thread\ThreadModel
	{
		$service = new Application\Model\Forum\Thread\ThreadModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__12(): Application\Model\Article\Participant\ParticipantModel
	{
		$service = new Application\Model\Article\Participant\ParticipantModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__13(): Application\Model\Article\ArticleModel
	{
		$service = new Application\Model\Article\ArticleModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__14(): Application\Model\Article\Revision\RevisionModel
	{
		$service = new Application\Model\Article\Revision\RevisionModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
			$this->getService('logger.logger.main.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__15(): Application\Model\Photo\PhotoModel
	{
		$service = new Application\Model\Photo\PhotoModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__16(): Application\Model\Photo\Participant\ParticipantModel
	{
		$service = new Application\Model\Photo\Participant\ParticipantModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__17(): Application\Model\Photo\Revision\RevisionModel
	{
		$service = new Application\Model\Photo\Revision\RevisionModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__18(): Application\Model\Editor\Image\ImageModel
	{
		$service = new Application\Model\Editor\Image\ImageModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__2(): Application\Model\Page\Participant\ParticipantModel
	{
		$service = new Application\Model\Page\Participant\ParticipantModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__3(): Application\Model\Page\Discussion\DiscussionModel
	{
		$service = new Application\Model\Page\Discussion\DiscussionModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__4(): Application\Model\Page\Revision\RevisionModel
	{
		$service = new Application\Model\Page\Revision\RevisionModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__5(): Application\Model\Documentation\Revision\DocumentationModel
	{
		$service = new Application\Model\Documentation\Revision\DocumentationModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__6(): Application\Model\Documentation\Tag\TagRelationshipModel
	{
		$service = new Application\Model\Documentation\Tag\TagRelationshipModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__7(): Application\Model\Documentation\Tag\TagModel
	{
		$service = new Application\Model\Documentation\Tag\TagModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__8(): Application\Model\Documentation\Revision\RevisionModel
	{
		$service = new Application\Model\Documentation\Revision\RevisionModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceModel__9(): Application\Model\Forum\ForumModel
	{
		$service = new Application\Model\Forum\ForumModel(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('cache.cache.application.application.model'),
		);
		$service->injectPrimary($this, $this->getService('filesystem.filesystem'), $this->getService('serializer.resolver'));
		return $service;
	}


	public function createServiceNotification__command__clear_user(): nomit\Notification\Command\ClearUserNotificationCommand
	{
		$service = new nomit\Notification\Command\ClearUserNotificationCommand(
			$this->getService('notification.notifier'),
			[$this->getService('authentication.user.provider.concrete.database')],
		);
		$service->setName('notification:clear');
		$service->setName('notification:clear');
		return $service;
	}


	public function createServiceNotification__command__get_user(): nomit\Notification\Command\GetUserNotificationCommand
	{
		$service = new nomit\Notification\Command\GetUserNotificationCommand(
			$this->getService('notification.notifier'),
			[$this->getService('authentication.user.provider.concrete.database')],
		);
		$service->setName('notification:user');
		$service->setName('notification:user');
		return $service;
	}


	public function createServiceNotification__command__push_to_everyone(): nomit\Notification\Command\PushToEveryoneNotificationCommand
	{
		$service = new nomit\Notification\Command\PushToEveryoneNotificationCommand(
			$this->getService('notification.notifier'),
			[$this->getService('authentication.user.provider.concrete.database')],
		);
		$service->setName('notification:everyone');
		$service->setName('notification:everyone');
		return $service;
	}


	public function createServiceNotification__command__push_to_user(): nomit\Notification\Command\PushToUserNotificationCommand
	{
		$service = new nomit\Notification\Command\PushToUserNotificationCommand(
			$this->getService('notification.notifier'),
			[$this->getService('authentication.user.provider.concrete.database')],
		);
		$service->setName('notification:user');
		return $service;
	}


	public function createServiceNotification__event_listener__notifying(): nomit\Notification\EventListener\NotifyingEventListener
	{
		return new nomit\Notification\EventListener\NotifyingEventListener(
			$this->getService('notification.notifier'),
			$this->getService('authentication.token.storage.session'),
			(object) [
				'path' => '/api/notifications',
				'bag' => 'filesystem',
				'default_format' => 'json',
				'serialization' => (object) [
					'format' => 'json',
					'context' => [],
				],
				'views' => [
					'php' => (object) [
						'service' => 'array',
						'serialized' => (object) [
							'format' => null,
						],
					],
					'json' => (object) [
						'service' => 'serialized',
						'serialized' => (object) [
							'format' => 'json',
						],
					],
				],
				'criteria' => (object) [
					'ttl' => 300,
				],
				'context' => [],
				'maximum_duration' => 10000,
				'lock' => (object) [
					'prefix' => '_notification.user',
				],
			],
			$this->getService('logger.main'),
		);
	}


	public function createServiceNotification__event_listener__remove_notifications(): nomit\Notification\EventListener\RemoveNotificationsEventListener
	{
		return new nomit\Notification\EventListener\RemoveNotificationsEventListener;
	}


	public function createServiceNotification__event_listener__stamp_notifications(): nomit\Notification\EventListener\StampNotificationsEventListener
	{
		return new nomit\Notification\EventListener\StampNotificationsEventListener;
	}


	public function createServiceNotification__event_listener__store_notifications(): nomit\Notification\EventListener\StoreNotificationsEventListener
	{
		return new nomit\Notification\EventListener\StoreNotificationsEventListener(
			$this->getService('notification.storage.manager'),
			$this->getService('logger.main'),
		);
	}


	public function createServiceNotification__notifier(): nomit\Notification\Notifier
	{
		return new nomit\Notification\Notifier(
			'json',
			$this->getService('notification.response.responder'),
			$this->getService('notification.storage.manager'),
		);
	}


	public function createServiceNotification__response__responder(): nomit\Notification\Response\Responder
	{
		return new nomit\Notification\Response\Responder(
			$this->getService('notification.storage.manager'),
			$this->getService('event_dispatcher.dispatcher'),
			[
				'php' => $this->getService('notification.response.view.array'),
				'json' => $this->getService('notification.response.view.serialized'),
			],
		);
	}


	public function createServiceNotification__response__view__array(): nomit\Notification\Response\View\ArrayView
	{
		return new nomit\Notification\Response\View\ArrayView;
	}


	public function createServiceNotification__response__view__serialized(): nomit\Notification\Response\View\SerializedView
	{
		return new nomit\Notification\Response\View\SerializedView($this->getService('serializer'), 'json');
	}


	public function createServiceNotification__serialization__serializer(): nomit\Notification\Serialization\Serializer
	{
		return new nomit\Notification\Serialization\Serializer($this->getService('serializer.resolver'), 'json', []);
	}


	public function createServiceNotification__storage__bag(): nomit\Notification\Storage\BagStorage
	{
		return new nomit\Notification\Storage\BagStorage(
			$this->getService('notification.storage.bag.filesystem'),
			$this->getService('event_dispatcher.dispatcher'),
			(object) [
				'ttl' => 300,
			],
		);
	}


	public function createServiceNotification__storage__bag__array(): nomit\Notification\Storage\Bag\ArrayBag
	{
		return new nomit\Notification\Storage\Bag\ArrayBag;
	}


	public function createServiceNotification__storage__bag__filesystem(): nomit\Notification\Storage\Bag\FileSystemBag
	{
		return new nomit\Notification\Storage\Bag\FileSystemBag(
			$this->getService('filesystem.filesystem'),
			$this->getService('notification.serialization.serializer'),
			$this->getService('lock.factory'),
			'/Users/im.nomit/Sites/nomit.php/tmp/',
			'_notification.user',
			$this->getService('logger.main'),
		);
	}


	public function createServiceNotification__storage__manager(): nomit\Notification\Storage\StorageManager
	{
		return new nomit\Notification\Storage\StorageManager(
			$this->getService('notification.storage.bag'),
			$this->getService('event_dispatcher.dispatcher'),
			$this->getService('logger.main'),
			['ttl' => 300],
		);
	}


	public function createServiceParameters__factory(): nomit\Kernel\Secret\SecretFactory
	{
		return new nomit\Kernel\Secret\SecretFactory([$this->getService('kernel.secret.vault')]);
	}


	public function createServicePassword_reset__event_listener__password_hashing(): nomit\Security\PasswordReset\EventListener\PasswordHashingPasswordResetEventListener
	{
		return new nomit\Security\PasswordReset\EventListener\PasswordHashingPasswordResetEventListener($this->getService('logger.main'));
	}


	public function createServicePassword_reset__event_listener__perform(): nomit\Security\PasswordReset\EventListener\PerformPasswordResetEventListener
	{
		return new nomit\Security\PasswordReset\EventListener\PerformPasswordResetEventListener(
			$this->getService('csrf.manager'),
			$this->getService('session.session.main'),
			$this->getService('cryptography.password.hasher.native'),
			['authentication.user.provider.concrete.database'],
			$this,
			(object) [
				'password_hasher' => 'native',
				'catch_exceptions' => false,
				'token' => (object) [
					'ttl' => 360400,
					'session_token_name' => '_security.password_reset.token',
					'csrf_token_name' => '_security.password_reset.token',
					'persistence' => (object) [
						'service' => 'database',
						'database' => (object) [
							'table' => 'user_password_resets',
						],
					],
				],
				'request' => (object) [
					'path' => '/api/account/reset/request',
					'mail' => (object) [
						'from' => 'do-not-reply@nomit.ca',
						'subject' => 'Your Password Reset Request',
					],
				],
				'validate' => (object) [
					'path' => '/api/account/reset/validate',
				],
				'perform' => (object) [
					'path' => '/api/account/reset/perform',
				],
			],
			$this->getService('event_dispatcher.dispatcher'),
			$this->getService('logger.main'),
		);
	}


	public function createServicePassword_reset__event_listener__request(): nomit\Security\PasswordReset\EventListener\RequestPasswordResetEventListener
	{
		return new nomit\Security\PasswordReset\EventListener\RequestPasswordResetEventListener(
			$this->getService('password_reset.token.manager'),
			$this->getService('password_reset.mail.mailer'),
			(object) [
				'password_hasher' => 'native',
				'catch_exceptions' => false,
				'token' => (object) [
					'ttl' => 360400,
					'session_token_name' => '_security.password_reset.token',
					'csrf_token_name' => '_security.password_reset.token',
					'persistence' => (object) [
						'service' => 'database',
						'database' => (object) [
							'table' => 'user_password_resets',
						],
					],
				],
				'request' => (object) [
					'path' => '/api/account/reset/request',
					'mail' => (object) [
						'from' => 'do-not-reply@nomit.ca',
						'subject' => 'Your Password Reset Request',
					],
				],
				'validate' => (object) [
					'path' => '/api/account/reset/validate',
				],
				'perform' => (object) [
					'path' => '/api/account/reset/perform',
				],
			],
			['authentication.user.provider.concrete.database'],
			$this,
			$this->getService('logger.main'),
		);
	}


	public function createServicePassword_reset__event_listener__validate(): nomit\Security\PasswordReset\EventListener\ValidatePasswordResetEventListener
	{
		return new nomit\Security\PasswordReset\EventListener\ValidatePasswordResetEventListener(
			$this->getService('password_reset.token.manager'),
			$this->getService('csrf.manager'),
			$this->getService('session.session.main'),
			(object) [
				'password_hasher' => 'native',
				'catch_exceptions' => false,
				'token' => (object) [
					'ttl' => 360400,
					'session_token_name' => '_security.password_reset.token',
					'csrf_token_name' => '_security.password_reset.token',
					'persistence' => (object) [
						'service' => 'database',
						'database' => (object) [
							'table' => 'user_password_resets',
						],
					],
				],
				'request' => (object) [
					'path' => '/api/account/reset/request',
					'mail' => (object) [
						'from' => 'do-not-reply@nomit.ca',
						'subject' => 'Your Password Reset Request',
					],
				],
				'validate' => (object) [
					'path' => '/api/account/reset/validate',
				],
				'perform' => (object) [
					'path' => '/api/account/reset/perform',
				],
			],
			$this->getService('logger.main'),
		);
	}


	public function createServicePassword_reset__mail__mailer(): nomit\Security\PasswordReset\Mail\Mailer
	{
		return new nomit\Security\PasswordReset\Mail\Mailer(
			$this->getService('mail.mailer'),
			'/Users/im.nomit/Sites/nomit.php/public/resources/',
		);
	}


	public function createServicePassword_reset__token__manager(): nomit\Security\PasswordReset\Token\TokenManager
	{
		return new nomit\Security\PasswordReset\Token\TokenManager(
			$this->getService('cryptography.hasher.sha256'),
			$this->getService('password_reset.token.persistence.database'),
			(object) [
				'password_hasher' => 'native',
				'catch_exceptions' => false,
				'token' => (object) [
					'ttl' => 360400,
					'session_token_name' => '_security.password_reset.token',
					'csrf_token_name' => '_security.password_reset.token',
					'persistence' => (object) [
						'service' => 'database',
						'database' => (object) [
							'table' => 'user_password_resets',
						],
					],
				],
				'request' => (object) [
					'path' => '/api/account/reset/request',
					'mail' => (object) [
						'from' => 'do-not-reply@nomit.ca',
						'subject' => 'Your Password Reset Request',
					],
				],
				'validate' => (object) [
					'path' => '/api/account/reset/validate',
				],
				'perform' => (object) [
					'path' => '/api/account/reset/perform',
				],
			],
		);
	}


	public function createServicePassword_reset__token__persistence__database(): nomit\Security\PasswordReset\Token\Persistence\DatabaseTokenPersistence
	{
		return new nomit\Security\PasswordReset\Token\Persistence\DatabaseTokenPersistence(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			'user_password_resets',
			$this->getService('logger.main'),
		);
	}


	public function createServiceProfile__event_listener__password_hashing(): nomit\Security\Profile\EventListener\PasswordHashingEventListener
	{
		return new nomit\Security\Profile\EventListener\PasswordHashingEventListener($this->getService('logger.logger.main.security.profile'));
	}


	public function createServiceProfile__event_listener__update_profile(): nomit\Security\Profile\EventListener\UpdateProfileEventListener
	{
		return new nomit\Security\Profile\EventListener\UpdateProfileEventListener(
			$this->getService('authentication.token.storage.session'),
			$this->getService('profile.provider.database'),
			$this->getService('cryptography.password.hasher.native'),
			$this->getService('profile.handler.success.default'),
			$this->getService('profile.handler.failure.default'),
			(object) [
				'path' => '/api/account/profile/update',
				'images_path' => '/Users/im.nomit/Sites/nomit.php/public/resources/dynamic/images/Application/User/',
				'password_hasher' => 'native',
				'success_handler' => 'default',
				'failure_handler' => 'default',
				'user_provider' => 'database',
				'token_name' => '_security_profile.token',
			],
			$this->getService('toasting.toaster'),
			$this->getService('csrf.manager'),
			$this->getService('event_dispatcher.dispatcher'),
			$this->getService('logger.main'),
		);
	}


	public function createServiceProfile__event_listener__view_profile(): nomit\Security\Profile\EventListener\ViewProfileEventListener
	{
		return new nomit\Security\Profile\EventListener\ViewProfileEventListener(
			$this->getService('application.security.profile.profile'),
			$this->getService('authentication.token.storage.session'),
			$this->getService('profile.provider.database'),
			(object) [
				'path' => '/api/profile',
				'user_provider' => 'database',
			],
			$this->getService('logger.main'),
		);
	}


	public function createServiceProfile__handler__failure__default(): nomit\Security\Profile\Handler\DefaultFailedProfileUpdateHandler
	{
		return new nomit\Security\Profile\Handler\DefaultFailedProfileUpdateHandler;
	}


	public function createServiceProfile__handler__success__default(): nomit\Security\Profile\Handler\DefaultSuccessfulProfileUpdateHandler
	{
		return new nomit\Security\Profile\Handler\DefaultSuccessfulProfileUpdateHandler;
	}


	public function createServiceProfile__provider__database(): nomit\Security\Profile\User\DatabaseProfileUserProvider
	{
		return new nomit\Security\Profile\User\DatabaseProfileUserProvider(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('event_dispatcher.dispatcher'),
			$this->getService('logger.main'),
		);
	}


	public function createServiceProperty_accessor__accessor(): nomit\Property\Accessor\PropertyAccessor
	{
		return new nomit\Property\Accessor\PropertyAccessor(3, 2);
	}


	public function createServiceRate_limiter__factory___login_globalmain(): nomit\RateLimiter\RateLimiterFactory
	{
		return new nomit\RateLimiter\RateLimiterFactory(
			['policy' => 'fixed_window', 'limit' => 50, 'interval' => '5 minutes', 'rate' => [], 'id' => '_login_globalmain'],
			$this->getService('rate_limiter.storage.cache'),
			$this->getService('lock.factory'),
		);
	}


	public function createServiceRate_limiter__factory___login_local__main(): nomit\RateLimiter\RateLimiterFactory
	{
		return new nomit\RateLimiter\RateLimiterFactory(
			['policy' => 'fixed_window', 'limit' => 10, 'interval' => '5 minutes', 'rate' => [], 'id' => '_login_local.main'],
			$this->getService('rate_limiter.storage.cache'),
			$this->getService('lock.factory'),
		);
	}


	public function createServiceRate_limiter__factory__anonymous_api(): nomit\RateLimiter\RateLimiterFactory
	{
		return new nomit\RateLimiter\RateLimiterFactory(
			[
				'policy' => 'fixed_window',
				'limit' => 150,
				'interval' => '5 minutes',
				'rate' => ['interval' => '10 minutes', 'amount' => 150],
				'id' => 'anonymous_api',
			],
			$this->getService('rate_limiter.storage.cache'),
			$this->getService('lock.factory'),
		);
	}


	public function createServiceRate_limiter__factory__authenticated_api(): nomit\RateLimiter\RateLimiterFactory
	{
		return new nomit\RateLimiter\RateLimiterFactory(
			[
				'policy' => 'token_bucket',
				'interval' => '5 minutes',
				'limit' => 500,
				'rate' => ['interval' => '10 minutes', 'amount' => 500],
				'id' => 'authenticated_api',
			],
			$this->getService('rate_limiter.storage.cache'),
			$this->getService('lock.factory'),
		);
	}


	public function createServiceRate_limiter__storage__cache(): nomit\RateLimiter\Storage\CacheStorage
	{
		return new nomit\RateLimiter\Storage\CacheStorage($this->getService('cache'));
	}


	public function createServiceRate_limiter__storage__in_memory(): nomit\RateLimiter\Storage\InMemoryStorage
	{
		return new nomit\RateLimiter\Storage\InMemoryStorage;
	}


	public function createServiceRegistration__event_listener__password_hashing(): nomit\Security\Registration\EventListener\PasswordHashingEventListener
	{
		return new nomit\Security\Registration\EventListener\PasswordHashingEventListener($this->getService('logger.main'));
	}


	public function createServiceRegistration__event_listener__registration(): nomit\Security\Registration\EventListener\RegistrationEventListener
	{
		return new nomit\Security\Registration\EventListener\RegistrationEventListener(
			$this->getService('registration.token.storage.session'),
			$this->getService('registration.user.provider.database'),
			$this->getService('registration.handler.success.default'),
			$this->getService('registration.handler.failure.default'),
			$this->getService('cryptography.password.hasher.native'),
			'/Users/im.nomit/Sites/nomit.php/public/resources/',
			'_security.registration.token',
			'_security.registration.request_token',
			86400,
			$this->getService('event_dispatcher.dispatcher'),
			$this->getService('logger.main'),
		);
	}


	public function createServiceRegistration__handler__failure__default(): nomit\Security\Registration\Handler\DefaultFailedRegistrationHandler
	{
		return new nomit\Security\Registration\Handler\DefaultFailedRegistrationHandler;
	}


	public function createServiceRegistration__handler__success__default(): nomit\Security\Registration\Handler\DefaultSuccessfulRegistrationHandler
	{
		return new nomit\Security\Registration\Handler\DefaultSuccessfulRegistrationHandler;
	}


	public function createServiceRegistration__token__storage__session(): nomit\Security\Registration\Token\Storage\SessionTokenStorage
	{
		return new nomit\Security\Registration\Token\Storage\SessionTokenStorage(
			$this->getService('session.session.main'),
			'token',
			'_security.registration.token',
			$this->getService('cryptography.encrypter'),
		);
	}


	public function createServiceRegistration__user__provider__database(): nomit\Security\Registration\User\DatabaseRegistrationUserProvider
	{
		return new nomit\Security\Registration\User\DatabaseRegistrationUserProvider(
			$this->getService('database.default.connection'),
			$this->getService('database.default.explorer'),
			$this->getService('event_dispatcher.dispatcher'),
			$this->getService('logger.main'),
			'users',
		);
	}


	public function createServiceResource__command__clear_cache(): nomit\Resource\Command\ClearCacheResourceCommand
	{
		$service = new nomit\Resource\Command\ClearCacheResourceCommand(
			$this->getService('filesystem'),
			'/Users/im.nomit/Sites/nomit.php/tmp/cache/',
			true,
		);
		$service->setName('resource:clear');
		$service->setName('resource:clear');
		return $service;
	}


	public function createServiceRouting__loader(): nomit\Routing\Loader\ConfigLoader
	{
		$service = new nomit\Routing\Loader\ConfigLoader(null, null, $this->getService('routing.reference_resolver'));
		$service->attach('/Users/im.nomit/Sites/nomit.php/app//Routing/routes.php');
		$service->setContainer($this);
		return $service;
	}


	public function createServiceRouting__reference_resolver(): nomit\Routing\ReferenceResolver
	{
		return new nomit\Routing\ReferenceResolver(
			$this,
			$this->getService('controller.factory'),
			$this->getService('event_dispatcher.dispatcher'),
		);
	}


	public function createServiceRouting__router(): nomit\Routing\Router
	{
		$service = new nomit\Routing\Router;
		$service->load($this->getService('routing.loader'));
		return $service;
	}


	public function createServiceSerializer__encoder__json(): nomit\Serialization\Encoder\JsonEncoder
	{
		return new nomit\Serialization\Encoder\JsonEncoder;
	}


	public function createServiceSerializer__encoder__md(): nomit\Serialization\Encoder\MarkdownEncoder
	{
		return new nomit\Serialization\Encoder\MarkdownEncoder($this->getService('markdown.parser'));
	}


	public function createServiceSerializer__encoder__neon(): nomit\Serialization\Encoder\NeonEncoder
	{
		return new nomit\Serialization\Encoder\NeonEncoder;
	}


	public function createServiceSerializer__encoder__null(): nomit\Serialization\Encoder\NullEncoder
	{
		return new nomit\Serialization\Encoder\NullEncoder;
	}


	public function createServiceSerializer__encoder__php(): nomit\Serialization\Encoder\PhpEncoder
	{
		return new nomit\Serialization\Encoder\PhpEncoder;
	}


	public function createServiceSerializer__encoder__xml(): nomit\Serialization\Encoder\XmlEncoder
	{
		return new nomit\Serialization\Encoder\XmlEncoder;
	}


	public function createServiceSerializer__encoder__yaml(): nomit\Serialization\Encoder\YamlEncoder
	{
		return new nomit\Serialization\Encoder\YamlEncoder;
	}


	public function createServiceSerializer__resolver(): nomit\Serialization\SerializerResolverInterface
	{
		return new nomit\Serialization\SerializerResolver(
			[
				$this->getService('serializer.encoder.json'),
				$this->getService('serializer.encoder.php'),
				$this->getService('serializer.encoder.xml'),
				$this->getService('serializer.encoder.yaml'),
				$this->getService('serializer.encoder.md'),
				$this->getService('serializer.encoder.neon'),
			],
			[],
		);
	}


	public function createServiceSerializer__transformer__array(): nomit\Serialization\Transformer\ArrayTransformer
	{
		return new nomit\Serialization\Transformer\ArrayTransformer;
	}


	public function createServiceSerializer__transformer__array__flat(): nomit\Serialization\Transformer\FlatArrayTransformer
	{
		return new nomit\Serialization\Transformer\FlatArrayTransformer;
	}


	public function createServiceSerializer__transformer__closure(): nomit\Serialization\Transformer\ClosureTransformer
	{
		return new nomit\Serialization\Transformer\ClosureTransformer;
	}


	public function createServiceSerializer__transformer__json(): nomit\Serialization\Transformer\JsonTransformer
	{
		return new nomit\Serialization\Transformer\JsonTransformer;
	}


	public function createServiceSerializer__transformer__xml(): nomit\Serialization\Transformer\XmlTransformer
	{
		return new nomit\Serialization\Transformer\XmlTransformer;
	}


	public function createServiceSerializer__transformer__yaml(): nomit\Serialization\Transformer\YamlTransformer
	{
		return new nomit\Serialization\Transformer\YamlTransformer;
	}


	public function createServiceSession__bridge__error__extension(): nomit\Security\Session\Bridge\Error\SessionExtension
	{
		return new nomit\Security\Session\Bridge\Error\SessionExtension([$this->getService('session.session.main')]);
	}


	public function createServiceSession__context__main(): nomit\Security\Session\Context\SessionContext
	{
		return new nomit\Security\Session\Context\SessionContext([
			'factory' => 'nomit\Security\Session\DependencyInjection\Factory\ContainerBuilderSessionFactory',
			'methods' => ['GET', 'POST'],
			'paths' => [['path' => '^/api/', 'enabled' => true], ['path' => '^/api/resource', 'enabled' => false]],
			'name' => '_nomit_security_session',
			'storage' => 'nomit\Security\Session\Storage\ArraySessionStorage',
			'session_fixation_strategy' => 'migrate',
			'secret' => 'gFjg3WV9sO0chayT9cRb8baq2j4MfNPJ',
			'token' => (object) [
				'persistence' => (object) [
					'service' => 'nomit\Security\Session\Token\Persistence\FileSystemTokenPersistence',
					'nomit\Security\Session\Token\Persistence\DatabaseTokenPersistence' => (object) [
						'table' => 'session_tokens',
					],
					'nomit\Security\Session\Token\Persistence\FileSystemTokenPersistence' => (object) [
						'directory' => '/Users/im.nomit/Sites/nomit.php/tmp/',
						'format' => 'json',
					],
				],
				'storage' => 'nomit\Security\Session\Token\Storage\CookieTokenStorage',
				'time_to_live' => 172800,
				'name' => 'nomit_session',
				'identity_generator' => 'nomit\Security\Session\Token\Identity\FingerprintIdentityGenerator',
			],
			'garbage' => (object) [
				'probability' => 2,
				'divisor' => 4,
			],
			'requests_count_limit' => 20,
			'fingerprint_generator' => 'nomit\Security\Session\Fingerprint\UserAgentFingerprintGenerator',
			'event_listeners' => [
				'closing' => 'nomit\Security\Session\EventListener\ClosingSessionEventListener',
				'invalidating' => 'nomit\Security\Session\EventListener\InvalidatingSessionEventListener',
				'opening' => 'nomit\Security\Session\EventListener\OpeningSessionEventListener',
				'regenerating' => 'nomit\Security\Session\EventListener\RegeneratingSessionEventListener',
			],
			'host' => null,
			'port' => 80,
		]);
	}


	public function createServiceSession__event_dispatcher__main(): nomit\EventDispatcher\LazyEventDispatcher
	{
		$service = new nomit\EventDispatcher\LazyEventDispatcher($this);
		$service->addLazySubscriber('kernel.response', 'session.event_listener.closing.main');
		$service->addLazySubscriber('nomit\Security\Session\Event\SavingSessionEvent', 'session.event_listener.closing.main');
		$service->addLazySubscriber('nomit\Security\Session\Event\ClosingSessionEvent', 'session.event_listener.closing.main');
		$service->addLazySubscriber('nomit\Security\Session\Event\ClosedSessionEvent', 'session.event_listener.closing.main');
		$service->addLazySubscriber(
			'nomit\Security\Session\Event\InvalidateSessionEvent',
			'session.event_listener.invalidating.main',
		);
		$service->addLazySubscriber('nomit\Security\Session\Event\OpeningSessionEvent', 'session.event_listener.opening.main');
		$service->addLazySubscriber('nomit\Security\Session\Event\OpenedSessionEvent', 'session.event_listener.opening.main');
		$service->addLazySubscriber(
			'nomit\Security\Session\Event\RegeneratingSessionEvent',
			'session.event_listener.regenerating.main',
		);
		$service->addLazySubscriber('kernel.response', 'authentication.remember_me.event_listener.response');
		return $service;
	}


	public function createServiceSession__event_listener__closing__main(): nomit\Security\Session\EventListener\ClosingSessionEventListener
	{
		return new nomit\Security\Session\EventListener\ClosingSessionEventListener(
			$this->getService('session.token.persistence.main'),
			$this->getService('session.token.storage.main'),
			$this->getService('logger.logger.main.security.session.firewall.main'),
		);
	}


	public function createServiceSession__event_listener__invalidating__main(): nomit\Security\Session\EventListener\InvalidatingSessionEventListener
	{
		return new nomit\Security\Session\EventListener\InvalidatingSessionEventListener($this->getService('logger.logger.main.security.session.firewall.main'));
	}


	public function createServiceSession__event_listener__opening__main(): nomit\Security\Session\EventListener\OpeningSessionEventListener
	{
		return new nomit\Security\Session\EventListener\OpeningSessionEventListener(
			$this->getService('session.token.validator.main'),
			$this->getService('session.event_dispatcher.main'),
			$this->getService('logger.logger.main.security.session.firewall.main'),
		);
	}


	public function createServiceSession__event_listener__regenerating__main(): nomit\Security\Session\EventListener\RegeneratingSessionEventListener
	{
		return new nomit\Security\Session\EventListener\RegeneratingSessionEventListener($this->getService('logger.logger.main.security.session.firewall.main'));
	}


	public function createServiceSession__factory(): nomit\Security\Session\SessionFactory
	{
		return new nomit\Security\Session\SessionFactory($this->getService('session.session.main'));
	}


	public function createServiceSession__fingerprint_generator__main(): nomit\Security\Session\Fingerprint\UserAgentFingerprintGenerator
	{
		return new nomit\Security\Session\Fingerprint\UserAgentFingerprintGenerator($this->getService('cryptography.hasher.sha256'));
	}


	public function createServiceSession__firewall__listener__exception__main(): nomit\Security\Session\Firewall\Listener\ExceptionFirewallListener
	{
		return new nomit\Security\Session\Firewall\Listener\ExceptionFirewallListener('main', $this->getService('logger.main'));
	}


	public function createServiceSession__firewall__listener__matcher__main(): nomit\Security\Session\Firewall\Listener\MatcherFirewallListener
	{
		return new nomit\Security\Session\Firewall\Listener\MatcherFirewallListener($this->getService('session.firewall.map.matcher.main'));
	}


	public function createServiceSession__firewall__main(): nomit\Security\Session\Firewall\Firewall
	{
		return new nomit\Security\Session\Firewall\Firewall(
			$this->getService('session.firewall.map.main'),
			$this->getService('session.event_dispatcher.main'),
			$this->getService('session.factory'),
			$this->getService('logger.logger.main.security.session'),
		);
	}


	public function createServiceSession__firewall__map__main(): nomit\Security\Session\Firewall\Map\FirewallMap
	{
		$service = new nomit\Security\Session\Firewall\Map\FirewallMap;
		$service->add(
			$this->getService('session.request_matcher.yQraWUf'),
			[$this->getService('session.firewall.listener.matcher.main')],
			$this->getService('session.firewall.listener.exception.main'),
		);
		return $service;
	}


	public function createServiceSession__firewall__map__matcher__main(): nomit\Security\Session\Firewall\Map\MatcherMap
	{
		return new nomit\Security\Session\Firewall\Map\MatcherMap;
	}


	public function createServiceSession__request_matcher__yQraWUf(): nomit\Web\Request\RequestMatcher
	{
		return new nomit\Web\Request\RequestMatcher(null, null, ['GET', 'POST'], null, [], null, 80);
	}


	public function createServiceSession__session__main(): nomit\Security\Session\Session
	{
		return new nomit\Security\Session\Session(
			false,
			$this->getService('session.context.main'),
			$this->getService('session.token.identity.main'),
			$this->getService('session.token.storage.main'),
			$this->getService('session.token.persistence.main'),
			$this->getService('session.fingerprint_generator.main'),
			'_nomit_security_session',
			'nomit_session',
			$this->getService('session.event_dispatcher.main'),
			$this->getService('logger.logger.main.security.session'),
		);
	}


	public function createServiceSession__token__identity__main(): nomit\Security\Session\Token\Identity\FingerprintIdentityGenerator
	{
		return new nomit\Security\Session\Token\Identity\FingerprintIdentityGenerator(
			$this->getService('session.fingerprint_generator.main'),
			$this->getService('cryptography.hasher.sha256'),
			$this->getService('cryptography.entropy_factory'),
		);
	}


	public function createServiceSession__token__persistence__filesystem(): nomit\Security\Session\Token\Persistence\FileSystemTokenPersistence
	{
		return new nomit\Security\Session\Token\Persistence\FileSystemTokenPersistence(
			$this->getService('filesystem'),
			$this->getService('serializer'),
			null,
			$this->getService('cryptography.encrypter'),
			$this->getService('cryptography.hasher'),
			$this->getService('lock.factory'),
			null,
			$this->getService('logger.logger.main.security.session.token.persistence'),
		);
	}


	public function createServiceSession__token__persistence__main(): nomit\Security\Session\Token\Persistence\FileSystemTokenPersistence
	{
		return new nomit\Security\Session\Token\Persistence\FileSystemTokenPersistence(
			$this->getService('filesystem.filesystem'),
			$this->getService('serializer.resolver'),
			'/Users/im.nomit/Sites/nomit.php/tmp/',
			$this->getService('cryptography.encrypter'),
			$this->getService('cryptography.hasher.sha256'),
			$this->getService('lock.factory'),
			'json',
			$this->getService('logger.logger.main.security.session.token.persistence'),
		);
	}


	public function createServiceSession__token__storage__main(): nomit\Security\Session\Token\Storage\CookieTokenStorage
	{
		return new nomit\Security\Session\Token\Storage\CookieTokenStorage(
			'nomit_session',
			$this->getService('session.token.persistence.main'),
		);
	}


	public function createServiceSession__token__validator__main(): nomit\Security\Session\Token\Validation\TokenValidator
	{
		return new nomit\Security\Session\Token\Validation\TokenValidator(
			$this->getService('session.context.main'),
			$this->getService('session.token.persistence.main'),
		);
	}


	public function createServiceTemplate__kernelTemplateFactory(): nomit\Template\Bridges\Kernel\KernelTemplateFactory
	{
		return new class ($this) implements nomit\Template\Bridges\Kernel\KernelTemplateFactory {
			private $container;


			public function __construct(Container_ad1a675328 $container)
			{
				$this->container = $container;
			}


			public function create(): nomit\Template\Engine
			{
				$service = new nomit\Template\Engine;
				$service->setTempDirectory('/Users/im.nomit/Sites/nomit.php/tmp//cache/template');
				$service->setAutoRefresh(true);
				$service->setStrictTypes(false);
				return $service;
			}
		};
	}


	public function createServiceTemplate__templateFactory(): nomit\Template\Bridges\Kernel\TemplateFactory
	{
		return new nomit\Template\Bridges\Kernel\TemplateFactory(
			$this->getService('template.kernelTemplateFactory'),
			$this->getService('web.request'),
			cache: $this->getService('cache.cache.application.application'),
			templateClass: null,
		);
	}


	public function createServiceToasting__event_listener__preset_envelopes(): nomit\Toasting\EventListener\PresetEnvelopesEventListener
	{
		return new nomit\Toasting\EventListener\PresetEnvelopesEventListener;
	}


	public function createServiceToasting__event_listener__remove_envelopes(): nomit\Toasting\EventListener\RemoveEnvelopesEventListener
	{
		return new nomit\Toasting\EventListener\RemoveEnvelopesEventListener;
	}


	public function createServiceToasting__event_listener__stamp_envelopes(): nomit\Toasting\EventListener\StampEnvelopesEventListener
	{
		return new nomit\Toasting\EventListener\StampEnvelopesEventListener;
	}


	public function createServiceToasting__event_listener__store_envelopes(): nomit\Toasting\EventListener\StoreEnvelopesEventListener
	{
		return new nomit\Toasting\EventListener\StoreEnvelopesEventListener;
	}


	public function createServiceToasting__event_listener__toasting(): nomit\Toasting\EventListener\ToastingEventListener
	{
		return new nomit\Toasting\EventListener\ToastingEventListener(
			$this->getService('toasting.toaster'),
			(object) [
				'path' => '/api/toasts',
				'bag' => 'session',
				'default_format' => 'json',
				'views' => [
					'json' => (object) [
						'service' => 'serialized',
						'serialized' => (object) [
							'format' => 'json',
						],
					],
				],
				'criteria' => [],
				'context' => [],
				'maximum_duration' => 10,
			],
			$this->getService('logger.main'),
		);
	}


	public function createServiceToasting__response__responder(): nomit\Toasting\Response\Responder
	{
		return new nomit\Toasting\Response\Responder(
			$this->getService('toasting.storage.manager'),
			$this->getService('event_dispatcher.dispatcher'),
			['json' => $this->getService('toasting.response.view.serialized')],
		);
	}


	public function createServiceToasting__response__view__array(): nomit\Toasting\Response\View\ArrayView
	{
		return new nomit\Toasting\Response\View\ArrayView;
	}


	public function createServiceToasting__response__view__serialized(): nomit\Toasting\Response\View\SerializedView
	{
		return new nomit\Toasting\Response\View\SerializedView($this->getService('serializer'), 'json');
	}


	public function createServiceToasting__storage__bag(): nomit\Toasting\Storage\BagStorage
	{
		return new nomit\Toasting\Storage\BagStorage(
			$this->getService('toasting.storage.bag.session'),
			$this->getService('event_dispatcher.dispatcher'),
			[],
		);
	}


	public function createServiceToasting__storage__bag__array(): nomit\Toasting\Storage\Bag\ArrayBag
	{
		return new nomit\Toasting\Storage\Bag\ArrayBag;
	}


	public function createServiceToasting__storage__bag__session(): nomit\Toasting\Storage\Bag\SessionBag
	{
		return new nomit\Toasting\Storage\Bag\SessionBag($this->getService('session.session.main'));
	}


	public function createServiceToasting__storage__manager(): nomit\Toasting\Storage\StorageManager
	{
		return new nomit\Toasting\Storage\StorageManager(
			$this->getService('toasting.storage.bag'),
			$this->getService('event_dispatcher.dispatcher'),
			[],
		);
	}


	public function createServiceToasting__toaster(): nomit\Toasting\Toaster
	{
		return new nomit\Toasting\Toaster(
			'json',
			$this->getService('toasting.response.responder'),
			$this->getService('toasting.storage.manager'),
		);
	}


	public function createServiceView__html(): nomit\View\HtmlView
	{
		return new nomit\View\HtmlView(true, $this);
	}


	public function createServiceWeb__event_listener__access_control(): nomit\Web\EventListener\AccessControlAllowedOriginHeaderEventListener
	{
		return new nomit\Web\EventListener\AccessControlAllowedOriginHeaderEventListener('*');
	}


	public function createServiceWeb__event_listener__response(): nomit\Kernel\EventListener\ResponseEventListener
	{
		return new nomit\Kernel\EventListener\ResponseEventListener('%kernel.charset%', true);
	}


	public function createServiceWeb__request(): nomit\Web\Request\RequestInterface
	{
		return nomit\Web\Request\RequestFactory::createFromGlobals();
	}


	public function createServiceWeb__request__factory(): nomit\Web\Request\RequestFactory
	{
		$service = new nomit\Web\Request\RequestFactory;
		$service->setProxy([]);
		return $service;
	}


	public function createServiceWeb__requests(): nomit\Web\Request\RequestStack
	{
		return new nomit\Web\Request\RequestStack;
	}


	public function createServiceWeb__response(): nomit\Web\Response\Response
	{
		$service = new nomit\Web\Response\Response;
		$service->cookie_secure = $this->getService('web.request')->isSecured();
		return $service;
	}


	public function initialize()
	{
		// web.
		(function () {
			$this->getService('web.requests')->push($this->getService('web.request'));
		})();
		nomit\Reflection\AnnotationsParser::setCache($this->getService('cache'));
		nomit\Reflection\AnnotationsParser::$autoRefresh = true;
	}
}

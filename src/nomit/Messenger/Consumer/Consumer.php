<?php

namespace nomit\Messenger\Consumer;

use nomit\Dumper\Dumper;
use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Messenger\Envelope\Envelope;
use nomit\Messenger\Envelope\EnvelopeInterface;
use nomit\Messenger\Event\ConsumerEvent;
use nomit\Messenger\Event\MessengerEvents;
use nomit\Messenger\Event\PingEvent;
use nomit\Messenger\Event\RejectedEnvelopeEvent;
use nomit\Messenger\Event\StatusEvent;
use nomit\Messenger\Exception\BadMethodCallException;
use nomit\Messenger\Message\MessageInterface;
use nomit\Messenger\Queue\QueueInterface;
use nomit\Messenger\Router\RouterInterface;
use nomit\Messenger\Stamp\AcknowledgedStamp;
use nomit\Messenger\Stamp\DelayStamp;
use nomit\Messenger\Stamp\RejectedStamp;
use nomit\Messenger\Worker\WorkerInterface;
use nomit\Work\CallbackInterface;
use Psr\Log\LoggerInterface;

class Consumer implements ConsumerInterface
{

    protected array $options = [
        'max_runtime' => PHP_INT_MAX,
        'max_attempts' => null,
        'stop_when_empty' => true,
        'catch_exceptions' => false,
    ];

    protected ?CallbackInterface $process = null;

    protected bool $running = false;

    protected bool $started = false;

    public function __construct(
        protected WorkerInterface $worker,
        protected RouterInterface $router,
        array $options = [],
        protected ?EventDispatcherInterface $dispatcher = null,
        protected ?LoggerInterface $logger = null
    )
    {
        $this->options = array_merge($this->options, $options);
    }

    public function supports(mixed $hook): bool
    {
        return $this->router->has($hook);
    }

    public function consume(QueueInterface $queue, array $options = []): void
    {
        $options = array_merge($this->options, $options);

        try {
            $this->start();

            while($this->running) {
                $this->dispatcher->dispatch(new PingEvent($queue), MessengerEvents::EVENT_ITERATE);

                $envelope = $queue->dequeue();

                if($envelope === null && $options['stop_when_empty']) {
                    $this->stop();
                }

                if(!$this->running) {
                    return;
                }

                $this->run($envelope, $queue);
            }
        } finally {
            $this->end();
        }
    }

    public function stop(bool $timedOut = false): void
    {
        $this->running = false;

        if($this->process) {
            $this->process->stop($timedOut);
        }
    }

    protected function start(): void
    {
        declare(ticks=1);

        $this->bind();

        $this->dispatcher->dispatch(new StatusEvent($this), MessengerEvents::EVENT_START);

        $this->started = true;
        $this->running = true;
    }

    protected function bind(): void
    {
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGTERM, [$this, 'end']);
            pcntl_signal(SIGINT, [$this, 'end']);
            pcntl_signal(SIGQUIT, [$this, 'end']);
            pcntl_signal(SIGUSR2, [$this, 'pause']);
            pcntl_signal(SIGCONT, [$this, 'resume']);
        }
    }

    public function run(EnvelopeInterface|MessageInterface $envelope, QueueInterface $queue): void
    {
        if($envelope instanceof MessageInterface) {
            $envelope = new Envelope($envelope);
        }

        $handlers = $this->router->resolve($envelope);
        $process = $this->process = $this->worker->build($envelope, $handlers);

        try {
            $this->dispatcher->dispatch(new ConsumerEvent($envelope, $queue, $this), MessengerEvents::EVENT_RUN);

            if($envelope->all(DelayStamp::class)) {
                $delayStamp = $envelope->last(DelayStamp::class);
                $delay = $delayStamp->getDelay();

                $process->wait($delay);
            }

            $this->worker->run($process);
        } catch(\Throwable $error) {
            $this->reject($error, $envelope, $queue);
        } finally {
            if(!$process->isRunning() || $process->isStopped()) {
                $this->running = false;
            }

            $queue->acknowledge($envelope);

            $envelope->stamp(new AcknowledgedStamp());

            $this->dispatcher->dispatch(new ConsumerEvent($envelope, $queue, $this), MessengerEvents::EVENT_ACKNOWLEDGE);
        }
    }

    protected function end(): void
    {
        $this->running = false;
        $this->started = false;

        $this->dispatcher?->dispatch(new StatusEvent($this), MessengerEvents::EVENT_TERMINATE);
    }

    protected function configure(CallbackInterface $process): void
    {
        $process->setMaximumRuntime($this->options['max_runtime']);
        $process->setMaximumAttempts($this->options['max_attempts']);
        $process->catch($this->options['catch_exceptions']);
    }

    public function pause(): self
    {
        $this->requiresProcess(__METHOD__);

        $this->dispatcher?->dispatch(new StatusEvent($this), MessengerEvents::EVENT_PAUSE);

        $this->process->pause();

        return $this;
    }

    public function resume(): self
    {
        $this->requiresProcess(__METHOD__);

        $this->dispatcher?->dispatch(new StatusEvent($this), MessengerEvents::EVENT_RESUME);

        $this->process->resume();

        return $this;
    }

    public function wait(int $timeout): self
    {
        $this->requiresProcess(__METHOD__);

        $this->dispatcher?->dispatch(new StatusEvent($this), MessengerEvents::EVENT_WAIT);

        $this->process->wait($timeout);

        return $this;
    }

    public function reject(\Throwable $throwable, EnvelopeInterface $envelope, QueueInterface $queue): void
    {
        $envelope->stamp(new RejectedStamp(get_class($throwable), $throwable->getCode(), $throwable->getMessage(), FlattenedException::createFromThrowable($throwable)));

        $this->dispatcher?->dispatch(new RejectedEnvelopeEvent($envelope, $queue, $throwable), MessengerEvents::EVENT_REJECT);
    }

    protected function requiresProcess(string $caller): void
    {
        if(!$this->process instanceof CallbackInterface) {
            throw new BadMethodCallException(sprintf('The "%s" method of the "%s" consumer object cannot be called if a process has not been previously assigned to it.', $caller, __CLASS__));
        }
    }

    public function isRunning(): bool
    {
        $this->requiresProcess(__METHOD__);

        return $this->process->isRunning();
    }

    public function isPaused(): bool
    {
        $this->requiresProcess(__METHOD__);

        return $this->process->isPaused();
    }

    public function isWaiting(): bool
    {
        $this->requiresProcess(__METHOD__);

        return $this->process->isWaiting();
    }

    public function isSuccessful(): bool
    {
        $this->requiresProcess(__METHOD__);

        return $this->process->isSuccessful();
    }

    public function isFailure(): bool
    {
        $this->requiresProcess(__METHOD__);

        return $this->process->isFailure();
    }

    public function isShutdown(): bool
    {
        $this->requiresProcess(__METHOD__);

        return $this->process->isShutdown();
    }

}
<?php

namespace nomit\Work;

use nomit\Dumper\Dumper;
use nomit\Utility\Bag\BagInterface;
use nomit\EventDispatcher\EventDispatcherInterface;
use nomit\Work\Event\AttemptEvent;
use nomit\Work\Event\ExceptionEvent;
use nomit\Work\Event\PauseEvent;
use nomit\Work\Event\WorkerEvent;
use nomit\Work\Event\ProcessEvents;
use nomit\Work\Event\ResultEvent;
use nomit\Work\Event\RunEvent;
use nomit\Work\Event\StartEvent;
use nomit\Work\Event\StopEvent;
use nomit\Work\Event\ThrowableEvent;
use nomit\Work\Event\WaitEvent;
use nomit\Work\Exception\BadMethodCallException;
use nomit\Work\Exception\ExceptionInterface;
use nomit\Work\Exception\InvalidArgumentException;
use nomit\Work\Job\JobInterface;
use nomit\Work\Results\Results;
use nomit\Work\Results\ResultsInterface;
use nomit\Stream\StreamFactory;
use nomit\Stream\StreamFactoryInterface;
use nomit\Utility\Bag\Bag;
use Psr\Http\Message\StreamInterface;

class Callback implements CallbackInterface
{

    protected int $status = 0;

    protected int $start_time = 0;

    protected int $stop_time = 0;

    protected bool $opened = false;

    protected bool $started = false;

    protected bool $running = false;

    protected bool $stopped = false;

    protected bool $ended = false;

    protected int $maximum_runtime = PHP_INT_MAX;

    protected int $attempts = 0;

    protected int $maximum_attempts = 5;

    protected int $run_time = 0;

    protected int $running_time = 0;

    protected bool $paused = false;

    protected int $pause_time = 0;

    protected int $pause_timeout = 10000;

    protected bool $waiting = false;

    protected int $waiting_timeout = 0;

    protected int $wait_time = 0;

    protected int $waiting_time = 0;

    protected int $waited_time = 0;

    protected int $timeout = 0;

    protected bool $timed_out = false;

    protected int $writing_time = 0;

    protected bool $blocked = false;

    protected StreamFactoryInterface $stream_factory;

    protected StreamInterface $input;

    protected StreamInterface $output;

    protected StreamInterface $error;

    protected BagInterface $arguments;

    protected ResultsInterface $results;

    protected BagInterface $exceptions;

    protected ?JobInterface $job = null;

    protected ?EventDispatcherInterface $dispatcher;

    protected $callback;

    protected bool $catch = false;

    public static function isStatus(CallbackInterface $process, int $status): bool
    {
        return $status === $process->getStatus() & $status;
    }

    public function __construct(JobInterface $job, EventDispatcherInterface $dispatcher = null)
    {
        $this->stream_factory = new StreamFactory();
        $this->dispatcher = $dispatcher;

        $this->setJob($job);
    }

    public function __sleep(): array
    {
        throw new BadMethodCallException(sprintf('The "%s" process object cannot be serialized.', __CLASS__));
    }

    public function __wakeup()
    {
        throw new BadMethodCallException(sprintf('The "%s" process object cannot be unserialized.', __CLASS__));
    }

    public function __destruct()
    {
        if($this->isStarted()) {
            if($this->isRunning()) {
                $this->stop();
            }

            $this->end();
        }

        $this->reset();
    }

    protected function open(): void
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The callback "%s" cannot be opened while running.', __CLASS__));
        }

        if($this->opened) {
            return;
        }

        $this->setStream(fopen('php://stdin', 'r'), self::STREAM_TO_INPUT);
        $this->setStream(fopen('php://stdout', 'w'), self::STREAM_TO_OUTPUT);
        $this->setStream(fopen('php://stderr', 'w'), self::STREAM_TO_ERROR);

        $this->opened = true;
    }

    public function setStream($resource = null, string $direction = self::STREAM_TO_INPUT): self
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The streams of the callback "%s" cannot be modified while running.', __CLASS__));
        }

        if($resource === null) {
            $stream = $this->stream_factory->createStream();
        } else if(is_resource($resource)) {
            $stream = $this->stream_factory->createStreamFromResource($resource);
        } else if(is_file($resource)) {
            $stream = $this->stream_factory->createStreamFromFile($resource);
        } else {
            throw new InvalidArgumentException(sprintf('When setting a worker\'s stream, the passed resource must be of a resource or string filename type: instead, a "%s"-typed value was supplied.', get_debug_type($resource)));
        }

        switch($direction) {
            case self::STREAM_TO_INPUT:
                $this->input = $stream;
                break;

            case self::STREAM_TO_OUTPUT:
                $this->output = $stream;
                break;

            case self::STREAM_TO_ERROR:
                $this->error = $stream;
                break;

            default:
                throw new InvalidArgumentException(sprintf('The supplied stream type, "%s", is invalid.', $direction));
        }

        return $this;
    }

    protected function close(): void
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The callback "%s" cannot be closed while running.', __CLASS__));
        }

        if(!$this->opened) {
            return;
        }

        foreach([$this->input, $this->output, $this->error] as $stream) {
            $stream->close();
        }

        $this->opened = false;
    }

    protected function reopen(): void
    {
        if($this->isOpened()) {
            $this->close();
            $this->open();
        }
    }

    public function block(): void
    {
        if($this->isBlocked()) {
            return;
        }

        foreach([$this->input, $this->output, $this->error] as $stream) {
            stream_set_blocking($stream, true);
        }

        $this->blocked = true;
    }

    public function unblock(): void
    {
        if(!$this->isBlocked()) {
            return;
        }

        foreach([$this->input, $this->output, $this->error] as $stream) {
            stream_set_blocking($stream, false);
        }

        $this->blocked = false;
    }

    protected function reset(JobInterface $job = null): void
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The callback "%s" cannot be reset while running.', __CLASS__));
        }

        $this->status = 0;
        $this->start_time = 0;
        $this->stopped = false;
        $this->stop_time = 0;
        $this->opened = false;
        $this->started = false;
        $this->running = false;
        $this->ended = false;
        $this->attempts = 0;
        $this->maximum_attempts = 5;
        $this->maximum_runtime = PHP_INT_MAX;
        $this->run_time = 0;
        $this->running_time = 0;
        $this->paused = false;
        $this->pause_time = 0;
        $this->pause_timeout = 10000;
        $this->waiting = false;
        $this->waiting_timeout = 0;
        $this->wait_time = 0;
        $this->waited_time = 0;
        $this->waiting_time = 0;
        $this->timeout = 0;
        $this->timed_out = false;
        $this->writing_time = 0;
        $this->arguments = new Bag();
        $this->results = new Results($this);
        $this->exceptions = new Bag();

        $this->reopen();

        if($job !== null) {
            $this->setJob($job);
        }
    }

    protected function setJob(JobInterface $job): self
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The callback "%s" cannot have its job set while running.', __CLASS__));
        }

        $this->job = $job;

        return $this;
    }

    public function getJob(): JobInterface
    {
        return $this->job;
    }

    protected function start(): void
    {
        if($this->isStarted() || $this->isRunning()) {
            throw new BadMethodCallException(sprintf('The callback "%s" cannot be started, because it has already been started and/or is running.', __CLASS__));
        }

        $this->reset();

        if(!$this->isOpened()) {
            $this->open();
        } else {
            $this->reopen();
        }

        $this->started = true;
        $this->start_time = microtime(true);
        $this->running = true;

        $this->dispatcher?->dispatch(new StartEvent($this, $this->start_time), ProcessEvents::START);
    }

    public function run(callable $callback = null): int
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The callback "%s" is already running.', __CLASS__));
        }

        $this->run_time = microtime(true);

        try {
            $this->start();

            $callback = $this->normalizeCallback($callback);

            $this->process($callback);
        } catch(\Throwable $throwable) {
            $this->dispatcher?->dispatch(new ThrowableEvent($this, $throwable), ProcessEvents::THROWABLE);

            if(!$this->catch) {
                throw $throwable;
            }
        } finally {
            $this->end();
        }

        return $this->status;
    }

    protected function normalizeCallback(callable $callback = null): ?callable
    {
        if($callback === null) {
            return null;
        }

        return function(mixed $result) use($callback): mixed
        {
            return $callback($result);
        };
    }

    protected function process(?callable $callback): int
    {
        if(!$this->isRunning()) {
            throw new BadMethodCallException(sprintf('The "%s" callback cannot be processed if it is not running.', __CLASS__));
        }

        $arguments = $this->getArguments();

        $this->dispatcher?->dispatch(new RunEvent($this, $this->run_time, $this->job, $arguments), ProcessEvents::RUN);

        while($this->isRunning()) {
            $this->write(1);
            if($this->isPaused()) {
                if(microtime(true) > $this->pause_time + $this->pause_timeout) {
                    $this->resume();
                } else {
                    usleep(1000);

                    continue;
                }
            }

            if(($this->attempts > $this->maximum_attempts)
                || (($this->running = microtime(true) - $this->run_time) > $this->maximum_runtime)
            ){
                $this->stop(true);
            }

            $this->waiting();

            if(!$this->isStopped() && ($this->isSuccessful() || $this->isFailure())) {
                $this->stop();
            }

            if($this->isStopped()) {
                break;
            }

            $result = null;

            try {
                $this->dispatcher?->dispatch(new AttemptEvent($this, microtime(true)), ProcessEvents::ATTEMPTING);

                $result = $this->handle($this->job, ...$arguments);

                if($callback !== null) {
                    $result = $callback($result);
                }

                $this->dispatcher?->dispatch(new ResultEvent($this, $result), ProcessEvents::RESULT);
            } catch(ExceptionInterface $exception) {
                $this->throw($exception);

                break;
            } finally {
                $this->attempts++;
            }
            
            if($result !== null) {
                $this->results->push($result);
            }
        }

        return $this->status;
    }

    protected function handle(JobInterface $job, ...$arguments): mixed
    {
        return $job->run($this, ...$arguments);
    }

    public function pause(): self
    {
        if($this->isStopped()) {
            throw new BadMethodCallException(sprintf('The "%s" process cannot be paused as it has already been stopped.', __CLASS__));
        }

        $this->pause_time = microtime(true);
        $this->paused = true;

        $this->dispatcher?->dispatch(new PauseEvent($this, $this->pause_time, $this->pause_timeout), ProcessEvents::PAUSE);

        return $this;
    }

    public function resume(): self
    {
        if(!$this->isStopped()) {
            throw new BadMethodCallException(sprintf('The "%s" process cannot be resumed because it has already been stopped.', __CLASS__));
        }

        $this->pause_time = 0;
        $this->paused = false;

        return $this;
    }

    public function wait(int $timeout): void
    {
        if($this->isStopped()) {
            throw new BadMethodCallException(sprintf('The "%s" process cannot wait when it has been previously stopped.', __CLASS__));
        }

        $this->waiting_timeout = $timeout;
        $this->wait_time = microtime(true);
        $this->waiting_time = $this->wait_time + $this->waiting_timeout;
        $this->waiting = true;

        $this->dispatcher?->dispatch(new WaitEvent($this, $this->waiting_timeout), ProcessEvents::WAIT);
    }

    protected function waiting(): void
    {
        if(!$this->isRunning() || $this->isStopped()) {
            return;
        }

        while($this->waiting) {
            if($this->wait_time < $this->waiting_time) {
                $this->waiting = false;

                break;
            }

            $this->waited_time = $this->wait_time - microtime(true);

            usleep(1000);
        }
    }

    public function stop(bool $timedOut = false): void
    {
        if(!$this->isRunning() || $this->isStopped()) {
            return;
        }

        if($timedOut) {
            $this->timed_out = true;
        }

        $this->stop_time = microtime(true);

        $this->dispatcher?->dispatch(new StopEvent($this, $this->run_time, $this->stop_time), ProcessEvents::STOP);

        $this->stopped = true;
        $this->running = false;
    }

    protected function end(): void
    {
        if(!$this->isStarted()) {
            return;
        }

        if($this->isRunning()) {
            $this->stop();
        }

        $this->dispatcher?->dispatch(new WorkerEvent($this), ProcessEvents::END);

        $this->close();

        $this->started = false;
        $this->ended = true;
    }

    public function succeed(): self
    {
        if(!$this->isStarted()) {
            throw new BadMethodCallException(sprintf('The callback "%s" cannot be marked as having succeeded if it has not been previously started.', __CLASS__));
        }

        $this->stop();

        if(!$this->isSuccessful()) {
            $this->addStatus(self::STATUS_SUCCESS);
        }

        if($this->isFailure()) {
            $this->removeStatus(self::STATUS_FAILURE);
        }

        $this->dispatcher?->dispatch(new WorkerEvent($this), ProcessEvents::SUCCESS);

        return $this;
    }

    public function fail(): self
    {
        if(!$this->isStarted()) {
            throw new BadMethodCallException(sprintf('The callback "%s" cannot be marked as having failed if it has not been previously started.', __CLASS__));
        }

        $this->stop();

        if(!$this->isFailure()) {
            $this->addStatus(self::STATUS_FAILURE);
        }

        if($this->isSuccessful()) {
            $this->removeStatus(self::STATUS_SUCCESS);
        }

        $this->dispatcher?->dispatch(new WorkerEvent($this), ProcessEvents::FAILURE);

        return $this;
    }

    protected function throw(ExceptionInterface $exception): void
    {
        $this->stop();

        $this->status &= self::STATUS_FAILURE;

        $this->addException($exception);

        $this->dispatcher?->dispatch(new ExceptionEvent($this, $exception), ProcessEvents::EXCEPTION);

        while(($throwable = $exception->getPrevious()) !== null) {
            $this->addException($throwable);
        }
    }

    public function read(int $maximumLength = null): string
    {
        if(!$this->isOpened()) {
            throw new BadMethodCallException(sprintf('The "%s" process cannot read from the input stream because it has been previously closed.', __CLASS__));
        }

        $buffer = '';
        $size = 0;

        while (!$this->input->eof()) {
            // Using a loose equality here to match on '' and false.
            if (null === ($byte = $this->input->read(1))) {
                return $buffer;
            }

            $buffer .= $byte;

            // Break when a new line is found or the max length - 1 is reached
            if ($byte === PHP_EOL || ++$size === $maximumLength - 1) {
                break;
            }
        }

        return $buffer;
    }

    public function write(string $message): void
    {
        if(!$this->isOpened()) {
            throw new BadMethodCallException(sprintf('The "%s" process cannot write to the output stream because it is not open.', __CLASS__));
        }

        $this->output->write($message);
    }

    public function error(string|\Throwable $message): void
    {
        if(!$this->isOpened()) {
            throw new BadMethodCallException(sprintf('The "%s" process cannot write to the error stream because it has been previously closed.', __CLASS__));
        }

        if($message instanceof \Throwable) {
            $message = $message->getMessage();
        }

        $this->error->write($message);
    }

    public function isTimedOut(): bool
    {
        return $this->timed_out;
    }

    public function isEnded(): bool
    {
        return $this->ended;
    }

    public function isStopped(): bool
    {
        return $this->stopped;
    }

    public function isOpened(): bool
    {
        return $this->opened;
    }

    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function isReady(): bool
    {
        return !$this->isStarted()
            && !$this->isRunning()
            && !$this->isPaused()
            && !$this->isWaiting()
            && !$this->isShutdown();
    }

    public function isRunning(): bool
    {
        return $this->running;
    }

    public function isPaused(): bool
    {
        return $this->paused;
    }

    public function isWaiting(): bool
    {
        return $this->waiting;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function addStatus(int $status): self
    {
        $this->status &= $status;

        return $this;
    }

    public function removeStatus(int $status): self
    {
        $this->status &= ~$status;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function hasStatus(int $status): bool
    {
        return $status === ($this->getStatus() & $status);
    }

    public function isShutdown(): bool
    {
        return $this->isSuccessful() || $this->isFailure();
    }

    public function isSuccessful(): bool
    {
        return $this->hasStatus(self::STATUS_SUCCESS);
    }

    public function isFailure(): bool
    {
        return $this->hasStatus(self::STATUS_FAILURE);
    }

    public function getStartTime(): int
    {
        return $this->start_time;
    }

    public function getRunTime(): int
    {
        return $this->run_time;
    }

    public function getStopTime(): int
    {
        return $this->stop_time;
    }

    public function getRunDuration(): int
    {
        return $this->stop_time - $this->run_time;
    }

    public function setArguments(array $arguments): self
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The arguments of the callback "%s" cannot be modified while running.', __CLASS__));
        }

        $this->arguments->setAll($arguments);

        return $this;
    }

    public function setArgument(int $index, mixed $argument): self
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The arguments of the callback "%s" cannot be modified while running.', __CLASS__));
        }

        $this->arguments->set($index, $argument);

        return $this;
    }

    public function getArgument(int $index): mixed
    {
        return $this->arguments->get($index);
    }

    public function hasArgument(int $index): bool
    {
        return $this->arguments->has($index);
    }

    public function pushArgument(mixed $argument): self
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The arguments of the callback "%s" cannot be modified while running.', __CLASS__));
        }

        $this->arguments->push($argument);

        return $this;
    }

    public function unshiftArgument(mixed $argument): self
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The arguments of the callback "%s" cannot be modified while running.', __CLASS__));
        }

        $this->arguments->unshift($argument);

        return $this;
    }

    public function popArgument(): mixed
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The arguments of the callback "%s" cannot be modified while running.', __CLASS__));
        }

        return $this->arguments->pop();
    }

    public function shiftArgument(): mixed
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The arguments of the callback "%s" cannot be modified while running.', __CLASS__));
        }

        return $this->arguments->shift();
    }

    public function getArguments(): array
    {
        return $this->arguments->all();
    }

    protected function push(mixed $result): self
    {
        $this->results->push($result);

        return $this;
    }

    protected function unshift(mixed $result): self
    {
        $this->results->unshift($result);

        return $this;
    }

    public function pop(): mixed
    {
        return $this->results->pop();
    }

    public function shift(): mixed
    {
        return $this->results->shift();
    }

    public function first(): mixed
    {
        return $this->results->first();
    }

    public function last(): mixed
    {
        return $this->results->last();
    }

    public function current()
    {
        return $this->results->current();
    }

    public function next()
    {
        $this->results->next();
    }

    public function key()
    {
        return $this->results->key();
    }

    public function valid()
    {
        return $this->results->valid();
    }

    public function rewind()
    {
        $this->results->rewind();
    }

    public function count()
    {
        return $this->results->count();
    }

    public function all(): array
    {
        return $this->results->all();
    }

    public function getResults(): ResultsInterface
    {
        return $this->results;
    }

    public function setMaximumAttempts(int $attempts): self
    {
        if($this->isRunning()) {
            throw new BadMethodCallException(sprintf('The callback "%s" cannot have its maximum attempts count modified while running.', __CLASS__));
        }

        $this->maximum_attempts = $attempts;

        return $this;
    }

    public function getAttemptsCount(): int
    {
        return $this->attempts;
    }

    public function setMaximumRuntime(int $runtime): self
    {
        $this->maximum_runtime = $runtime;

        return $this;
    }

    public function catch(bool $catch = null): self
    {
        if($catch !== null) {
            $this->catch = $catch;
        } else {
            $this->catch = !$this->catch;
        }

        return $this;
    }

    protected function addException(\Throwable $exception): void
    {
        $this->exceptions->add($exception);
    }

    public function hasExceptions(): bool
    {
        return $this->exceptions->count() > 0;
    }

    public function allExceptions(): array
    {
        return $this->exceptions->all();
    }

    public function getExceptions(): BagInterface
    {
        return $this->exceptions;
    }

    public function popException(): ?\Throwable
    {
        return $this->exceptions->pop();
    }

    public function shiftException(): ?\Throwable
    {
        return $this->exceptions->shift();
    }

    public function getIterator()
    {
        return $this->results;
    }

}
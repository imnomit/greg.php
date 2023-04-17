<?php

namespace nomit\Error\Frame;

use nomit\Dumper\Dumper;
use nomit\Error\Solution\Solver;
use nomit\Error\Utilities\ErrorCodeUtilities;
use nomit\Kernel\Exception\HttpExceptionInterface;
use nomit\Web\Exception\RequestExceptionInterface;
use nomit\Web\Response\Response;

class Inspector implements InspectorInterface
{

    private ?FrameCollectionInterface $frames = null;

    private ?InspectorInterface $previousExceptionInspector = null;

    private \Throwable $exception;

    private string $message;

    private string|int $code;

    private string $class;

    private string $file;

    private int $line;

    private ?array $trace = null;

    private int $statusCode;

    private string $statusText;

    private array $headers = [];

    private Solver $solver;

    public static function create(\Throwable $exception, int $statusCode = null, array $headers = []): self
    {
        $instance = new self($exception);

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $headers = array_merge($headers, $exception->getHeaders());
        } else if ($exception instanceof RequestExceptionInterface) {
            $statusCode = 400;
        }

        if (null === $statusCode) {
            $statusCode = 500;
        }

        if (class_exists(Response::class) && isset(Response::$statusTexts[$statusCode])) {
            $statusText = Response::$statusTexts[$statusCode];
        } else {
            $statusText = 'Uh oh, it looks like something went wrong here.';
        }

        $instance->setStatusText($statusText);
        $instance->setStatusCode($statusCode);
        $instance->setHeaders($headers);

        return $instance;
    }

    public function __construct(
        \Throwable $exception,
        private bool $debug = false,
        Solver $solver = null
    )
    {
        $this->exception = $exception;
        $this->solver = $solver ?? new Solver();

        $this->setMessage($exception->getMessage());
        $this->setCode($exception->getCode());
        $this->setClass(get_debug_type($exception));
        $this->setFile($exception->getFile());
        $this->setLine($exception->getLine());
    }

    public function setDebug(bool $debug = true): self
    {
        $this->debug = $debug;

        return $this;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @return \Throwable
     */
    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function hasPrevious(): bool
    {
        return $this->previousExceptionInspector || $this->exception->getPrevious();
    }

    public function getPrevious(): ?InspectorInterface
    {
        if ($this->previousExceptionInspector === null) {
            $previousException = $this->exception->getPrevious();

            if ($previousException) {
                $this->previousExceptionInspector = new Inspector($previousException);
            }
        }

        return $this->previousExceptionInspector;
    }

    public function getAllPrevious(): array
    {
        $exceptions = [];
        $exception = $this;

        while($exception = $exception->getPrevious()) {
            $exceptions[] = $exception;
        }

        return $exceptions;
    }

    public function getFrames(): FrameCollectionInterface
    {
        if ($this->frames === null) {
            $frames = $this->exception->getTrace();

            // If we're handling an \ErrorException thrown by BooBoo,
            // get rid of the last frame, which matches the handleError method,
            // and do not add the current exception to trace. We ensure that
            // the next frame does have a filename / linenumber, though.
            if ($this->exception instanceof \ErrorException) {
                foreach ($frames as $k => $frame) {
                    if (isset($frame['class']) &&
                        strpos($frame['class'], 'nomit\\Error') !== false
                    ) {
                        unset($frames[$k]);
                    }
                }
            }

            $this->frames = new FrameCollection($frames);

            if ($previousInspector = $this->getPrevious()) {
                // Keep outer frame on top of the inner one
                $outerFrames = $this->frames;
                $newFrames = clone $previousInspector->getFrames();
                $newFrames->prependFrames($outerFrames->getDifference($newFrames));

                $this->frames = $newFrames;
            }
        }

        return $this->frames;
    }

    public function hasFrames(): bool
    {
        $frames = $this->getFrames();

        return count($frames) > 0;
    }

    public function setClass(string $class): static
    {
        $this->class = str_contains($class, "@anonymous\0") ? (get_parent_class($class) ?: key(class_implements($class)) ?: 'class').'@anonymous' : $class;

        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setFile(string $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setLine(int $line): static
    {
        $this->line = $line;

        return $this;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function setMessage(string $message): static
    {
        if (str_contains($message, "@anonymous\0")) {
            $message = preg_replace_callback('/[a-zA-Z_\x7f-\xff][\\\\a-zA-Z0-9_\x7f-\xff]*+@anonymous\x00.*?\.php(?:0x?|:[0-9]++\$)[0-9a-fA-F]++/', function ($m) {
                return class_exists($m[0], false) ? (get_parent_class($m[0]) ?: key(class_implements($m[0])) ?: 'class').'@anonymous' : $m[0];
            }, $message);
        }

        $this->message = $message;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setCode(int|string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getCode(): int|string
    {
        return $this->code;
    }

    public function setTrace(FrameCollectionInterface|array $frames): self
    {
        $this->trace = [];

        if($frames instanceof FrameCollectionInterface) {
            $frames = $frames->toArray();
        }

        /**
         * @var FrameInterface $frame
         */
        foreach($frames as $index => $frame) {
            $class = '';
            $namespace = '';

            if($className = $frame->getClass()) {
                $parts = explode('\\', $className);
                $class = array_pop($parts);
                $namespace = implode('\\', $parts);
            }

            $this->trace[] = [
                'namespace' => $namespace,
                'short_class' => $class,
                'class' => $frame->getClass(),
                'function' => $frame->getFunction(),
                'file' => $frame->getFile(),
                'line' => $frame->getLine(),
                'arguments' => $frame->getArguments(),
                'excerpt' => $frame->getFileContents(),
                'index' => $index
            ];
        }

        return $this;
    }

    public function getTrace(): array
    {
        if($this->trace === null) {
            $this->setTrace($this->getFrames());
        }

        return $this->trace;
    }

    public function setStatusCode(int $code): static
    {
        $this->statusCode = $code;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusText(string $statusText): static
    {
        $this->statusText = $statusText;

        return $this;
    }

    public function getStatusText(): string
    {
        return $this->statusText;
    }

    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function toArray(): array
    {
        $exceptions = [];

        foreach (array_merge([$this], $this->getAllPrevious()) as $inspector) {
            if($this->debug) {
                $solutions = [];

                foreach($this->solver->getSolutions($inspector->getException()) as $subSolutions) {
                    foreach($subSolutions as $solution) {
                        $solutions[] = [
                            'title' => $solution->getSolutionTitle(),
                            'description' => $solution->getSolutionDescription(),
                        ];
                    }
                }

                $exceptions[] = [
                    'message' => $inspector->getMessage(),
                    'class' => $inspector->getClass(),
                    'trace' => $inspector->getTrace(),
                    'file' => $inspector->getFile(),
                    'line' => $inspector->getLine(),
                    'solutions' => $solutions,
                    'type' => ErrorCodeUtilities::getType($inspector->getCode()),
                    'description' => ErrorCodeUtilities::getDescription($inspector->getCode()),
                    'fatal' => ErrorCodeUtilities::isFatal($inspector->getCode()),
                    'timestamp' => microtime(),
                    'color' => ErrorCodeUtilities::getTagColor($inspector->getCode()),
                    'level' => ErrorCodeUtilities::getLevel($inspector->getCode()),
                ];
            } else {
                $exceptions[] = [
                    'message' => $inspector->getMessage(),
                    'class' => $inspector->getClass(),
                    'type' => ErrorCodeUtilities::getType($inspector->getCode()),
                    'description' => ErrorCodeUtilities::getDescription($inspector->getCode()),
                    'fatal' => ErrorCodeUtilities::isFatal($inspector->getCode()),
                    'timestamp' => microtime(),
                    'color' => ErrorCodeUtilities::getTagColor($inspector->getCode()),
                    'level' => ErrorCodeUtilities::getLevel($inspector->getCode()),
                ];
            }
        }

        return $exceptions;
    }

    public function __toArray(): array
    {
        return $this->toArray();
    }

    public function toString(): string
    {
        $message = '';
        $next = false;

        foreach (array_reverse(array_merge([$this], $this->getAllPrevious())) as $exception) {
            if ($next) {
                $message .= 'Next ';
            } else {
                $next = true;
            }

            $message .= $exception->getClass();

            if ('' != $exception->getMessage()) {
                $message .= ': '.$exception->getMessage();
            }

            $message .= ' in '.$exception->getFile().':'.$exception->getLine().
                "\nStack trace:\n".$exception->getTraceAsString()."\n\n";
        }

        return rtrim($message);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

}
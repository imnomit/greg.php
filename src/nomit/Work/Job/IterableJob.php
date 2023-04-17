<?php

namespace nomit\Work\Job;

use nomit\Utility\Bag\BagInterface;
use nomit\Work\CallbackInterface;
use nomit\Work\Results\Results;
use nomit\Work\Results\ResultsInterface;
use nomit\Utility\Bag\Bag;

class IterableJob implements IterableJobInterface
{

    protected BagInterface $jobs;

    public function __construct(iterable $jobs)
    {
        $this->jobs = new Bag();

        $this->pushAll($jobs);
    }

    public function pushAll(iterable $jobs): self
    {
        foreach($jobs as $job) {
            $this->push($job);
        }

        return $this;
    }

    public function set(int $index, JobInterface $job): self
    {
        $this->jobs->set($index, $job);

        return $this;
    }

    public function push(JobInterface $job): self
    {
        $this->jobs->push($job);

        return $this;
    }

    public function unshift(JobInterface $job): self
    {
        $this->jobs->unshift($job);

        return $this;
    }

    public function pop(): ?JobInterface
    {
        return $this->jobs->pop();
    }

    public function shift(): ?JobInterface
    {
        return $this->jobs->shift();
    }

    public function run(CallbackInterface $process, ...$arguments): ResultsInterface
    {
        $results = new Results();

        while(($job = $this->shift()) instanceof JobInterface) {
            $results->push($job->run($process, ...$arguments));
        }

        return $results;
    }

    public function serialize(): string
    {
        $payload = [];

        foreach($this->jobs as $job) {
            $payload[] = get_class($job) . self::PAYLOAD_DELIMITER . $job->serialize();
        }

        return serialize($payload);
    }

    public function unserialize(string $payload): ?self
    {
        $payloads = unserialize($payload);
        $jobs = new Bag();

        foreach($payloads as $item) {
            [$className, $payload] = explode(self::PAYLOAD_DELIMITER, $item);

            $jobs->push(new $className(unserialize($payload)));
        }

        return new self($jobs);
    }

}
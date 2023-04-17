<?php

namespace nomit\Process\Action;

use nomit\Work\CallbackInterface;
use nomit\Work\Process;
use nomit\Work\ProcessInterface;
use nomit\Process\Context\ContextInterface;
use nomit\Process\Controller\ControllerInterface;

class ProcessAction extends AbstractAction
{

    protected ProcessInterface $process;

    public function __construct(ProcessInterface|CallbackInterface $process)
    {
        if($process instanceof CallbackInterface) {
            $process = new Process($process);
        }

        $this->process = $process;
    }

    public function run(ControllerInterface $controller, ContextInterface $context, ...$arguments): bool
    {
        return $this->process->start();
    }

}
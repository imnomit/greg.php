<?php

namespace nomit\Console\Exception;

class RunCommandConsoleException extends ConsoleException
{

    protected $message = 'An error occurred while attempting to run the handled console command';

}
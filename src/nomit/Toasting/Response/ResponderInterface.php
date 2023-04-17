<?php

namespace nomit\Toasting\Response;

use nomit\Web\Request\RequestInterface;

interface ResponderInterface
{

    public function load(array $criteria = [], array $context = []): mixed;

    public function render(array $criteria = [], string $view = 'html', array $context = []): mixed;

}
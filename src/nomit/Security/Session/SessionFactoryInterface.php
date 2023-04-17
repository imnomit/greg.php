<?php

namespace nomit\Security\Session;

interface SessionFactoryInterface
{

    public function factory(): SessionInterface;

}
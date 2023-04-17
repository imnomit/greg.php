<?php

namespace Application\Controller\Index;

use nomit\FileSystem\FileSystem;
use nomit\Kernel\Component\AbstractController;
use nomit\Dumper\Dumper;
use nomit\Web\File\File;
use nomit\Web\Response\Response;

class IndexController extends AbstractController
{

    public function renderView(): void
    {
        $templatePathName = __DIR__ . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'Index' . DIRECTORY_SEPARATOR . 'view.latte';

        $this->sendResponse(
            $this->saveView($templatePathName)
        );
    }

}
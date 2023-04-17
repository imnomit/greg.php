<?php

namespace nomit\View\Resource;

use nomit\Dom\Builder\Element;

class StylesheetResource extends AbstractResource
{

    public function __construct(
        private string $path,
        private string $type = 'text/css',
        protected bool $import = true
    )
    {
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function build(): Element
    {

        if($this->import) {
            $result = $this->import($this->path);

            if($result === null) {
                return $this->buildLink($this->path, $this->type);
            }

            $element = new Element('style');

            $element->attribute('type', $this->type);

            $element->text($result);

            return $element;
        }

        return $this->buildLink($this->path, $this->type);
    }

    private function buildLink(string $path, string $type): Element
    {
        $element = new Element('link');

        $element->attribute('href', $path);
        $element->attribute('type', $type);
        $element->attribute('rel', 'stylesheet');

        return $element;
    }

}
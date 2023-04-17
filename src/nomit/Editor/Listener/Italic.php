<?php

namespace nomit\Editor\Listener;

use nomit\Editor\InlineListener;
use nomit\Editor\Line;

/**
 * Convert Italic Inline elements.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Italic extends InlineListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        if ($line->getAttribute('italic')) {
            $this->updateInput($line, '<em>'.$line->getInput().'</em>');
        }
    }
}

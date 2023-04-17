<?php

namespace nomit\Editor\Listener;

use nomit\Editor\AbstractBlockListener;
use nomit\Editor\Lexer;
use nomit\Editor\Line;

/**
 * Code Block
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 2.7.0
 */
class CodeBlock extends AbstractBlockListener
{
    /**
     * {@inheritDoc}
     */
    public function process(Line $line)
    {
        $heading = $line->getAttribute('code-block');
        if ($heading) {
            $this->pick($line);
            $line->setDone();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function render(Lexer $lexer)
    {
        $this->wrapElement('<pre><code>{__buffer__}</code></pre>');
    }
}

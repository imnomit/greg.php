<?php

namespace nomit\Web\File;

/**
 * A PHP stream of unknown size.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class Stream extends File
{
    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return false;
    }
}

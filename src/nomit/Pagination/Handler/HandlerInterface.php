<?php

namespace nomit\Pagination\Handler;

/**
 * Adapter used for pagination of a list of items.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
interface HandlerInterface
{
    /**
     * Fetch a subset of data.
     *
     * @param int $offset Starting offset (0 indexed)
     * @param int $limit Limit the number of results
     */
    public function get($offset, $limit);

    /**
     * Count number of elements in data.
     */
    public function count();
}

<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nomit\OptionsResolver\Exception;

use LogicException;

/**
 * Thrown when trying to read an option outside of or write it inside of
 * {@link \nomit\OptionsResolver\Options::resolve()}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AccessException extends LogicException implements ExceptionInterface
{
}

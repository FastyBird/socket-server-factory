<?php declare(strict_types = 1);

/**
 * InvalidStateException.php
 *
 * @license        More in LICENSE.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:SocketServerFactory!
 * @subpackage     Exceptions
 * @since          0.1.0
 *
 * @date           06.10.21
 */

namespace FastyBird\SocketServerFactory\Exceptions;

use RuntimeException;

class InvalidStateException extends RuntimeException implements IException
{

}

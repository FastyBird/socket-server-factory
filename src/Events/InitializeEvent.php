<?php declare(strict_types = 1);

/**
 * InitializeEvent.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:SocketServerFactory!
 * @subpackage     Events
 * @since          0.1.0
 *
 * @date           06.10.21
 */

namespace FastyBird\SocketServerFactory\Events;

use React\Socket\ServerInterface;
use Symfony\Contracts\EventDispatcher;

/**
 * Socket server initialized event
 *
 * @package        FastyBird:SocketServerFactory!
 * @subpackage     Events
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class InitializeEvent extends EventDispatcher\Event
{

	/** @var ServerInterface */
	private ServerInterface $server;

	public function __construct(
		ServerInterface $server
	) {
		$this->server = $server;
	}

	/**
	 * @return ServerInterface
	 */
	public function getServer(): ServerInterface
	{
		return $this->server;
	}

}

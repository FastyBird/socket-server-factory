<?php declare(strict_types = 1);

/**
 * SocketServerFactory.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:SocketServerFactory!
 * @subpackage     common
 * @since          0.1.0
 *
 * @date           06.10.21
 */

namespace FastyBird\SocketServerFactory;

use Nette;
use Psr\EventDispatcher;
use React\EventLoop;
use React\Socket;
use RuntimeException;

class SocketServerFactory
{

	use Nette\SmartObject;

	/** @var string */
	private string $serverAddress;

	/** @var int */
	private int $serverPort;

	/** @var string|null */
	private ?string $serverCertificate;

	/** @var EventDispatcher\EventDispatcherInterface|null */
	private ?EventDispatcher\EventDispatcherInterface $dispatcher;

	/** @var EventLoop\LoopInterface|null */
	private ?EventLoop\LoopInterface $eventLoop;

	/**
	 * @param string $serverAddress
	 * @param int $serverPort
	 * @param EventDispatcher\EventDispatcherInterface $dispatcher
	 * @param EventLoop\LoopInterface|null $eventLoop
	 * @param string|null $serverCertificate
	 */
	public function __construct(
		string $serverAddress,
		int $serverPort,
		?EventDispatcher\EventDispatcherInterface $dispatcher = null,
		?EventLoop\LoopInterface $eventLoop = null,
		?string $serverCertificate = null
	) {
		$this->serverAddress = $serverAddress;
		$this->serverPort = $serverPort;
		$this->serverCertificate = $serverCertificate;

		$this->dispatcher = $dispatcher;

		$this->eventLoop = $eventLoop;
	}

	/**
	 * @param EventLoop\LoopInterface|null $eventLoop
	 *
	 * @return Socket\ServerInterface
	 */
	public function create(?EventLoop\LoopInterface $eventLoop = null): Socket\ServerInterface
	{
		if ($this->eventLoop === null && $eventLoop === null) {
			throw new Exceptions\InvalidStateException('React Event loop instance is missing. Register service or provide it in create call');
		}

		if ($eventLoop === null) {
			$eventLoop = $this->eventLoop;
		}

		try {
			$server = new Socket\SocketServer($this->serverAddress . ':' . $this->serverPort, [], $eventLoop);

		} catch (RuntimeException $ex) {
			throw new Exceptions\InvalidStateException('Socket server could not be created', $ex->getCode(), $ex);
		}

		if ($this->serverCertificate !== null) {
			if (
				is_file($this->serverCertificate)
				&& file_exists($this->serverCertificate)
			) {
				$server = new Socket\SecureServer($server, $eventLoop, [
					'local_cert' => $this->serverCertificate,
				]);

			} else {
				throw new Exceptions\InvalidArgumentException('Provided SSL certiificate file could not be loaded');
			}
		}

		if ($this->dispatcher !== null) {
			$this->dispatcher->dispatch(new Events\InitializeEvent($server));
		}

		return $server;
	}

}

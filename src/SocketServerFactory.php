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

	/** @var EventDispatcher\EventDispatcherInterface */
	private EventDispatcher\EventDispatcherInterface $dispatcher;

	/** @var EventLoop\LoopInterface */
	private EventLoop\LoopInterface $eventLoop;

	/**
	 * @param string $serverAddress
	 * @param int $serverPort
	 * @param EventLoop\LoopInterface $eventLoop
	 * @param EventDispatcher\EventDispatcherInterface $dispatcher
	 * @param string|null $serverCertificate
	 */
	public function __construct(
		string $serverAddress,
		int $serverPort,
		EventLoop\LoopInterface $eventLoop,
		EventDispatcher\EventDispatcherInterface $dispatcher,
		?string $serverCertificate = null
	) {
		$this->serverAddress = $serverAddress;
		$this->serverPort = $serverPort;
		$this->serverCertificate = $serverCertificate;

		$this->dispatcher = $dispatcher;

		$this->eventLoop = $eventLoop;
	}

	/**
	 * @return Socket\ServerInterface
	 */
	public function create(): Socket\ServerInterface
	{
		try {
			$server = new Socket\SocketServer($this->serverAddress . ':' . $this->serverPort, [], $this->eventLoop);

		} catch (RuntimeException $ex) {
			throw new Exceptions\InvalidStateException('Socket server could not be created', $ex->getCode(), $ex);
		}

		if ($this->serverCertificate !== null) {
			if (
				is_file($this->serverCertificate)
				&& file_exists($this->serverCertificate)
			) {
				$server = new Socket\SecureServer($server, $this->eventLoop, [
					'local_cert' => $this->serverCertificate,
				]);

			} else {
				throw new Exceptions\InvalidArgumentException('Provided SSL certiificate file could not be loaded');
			}
		}

		$this->dispatcher->dispatch(new Events\InitializeEvent($server));

		return $server;
	}

}

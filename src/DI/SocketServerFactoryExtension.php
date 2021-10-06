<?php declare(strict_types = 1);

/**
 * SocketServerFactoryExtension.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:SocketServerFactory!
 * @subpackage     DI
 * @since          0.1.0
 *
 * @date           06.10.21
 */

namespace FastyBird\SocketServerFactory\DI;

use FastyBird\SocketServerFactory;
use Nette;
use Nette\DI;
use Nette\Schema;
use React\EventLoop;
use stdClass;

/**
 * Socket server factory extension container
 *
 * @package        FastyBird:SocketServerFactory!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class SocketServerFactoryExtension extends DI\CompilerExtension
{

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 *
	 * @return void
	 */
	public static function register(
		Nette\Configurator $config,
		string $extensionName = 'fbSocketServerFactory'
	): void {
		$config->onCompile[] = function (
			Nette\Configurator $config,
			DI\Compiler $compiler
		) use (
			$extensionName
		): void {
			$compiler->addExtension($extensionName, new SocketServerFactoryExtension());
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfigSchema(): Schema\Schema
	{
		return Schema\Expect::structure([
			'server'    => Schema\Expect::structure([
				'address'     => Schema\Expect::string('127.0.0.1'),
				'port'        => Schema\Expect::int(8000),
				'certificate' => Schema\Expect::string(null)
					->nullable(),
			]),
			'eventLoop' => Schema\Expect::bool(true),
		]);
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		/** @var stdClass $configuration */
		$configuration = $this->getConfig();

		$builder->addDefinition($this->prefix('socketServer.factory'), new DI\Definitions\ServiceDefinition())
			->setType(SocketServerFactory\SocketServerFactory::class)
			->setArguments([
				'serverAddress'     => $configuration->server->address,
				'serverPort'        => $configuration->server->port,
				'serverCertificate' => $configuration->server->certificate,
			]);

		if ($configuration->eventLoop) {
			$builder->addDefinition('react.eventLoop', new DI\Definitions\ServiceDefinition())
				->setType(EventLoop\LoopInterface::class)
				->setFactory('React\EventLoop\Factory::create');
		}
	}

}

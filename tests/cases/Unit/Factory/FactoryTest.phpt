<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\SocketServerFactory;
use React\Socket\ServerInterface;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../BaseTestCase.php';

/**
 * @testCase
 */
final class FactoryTest extends BaseTestCase
{

	public function testCreateServer(): void
	{
		$socketServerFactory = $this->createContainer()->getByType(SocketServerFactory\SocketServerFactory::class);

		$server = $socketServerFactory->create();

		Assert::notNull($server);
		Assert::true($server instanceof ServerInterface);
		Assert::same('tcp://127.0.0.1:8000', $server->getAddress());
	}

	/**
	 * @throws FastyBird\SocketServerFactory\Exceptions\InvalidStateException
	 */
	public function testMultipleCreate(): void
	{
		$socketServerFactory = $this->createContainer()->getByType(SocketServerFactory\SocketServerFactory::class);

		$socketServerFactory->create();
		$socketServerFactory->create();
	}

}

$test_case = new FactoryTest();
$test_case->run();

<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\SocketServerFactory;
use React\EventLoop;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../BaseTestCase.php';

/**
 * @testCase
 */
final class ExtensionTest extends BaseTestCase
{

	public function testCompilersServices(): void
	{
		$container = $this->createContainer();

		Assert::notNull($container->getByType(SocketServerFactory\SocketServerFactory::class));

		Assert::notNull($container->getByType(EventLoop\LoopInterface::class));
	}

}

$test_case = new ExtensionTest();
$test_case->run();

<?php

App::uses('Model', 'Model');
App::uses('MasterReplicaMysql', 'MasterReplica.Model/Datasource/Database');
App::uses('CakeTestCase', 'TestSuite');

/**
 * Class MasterReplicaMysqlTest
 */
class MasterReplicaMysqlTest extends CakeTestCase {

	/** @var array */
	protected $_databaseConfig = [
		'datasource' => 'Database/MasterReplicaMysql',
		'persistent' => false,
		'prefix' => '',
		'encoding' => 'utf8mb4',
		'connections' => [
			'_common_' => [
				'host' => 'localhost',
				'password' => 'password',
				'database' => 'test',
			],
			'master' => [
				'login' => 'root',
			],
			'replica' => [
				'login' => 'reader',
			],
		],
	];

	/** @var string */
	protected static $_defaultConnectionRole;

	/** @var MasterReplicaMysql */
	public $testSource;

/**
 * @inheritdoc
 */
	public static function setUpBeforeClass() {
		$defaultConnectionRoleReflection = new ReflectionProperty('MasterReplicaMysql', '_defaultConnectionRole');
		$defaultConnectionRoleReflection->setAccessible(true);
		$defaultConnectionRole = $defaultConnectionRoleReflection->getValue();
		self::$_defaultConnectionRole = $defaultConnectionRole;

		parent::setUpBeforeClass();
	}

/**
 * @inheritdoc
 */
	public function setUp() {
		parent::setUp();

		$this->testSource = new MasterReplicaMysql($this->_databaseConfig, false);
	}

/**
 * {@inheritdoc}
 */
	public function tearDown() {
		MasterReplicaMysql::setDefaultConnectionRole(self::$_defaultConnectionRole);
		unset($this->testSource);

		parent::tearDown();
	}

/**
 * test setDefaultConnectionRole()
 *
 * @return void
 */
	public function testSetDefaultConnectionRole() {
		$prop = new ReflectionProperty('MasterReplicaMysql', '_defaultConnectionRole');
		$prop->setAccessible(true);

		$expected = 'tokubetsu_na_setsuzoku';

		MasterReplicaMysql::setDefaultConnectionRole($expected);
		$actual = $prop->getValue();

		$this->assertSame($expected, $actual);
	}

/**
 * test switchConnectionRole()
 *
 * @return void
 */
	public function testSwitchConnectionRole() {
		/** @var PHPUnit_Framework_MockObject_MockObject&MasterReplicaMysql $subject */
		$subject = $this->_createPartialMock(
			get_class($this->testSource),
			['reconnect']
		);

		$expected = [
				'login' => 'root',
				'host' => 'localhost',
				'password' => 'password',
				'database' => 'test',
				'port' => '3306',
				'flags' => [],
			] + $this->_databaseConfig;
		$subject->expects($this->once())
			->method('reconnect')
			->with($expected);

		$subject->setConfig($this->_databaseConfig);
		$subject->switchConnectionRole('master');
	}

/**
 * Get Partial mocked instance
 *
 * @see https://github.com/sebastianbergmann/phpunit/blob/master/src/Framework/TestCase.php#L1531
 * @return PHPUnit_Framework_MockObject_MockObject
 */
	protected function _createPartialMock($originalClassName, array $methods) {
		return $this->getMockBuilder($originalClassName)
			->disableOriginalConstructor()
			->disableOriginalClone()
			->disableArgumentCloning()
			->setMethods(empty($methods) ? null : $methods)
			->getMock();
	}
}

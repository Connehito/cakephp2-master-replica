<?php

App::uses('Mysql', 'Model/Datasource/Database');

/**
 * Class MasterReplicaMysql
 */
class MasterReplicaMysql extends Mysql {

	/** @var string */
	protected static $_defaultConnectionRole = 'master';

	/** @var string */
	protected $_currentConnectionRole;

	/** @var string shared key name in config.connections */
	const COMMON_CONNECTION_KEY = '_common_';

/**
 * {@inheritDoc}
 *
 * ```
 * # example
 * $config = [
 *      'datasource' => 'Database/MasterReplicaMysql',
 *      'persistent' => false,
 *      'prefix' => '',
 *      'encoding' => 'utf8mb4',
 *      'connections' => [
 *           '_common_' => [
 *               'login' => 'root',
 *               'password' => 'password',
 *               'database' => 'my_app',
 *           ],
 *           'master' => [
 *               'host' => 'localhost',
 *           ],
 *           'Replica' => [
 *               'host' => '192.168.0.2',
 *           ],
 *       ],
 * ];
 * ```
 */
	public function __construct(array $config = null, $autoConnect = true) {
		$role = $this->_getDefaultConnectionRole($config);
		$config = $this->_buildConfig($role, $config);

		parent::__construct($config, $autoConnect);
	}

/**
 * Set default role to connection
 *
 * @param string $role name of connection role (e.g. `master` `replica`)
 * @return void
 */
	public static function setDefaultConnectionRole($role) {
		self::$_defaultConnectionRole = $role;
	}

/**
 * Switch current role to connection
 *
 * @param string $role name of connection role (e.g. `master` `replica`)
 * @return void
 */
	public function switchConnectionRole($role) {
		if ($this->_currentConnectionRole === $role) {
			return;
		}

		$config = $this->_buildConfig($role);
		$this->reconnect($config);

		$this->_currentConnectionRole = $role;
	}

/**
 * Build database config to provide compatibility with CakePHP normal `Database/Mysql`.
 *
 * @param string $role name of connection role (e.g. `master` `replica`)
 * @param array $config Config name declared in database.php
 * @return array Config value set
 * @throws InvalidArgumentException
 */
	protected function _buildConfig($role, $config = []) {
		if (!$config) {
			$config = $this->config;
		}
		if (!isset($config['connections'])) {
			throw new InvalidArgumentException('config.connections must be set.');
		}

		$connections = $config['connections'];
		if (!isset($connections[$role])) {
			throw new InvalidArgumentException("{$role} is not declared");
		}
		$connection = $connections[$role] + $connections[self::COMMON_CONNECTION_KEY];
		$config = $connection + $config;

		return $config;
	}

/**
 * Get default connection role
 *
 * @param array $config Config value set
 * @return string Connection role name
 */
	protected function _getDefaultConnectionRole($config) {
		if (isset($config['connection_role'])) {
			return $config['connection_role'];
		}

		return self::$_defaultConnectionRole;
	}
}

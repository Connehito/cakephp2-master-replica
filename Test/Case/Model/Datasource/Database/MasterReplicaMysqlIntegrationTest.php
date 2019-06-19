<?php

App::uses('AppModel', 'Model');
App::uses('MasterReplicaMysql', 'MasterReplica.Model/Datasource/Database');
App::uses('CakeTestCase', 'TestSuite');

/*
if (!class_exists('DATABASE_CONFIG')) {
	class DATABASE_CONFIG {

		public $test = array(
			'datasource' => 'MasterReplica.Database/MasterReplicaMysql',
			'persistent' => true,
			'connection_role' => 'replica',
			'connections' => array(
				'_common_' => array(
					'host' => 'database',
					'database' => 'my_app',
				),
				'master' => array(
					'login' => 'my_app',
					'password' => 'secret',
				),
				'replica' => array(
					'login' => 'read-only-user',
					'password' => 'secretsecret',
				),
			),
		);
	}
}
*/

class Post extends AppModel
{

	public $useDbConfig = 'default';
}

/**
 * Class MasterReplicaMysqlTest
 *
 * @property Post $Post
 */
class MasterReplicaMysqlIntegrationTest extends CakeTestCase
{

	public $fixtures = array(
		'plugin.MasterReplica.post'
	);

	/**
	 * {@inheritdoc}
	 */
	public function setUp()
	{
		parent::setUp();
		$this->Post = ClassRegistry::init('Post');
	}

	/**
	 * {@inheritdoc}
	 */
	public function tearDown()
	{
		$conn = $this->Post->getDataSource();
		$conn->switchConnectionRole('master');

		unset($this->Post);
		/** @var MasterReplicaMysql $conn */

		parent::tearDown();
	}

	/**
	 * test switchConnectionRole()
	 *
	 * @expectedException PDOException
	 * @expectedExceptionMessage UPDATE command denied to user
	 * @return void
	 */
	public function testSwitchConnectionRole()
	{
		/** @var MasterReplicaMysql $conn */
		$conn = $this->Post->getDataSource();
		$newData = [
			'user_id' => 10,
			'title' => 'Lorem ipsum dolor sit amet',
			'content' => 'Lorem ipsum dolor sit amet',
		];
		$this->Post->save($newData);
		$this->assertCount(
			1,
			$this->Post->find('all', ['conditions' => $newData]),
			'Success to insert new record'
		);

		$conn->switchConnectionRole('replica');
		$this->Post->save($newData);
	}
}

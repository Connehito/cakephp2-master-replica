<?php

/**
 * All MasterReplicaPlugin plugin tests
 */
class AllMasterReplicaTest extends CakeTestCase {

/**
 * Suite define the tests for this plugin
 *
 * @return CakeTestSuite Suite class instance.
 */
	public static function suite() {
		$suite = new CakeTestSuite('All MasterReplicaPlugin test');

		$path = CakePlugin::path('MasterReplica') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}

}

#!/bin/bash

git clone -b master https://github.com/FriendsOfCake/travis.git --depth 1 ../travis
../travis/before_script.sh

if [ -n "$USE_PHPUNIT_5" ]; then
	cd ../cakephp
	composer global remove 'phpunit/phpunit'
	composer remove --dev  --ignore-platform-reqs "phpunit/phpunit"
	composer require  --dev --no-interaction --prefer-source --ignore-platform-reqs "phpunit/phpunit:5.*"
	echo "require_once __DIR__ . '/../../vendors/autoload.php';" >> ./app/Config/bootstrap.php
	rm ./app/Vendor/PHPUnit
	cd $TRAVIS_BUILD_DIR
fi

if [ -z "$PHPCS" ]; then
	mysql -e "CREATE USER 'read-only-user'@'%';"
	mysql -e "GRANT SELECT,SUPER ON *.* TO 'read-only-user';"
	echo "<?php
	class DATABASE_CONFIG {
	public \$test = array(
			'datasource' => 'MasterReplica.Database/MasterReplicaMysql',
			'persistent' => true,
			'connection_role' => 'master',
			'connections' => array(
				'_common_' => array(
					'host' => '0.0.0.0',
					'database' => 'cakephp_test',
				),
				'master' => array(
					'login' => 'travis',
				),
				'replica' => array(
					'login' => 'read-only-user',
				),
			),
	);
	}" > ../cakephp/app/Config/database.php
fi

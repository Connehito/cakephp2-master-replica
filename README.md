# CakePHP Master Replica Plugin

The datasource for CakePHP(2.x).This plugin enables one-connection to act as two(or more) roles, like master(read-write) and replica(read-only).

[![Build Status](https://travis-ci.org/Connehito/cakephp2-master-replica.svg?branch=master)](https://travis-ci.org/Connehito/cakephp2-master-replica)
[![codecov](https://codecov.io/gh/Connehito/cakephp2-master-replica/branch/master/graph/badge.svg)](https://codecov.io/gh/Connehito/cakephp2-master-replica)
[![Latest Stable Version](https://poser.pugx.org/connehito/cakephp2-master-replica/v/stable)](https://packagist.org/packages/Connehito/cakephp2-master-replica)
[![Total Downloads](https://poser.pugx.org/connehito/cakephp2-master-replica/downloads)](https://packagist.org/packages/Connehito/cakephp2-master-replica)
[![License](https://poser.pugx.org/connehito/cakephp2-master-replica/license)](https://packagist.org/packages/Connehito/cakephp2-master-replica)

## Supports

- PHP 5.6+ / 7.0+
- CakePHP 2.7+
- MySQL

## Usage

1. Download the repository to set `app/Plugin/MasterReplica`
2. Load plugin in `app/Config/bootstrap.php` like `CakePlugin::load('MasterReplica');` or `CakePlugin::loadAll();`
3. Set your `database.php` with `MasterReplica.Database/MasterReplicaMysql` datasource. It requires `connections` property.

### Example

Set up your database configuration.

- Databse-A(for master): mysql;host=db-host,databasename=app_db,login=root,pass=password
- Databse-B(for replica): mysql;host=replica-host,databasename=app_db,login=read-only-user,pass=another-password

```php
// database.php
<?php
class DATABASE_CONFIG {

	public $default = array(
		'datasource' => 'MasterReplica.Database/MasterReplicaMysql',
		'persistent' => true,
		// default connection role(optional)
		'connection_role' => 'master',
		'connections' => array(
			// shared values(you can leave this values empty, but must be declared)
			'_common_' => array(
				'database' => 'app_db',
			),
			// default connection values
			'master' => array(
				'host' => 'db-host',
				'login' => 'root',
				'password' => 'password',
			),
			// `secondary` role connection values
			'secondary' => array(
				'host' => 'replica-host',
				'login' => 'read-only-user',
				'password' => 'another-password',
			),
		),
	);
}
```

In app, now you can connect to database master or replica db as you like :tada:

```php
$Post = ClassRegistry::init('Post');
// as default, connect with `master` role.
$Post->save(array('Post' => array('user_id' => 10, 'title' => 'new post', 'content' => 'some content')));

// switch to `replica` role
$conn = $this->Post->getDataSource();
$conn->switchConnectionRole('secondary');
```

## License

The plugin is available as open source under the terms of the [MIT License](http://opensource.org/licenses/MIT).

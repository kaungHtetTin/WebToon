<?php
include_once(__DIR__ . '/env.php');

class Database
{
	private static $mysqliConnection = null;
	private static $pdoConnection = null;

	private static function getConfig()
	{
		Env::load(dirname(__DIR__) . '/.env');

		return [
			'host' => Env::get('DB_HOST', 'localhost'),
			'port' => (int)Env::get('DB_PORT', 3306),
			'database' => Env::get('DB_DATABASE', 'webtoon2'),
			'username' => Env::get('DB_USERNAME', 'root'),
			'password' => Env::get('DB_PASSWORD', ''),
			'charset' => Env::get('DB_CHARSET', 'utf8mb4'),
		];
	}

	function connect()
	{
		if (self::$mysqliConnection !== null) {
			return self::$mysqliConnection;
		}

		$config = self::getConfig();
		self::$mysqliConnection = mysqli_connect(
			$config['host'],
			$config['username'],
			$config['password'],
			$config['database'],
			$config['port']
		);

		if (self::$mysqliConnection) {
			mysqli_set_charset(self::$mysqliConnection, $config['charset']);
		}

		return self::$mysqliConnection;
	}

	public static function getPdoConnection()
	{
		if (self::$pdoConnection !== null) {
			return self::$pdoConnection;
		}

		$config = self::getConfig();
		$dsn = sprintf(
			'mysql:host=%s;port=%d;dbname=%s;charset=%s',
			$config['host'],
			$config['port'],
			$config['database'],
			$config['charset']
		);

		self::$pdoConnection = new PDO(
			$dsn,
			$config['username'],
			$config['password'],
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			]
		);

		return self::$pdoConnection;
	}

	function read($query)
	{
		$conn = $this->connect();
		$result = mysqli_query($conn, $query);

		if (!$result) {
			return false;
		}

		$data = false;
		while ($row = mysqli_fetch_assoc($result)) {
			$data[] = $row;
		}

		return $data;
	}

	function save($query)
	{
		$conn = $this->connect();
		$result = mysqli_query($conn, $query);
		return (bool)$result;
	}
}

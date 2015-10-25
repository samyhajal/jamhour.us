<?php
class MySQLIE extends mysqli
{
	private static $instance;

	/**
	 * Create a mysqli object
	 * @access public
	 * @param array|string $args     Either an array containing login credentials or just the host string
	 * @param string $user     User to connect as
	 * @param string $pass     Password for connecting
	 * @param database $database Description
	 *
	 */
	private function __construct($args, $user="",$pass="",$database="")
	{
		if ( is_array($args) )
		{
			$host		= $args["host"];
			$user		= $args["user"];
			$pass		= $args["pass"];
			$database	= $args["database"];
		}
		else
		{
			$host	= $args;
		}
		parent::__construct($host,$user,$pass,$database);
	}

	/**
	 * Singleton function for connecting to the database
	 * @access public
	 * @final
	 * @param array $params An array of connection parameters
	 *
	 * @return obj    Returns a static instance of the DB connection
	 */
	final public static function connect_db($params)
	{
		if ( !isset(self::$instance) )
		{
			self::$instance	= new MySQLIE($params);
		}
		return self::$instance;
	}

	/**
	 * Inserts data into the database
	 * @access public
	 * @param string $table       The table to insert into
	 * @param array $args        An array with field names as keys
	 * @param bool $html_encode Boolean whether to html encode or not
	 *
	 * @throws Throws an exception when there is a DB error
	 */
	public function insert($table, $args, $html_encode = false)
	{
		$query	= "INSERT INTO ". $table ." (";
		$col	= "";
		$values	= "";
		foreach ( $args as $key=>$val )
		{
			$col	.= $key .",";
			$values	.= "'";
			$values	.= ( $html_encode == false ) ? addslashes($val) : htmlentities(addslashes($val));
			$values	.= "',";
		}
		$col	= rtrim($col,",");
		$values	= rtrim($values,",");;
		$query	.= $col .") VALUES (". $values .")";
		$this->query($query);
		if ( $this->error )
		{
			throw new Exception($this->error);
		}
	}

	/**
	 * Updates a row in the database
	 * @access public
	 * @param string $table		The table to update
	 * @param array $args 		An array with field names as keys
	 * @param string $where		Where conditional of the insert string excluding "WHERE"
	 * @example $where			id='1' AND user_id='2'
	 * @param bool $html_encode	Boolean whether to html encode or not
	 *
	 * @throws Throws an exception when ther is a DB error
	 */
	public function update($table, $args, $where = null, $html_encode = false)
	{
		$query	= "UPDATE ". $table ." SET ";
		foreach ( $args as $key=>$val )
		{
			$query	.= $key ." = '";
			$query	.= ( $html_encode == false ) ? addslashes($val) : htmlentities(addslashes($val));
			$query	.= "',";
		}
		$query	= rtrim($query,",");
		$query	.= ( $where == null ) ? "" : " WHERE ". $where;
		$this->query($query);
		if ( $this->error )
		{
			throw new Exception($this->error . " -- \r\n". $query);
		}
	}

	/**
	 * Dynamically get the fields for different tables
	 * @access public
	 * @param string $table  The table to get fields from.
	 * @param array $ignore An array of fields to ignore.
	 *
	 * @return Type    Description
	 */
	public function get_fields($table,$ignore=null)
	{
		$db		= connect_db();
		$fields	= array();
		$args	= array();
		$ignore	= ( $ignore == null ) ? array() : $ignore;
		$result	= $db->query("DESCRIBE ". $table);
		while ( $row = $result->fetch_assoc() )
		{
			if ( empty($ignore) || ( !empty($ignore) && !in_array($row["Field"],$ignore) ) )
			{
				$fields[]	= $row["Field"];
			}
		}
		foreach ( $fields as $val )
		{
			if ( isset($_POST[$val]) )
			{
				$args[$val]	= $_POST[$val];
			}
		}
		return $args;
	}
}
?>
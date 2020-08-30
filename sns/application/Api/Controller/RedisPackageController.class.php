<?php
/**
 * User: 刘北林
 * Date: 2020-6-26 0026
 * Time: 15:32
 * For:
 */
namespace Api\Controller;
class RedisPackageController  extends AppController
{
	protected static $handler = null;

	protected $options = [
		'host' => '127.0.0.1',//密码和ip用的配置,和这里无关
		'port' => 6379,
		'password' => '',//密码和ip用的配置,和这里无关
		'select' => 0,
		'timeout' => 0,//关闭时间 0:代表不关闭
		'expire' => 0,
		'persistent' => false,
		'prefix' => '',
	];


	/**
	 * 构造函数
	 * @param array $options 缓存参数
	 * @access public
	 */
	public function __construct($options = [])    {
		if (!extension_loaded('redis')) {   //判断是否有扩展(如果你的apache没reids扩展就会抛出这个异常)
			throw new \BadFunctionCallException('not support: redis');
		}
		if (!empty($options)) {
			$this->options = array_merge($this->options, $options);
		}
		$func = $this->options['persistent'] ? 'pconnect' : 'connect';     //判断是否长连接
		self::$handler = new \Redis;
		if ($this->options['persistent']) {
			self::$handler->$func($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
		} else {
			self::$handler->$func($this->options['host'], $this->options['port'], $this->options['timeout']);
		}
//		if ('' != config('redis')['auth']) {
//			self::$handler->auth(config('redis')['auth']);
//		}
		if (0 != $this->options['select']) {
			self::$handler->select($this->options['select']);
		}
	}

	/**
	 * 释放资源
	 */
	public function close() {
		self::$handler->close ();
	}

	/**
	 * ***********************操作键key begin***************************************
	 */

	/**
	 * 查找符合给定模式的key。
	 * KEYS *命中数据库中所有key。
	 * KEYS h?llo命中hello， hallo and hxllo等。
	 * KEYS h*llo命中hllo和heeeeello等。
	 * KEYS h[ae]llo命中hello和hallo，但不命中hillo。
	 * 特殊符号用"\"隔开
	 * @param string $name
	 * @return; 返回符合给定模式的key列表。
	 */
	public function getKeys($key) {
		if (empty ( $key )) {
			$key = "*";
		}
		return self::$handler->keys ( $key );
	}

	/**
	 * 移除给定的一个或多个key。
	 * 如果key不存在，则忽略该命令
	 * @param string $name
	 */
	public function delKeys($key) {
		return self::$handler->del ( $key );
	}

	/**
	 * 检查给定key是否存在。
	 * @param string $name
	 * 若key存在，返回1，否则返回0。
	 */
	public function exists($key) {
		return self::$handler->exists ( $key );
	}

	/**
	 * 移除已经存在KEYS
	 *
	 * @param array $keys
	 */
	public function delete($keys) {
		return self::$handler->delete ( $keys );
	}

	/**
	 * 返回key所储存的值的类型。
	 * none(key不存在) int(0)
	 * string(字符串) int(1)
	 * list(列表) int(3)
	 * set(集合) int(2)
	 * zset(有序集) int(4)
	 * hash(哈希表) int(5)
	 * @param string $key
	 */
	public function type($key) {
		return self::$handler->type ( $key );
	}

	/**
	 * 排序，分页等
	 * 参数
	 * array(
	 * 'by' => 'some_pattern_*', //通过哪个key进行排序
	 * 'limit' => array(0, 1), //限定条数
	 * 'get' => 'some_other_pattern_*' or an array of patterns,//获取结果数据
	 * 'sort' => 'asc' or 'desc', //排序
	 * 'alpha' => TRUE, //按字母顺序
	 * 'store' => 'external-key' //将排序内容保存到指定key中
	 * )
	 * 返回或保存给定列表、集合、有序集合key中经过排序的元素。
	 * 排序默认以数字作为对象，值被解释为双精度浮点数，然后进行比较。
	 * @param string $key
	 * @param array $condition
	 */
	public function sort($key, $condition) {
		return self::$handler->sort ( $key, $condition );
	}

	/**
	 * ***********************操作键key end***************************************
	 */

	/**
	 * ***********************操作string类型数据 begin***************************************
	 */

	/**
	 * 将字符串值value关联到key。
	 * 如果key已经持有其他值，SET就覆写旧值，无视类型。
	 * 总是返回OK(TRUE)，因为SET不可能失败。
	 * @param string $key
	 * @param string $value
	 */
	public function set($key, $value) {
		return self::$handler->set ( $key, $value );
	}
	/**
	 * 将key的值设为value，当且仅当key不存在。
	 * 若给定的key已经存在，则SETNX不做任何动作。
	 * 设置成功，返回true。
	 * 设置失败，返回false。
	 * @param string $key
	 * @param string $value
	 */
	public function setNx($key, $value) {
		return self::$handler->setNx ( $key, $value );
	}

	/**
	 * 将值value关联到key，并将key的生存时间设为seconds(以秒为单位)。
	 * 如果key 已经存在，SETEX命令将覆写旧值。
	 * 设置成功时返回OK
	 * 当seconds参数不合法时，返回一个错误。
	 * @param string $key
	 * @param string $value
	 * @param int $time
	 */
	public function setEx($key, $value, $seconds) {
		return self::$handler->setEx ( $key, $seconds, $value );
	}

	/**
	 * 用value参数覆写(Overwrite)给定key所储存的字符串值，从偏移量offset开始。
	 * 不存在的key当作空白字符串处理。
	 * @param string $key
	 * @param string $value
	 * @param int $offset
	 */
	public function setRange($key, $value, $offset) {
		return self::$handler->setRange ( $key, $offset, $value );
	}

	/**
	 * 同时设置一个或多个key-value对。
	 * 当发现同名的key存在时，MSET会用新值覆盖旧值，如果你不希望覆盖同名key，请使用MSETNX命令。
	 * @param array $arrays
	 */
	public function mSet($arrays) {
		return self::$handler->mSet ( $arrays );
	}

	/**
	 * 同时设置一个或多个key-value对，当且仅当key不存在。
	 * 当所有key都成功设置，返回1。
	 * 如果所有key都设置失败(最少有一个key已经存在)，那么返回0。
	 * @param array $arrays
	 */
	public function mSetNx($arrays) {
		return self::$handler->mSetNx ( $arrays );
	}

	/**
	 * 如果key已经存在并且是一个字符串，APPEND命令将value追加到key原来的值之后。
	 * 如果key不存在，APPEND就简单地将给定key设为value，就像执行SET key value一样。
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function appendKey($key, $value) {
		return self::$handler->append ( $key, $value );
	}

	/**
	 * 返回key所关联的字符串值。
	 * 如果key不存在则返回特殊值nil。
	 * @param string $key
	 */
	public function get($key) {
		return self::$handler->get ( $key );
	}

	/**
	 * 返回所有(一个或多个)给定key的值。
	 * 如果某个指定key不存在，返回false。
	 * 因此，该命令永不失败。
	 * @param array $arrays
	 */
	public function mGet($arrays) {
		return self::$handler->mGet ( $arrays );
	}

	/**
	 * 返回key中字符串值的子字符串，字符串的截取范围由start和end两个偏移量决定(包括start和end在内)。
	 * 负数偏移量表示从字符串最后开始计数，-1表示最后一个字符，-2表示倒数第二个，以此类推。
	 * @param string $key
	 * @param int $start
	 * @param int $end
	 */
	public function getRange($key, $start, $end) {
		return self::$handler->getRange ( $key, $start, $end );
	}

	/**
	 * 将给定key的值设为value，并返回key的旧值。
	 * 当key存在但不是字符串类型时，返回一个错误。
	 * @param  $key
	 * @param  $value
	 */
	public function getSet($key, $value) {
		return self::$handler->getSet ( $key, $value );
	}

	/**
	 * 返回key所储存的字符串值的长度。
	 * 当key储存的不是字符串值时，返回一个错误。
	 * @param string $key
	 */
	public function strlen($key) {
		return self::$handler->strlen ( $key );
	}

	/**
	 * 将key中储存的数字值增一。
	 * 如果key不存在，以0为key的初始值，然后执行INCR操作。
	 * 如果值包含错误的类型，或字符串类型的值不能表示为数字，那么返回一个错误。
	 * @param string $key
	 */
	public function incr($key) {
		return self::$handler->incr ( $key );
	}

	/**
	 * 将key所储存的值加上增量num。
	 * 如果key不存在，以0为key的初始值，然后执行INCRBY命令。
	 * 如果值包含错误的类型，或字符串类型的值不能表示为数字，那么返回一个错误。
	 * @param string $key
	 * @param int $num
	 */
	public function incrBy($key, $num) {
		return self::$handler->incrBy ( $key, $num );
	}

	/**
	 * 将key中储存的数字值减一。
	 * 如果key不存在，以0为key的初始值，然后执行DECR操作。
	 * 如果值包含错误的类型，或字符串类型的值不能表示为数字，那么返回一个错误。
	 * @param string $key
	 */
	public function decr($key) {
		return self::$handler->decr ( $key );
	}

	/**
	 * 将key所储存的值减去增量num。
	 * 如果key不存在，以0为key的初始值，然后执行DECRBY命令。
	 * 如果值包含错误的类型，或字符串类型的值不能表示为数字，那么返回一个错误。
	 * @param string $key
	 * @param int $num
	 */
	public function decrBy($key, $num) {
		return self::$handler->decrBy ( $key, $num );
	}

	/**
	 * ***********************操作string类型数据 end***************************************
	 */

	/**
	 * ***********************操作哈希表(Hash)类型数据 begin***************************************
	 */

	/**
	 * 将哈希表key中的域field的值设为value。
	 * 如果key不存在，一个新的哈希表被创建并进行HSET操作。
	 * 如果域field已经存在于哈希表中，旧值将被覆盖。
	 * 如果field是哈希表中的一个新建域，并且值设置成功，返回1。
	 * 如果哈希表中域field已经存在且旧值已被新值覆盖，返回0。
	 * @param string $key
	 * @param string $field
	 * @param string $val
	 */
	public function hSet($key, $field, $val) {
		return self::$handler->hSet ( $key, $field, $val );
	}

	/**
	 * 将哈希表key中的域field的值设置为value，当且仅当域field不存在。设置成功返回true
	 * 若域field已经存在，该操作无效。返回false
	 * @param string $key
	 * @param string $field
	 * @param string $val
	 */
	public function hSetNx($key, $field, $val) {
		return self::$handler->hSetNx ( $key, $field, $val );
	}

	/**
	 * 同时将多个field - value(域-值)对设置到哈希表key中。
	 * 此命令会覆盖哈希表中已存在的域。
	 * 如果key不存在，一个空哈希表被创建并执行HMSET操作。
	 * 如果命令执行成功，返回true。
	 * 当key不是哈希表(hash)类型时，返回false。
	 * @param string $key
	 * @param array $vals
	 */
	public function hMset($key, $vals) {
		return self::$handler->hMset ( $key, $vals );
	}

	/**
	 * 返回哈希表key中给定域field的值。
	 * 当给定域不存在或是给定key不存在时，返回false。
	 * @param string $key
	 * @param string $field
	 * @return string
	 */
	public function hGet($key, $field) {
		return self::$handler->hGet ( $key, $field );
	}

	/**
	 * 返回哈希表key中，一个或多个给定域的值。
	 * 如果给定的域不存在于哈希表，那么返回一个false。
	 * @param string $key
	 * @param array $fields
	 * @return array
	 */
	public function hMget($key, $fields) {
		return self::$handler->hMget ( $key, $fields );
	}

	/**
	 * 返回哈希表key中，所有的域和值。
	 * 取得整个HASH表的信息，返回一个以KEY为索引VALUE为内容的数组。
	 * @param string $key
	 * @return array
	 */
	public function hGetAll($key) {
		return self::$handler->hGetAll ( $key );
	}

	/**
	 * 返回哈希表key的长度。
	 * 当给定key不存在时，返回false。
	 * @param string $key
	 * @return int
	 */
	public function hLen($key) {
		return self::$handler->hLen ( $key );
	}

	/**
	 * 删除哈希表key中指定的一个指定域。
	 * 若删除成果，返回1。
	 * 当给定key不存在时，返回0。
	 * 当key中指定域不存在时，返回false。
	 * @param string $key
	 * @param string $field
	 */
	public function hDel($key, $field) {
		return self::$handler->hDel ( $key, $field );
	}

	/**
	 * 返回哈希表key中的所有域。
	 * 当key存在时，返回哈希表中所有域的表。
	 * 当key不存在时，返回false。
	 * @param string $key
	 * @return array
	 */
	public function hKeys($key) {
		return self::$handler->hKeys ( $key );
	}

	/**
	 * 返回哈希表key中的所有值。
	 * 当key存在时，返回哈希表中所有值。
	 * 当key不存在时，返回false。
	 * @param string $key
	 * @return array
	 */
	public function hVals($key) {
		return self::$handler->hVals ( $key );
	}

	/**
	 * 查看哈希表key中，给定域field是否存在。
	 * 如果哈希表含有给定域，返回true。
	 * 如果哈希表不含有给定域，或key不存在，返回false。
	 * @param string $key
	 * @param string $field
	 */
	public function hExists($key, $field) {
		return self::$handler->hExists ( $key, $field );
	}

	/**
	 * 为哈希表key中的域field的值加上增量increment。
	 * 增量也可以为负数，相当于对给定域进行减法操作。
	 * 如果域key存在，field不存在，那么在执行命令前，域的值被初始化为0。
	 * 如果域field不是数值类型，则返回false。
	 * 如果key不存在，则返回false。
	 * @param string $key
	 * @param string $field
	 * @param int $num
	 */
	public function hIncrBy($key, $field, $num) {
		return self::$handler->hIncrBy ( $key, $field, $num );
	}

	/**
	 * 浮点型
	 * 为哈希表key中的域field的值加上增量increment。
	 * 增量也可以为负数，相当于对给定域进行减法操作。
	 * 如果域key存在，field不存在，那么在执行命令前，域的值被初始化为0。
	 * 如果域field不是数值类型，则返回false。
	 * 如果key不存在，则返回false。
	 * @param string $key
	 * @param string $field
	 * @param float $num
	 */
	public function hIncrByFloat($key, $field, $num) {
		return self::$handler->hIncrByFloat ( $key, $field, $num );
	}

	/**
	 * ***********************操作哈希表(Hash)类型数据 end***************************************
	 */

	/**
	 * ***********************操作链表(List)类型数据 begin***************************************
	 */

	/**
	 * 将一个或多个值value插入到列表key的表头(左侧)。
	 * 返回列表长度
	 * @param string $key
	 * @param string $val
	 * @return int 列表的长度
	 */
	public function lPush($key, $val) {
		return self::$handler->lPush ( $key, $val );
	}

	/**
	 * 将值value插入到列表key的表头，当且仅当key存在并且是一个列表。
	 * 当key不存在时，lPushx命令什么也不做，返回0
	 * 当key存在时，返回列表的长度
	 * @param string $key
	 * @param string $val
	 * @return int 列表的长度
	 */
	public function lPushx($key, $val) {
		return self::$handler->lPushx ( $key, $val );
	}

	/**
	 * 移除并返回列表key的头元素。
	 * 如果LIST有值时，返回列表的头元素。
	 * 如果是一个空LIST则返回FLASE。
	 * @param string $key
	 */
	public function lPop($key) {
		return self::$handler->lPop ( $key );
	}

	/**
	 * 将一个或多个值value插入到列表key的底部(右侧)。
	 * 返回列表长度
	 * @param string $key
	 * @param string $val
	 * @return int 列表的长度
	 */
	public function rPush($key, $val) {
		return self::$handler->rPush ( $key, $val );
	}

	/**
	 * 将值value插入到列表key的底部，当且仅当key存在并且是一个列表。
	 * 当key不存在时，lPushx命令什么也不做，返回0
	 * 当key存在时，返回列表的长度
	 * @param string $key
	 * @param string $val
	 * @return int 列表的长度
	 */
	public function rPushx($key, $val) {
		return self::$handler->rPushx ( $key, $val );
	}

	/**
	 * 移除并返回列表key的底部元素。
	 * 如果LIST有值时，返回列表的底部元素。
	 * 如果是一个空LIST则返回FLASE。
	 * @param string $key
	 */
	public function rPop($key) {
		return self::$handler->rPop ( $key );
	}

	/**
	 * 移除并获取列表头部的第一个元素。
	 * 如果列表没有元素则会阻塞列表直到等待超时或发现可弹出元素为止。
	 * 返回值：[0=>key,1=>value]，超时返回[]
	 * @param string $key
	 * @param int $time（单位：秒）
	 */
	public function blPop($key, $time) {
		return self::$handler->blPop ( $key, $time );
	}

	/**
	 * 移除并获取列表尾部的第一个元素。
	 * 如果列表没有元素则会阻塞列表直到等待超时或发现可弹出元素为止。
	 * 返回值：[0=>key,1=>value]，超时返回[]
	 * @param string $key
	 * @param int $time（单位：秒）
	 */
	public function brPop($key, $time) {
		return self::$handler->brPop ( $key, $time );
	}

	/**
	 * 如果KEY存在并且为LIST且有元素，那么返回KEY的长度，为空或者不存在返回0。
	 * 如果KEY存在但不是为LIST类型，则返回false。
	 * @param string $key
	 */
	public function lSize($key) {
		return self::$handler->lSize ( $key );
	}

	/**
	 * 根据索引值返回指定KEY LIST中的元素。
	 * 0为第一个元素，1为第二个元素。
	 * -1为倒数第一个元素，-2为倒数第二个元素。
	 * 如果指定了一个不存在的索引值，则返回FLASE。
	 * @param string $key
	 * @param int $num
	 */
	public function lGet($key, $num) {
		return self::$handler->lGet ( $key, $num );
	}

	/**
	 * 根据索引值设置新的VAULE
	 * @param string $key
	 * @param int $num
	 * @param string $val
	 *        	如果设置成功返回TURE，
	 *        	如果KEY所指向的不是LIST，或者索引值超出LIST本身的长度范围，则返回flase。
	 */
	public function lSet($key, $num, $val) {
		return self::$handler->lSet ( $key, $num, $val );
	}

	/**
	 * 取得指定索引值范围内的所有元素。
	 * 如果key存在时，返回list数组
	 * 如果key不存在时，返回空数组
	 * @param string $key
	 * @param int $start
	 * @param int $end
	 */
	public function lRange($key, $start, $end) {
		return self::$handler->lRange ( $key, $start, $end );
	}

	/**
	 * 返回列表key的长度。
	 * 如果key不存在，则key被解释为一个空列表，返回0.
	 * 如果key不是列表类型，返回false。
	 * @param string $key
	 */
	public function lLen($key) {
		return self::$handler->lLen ( $key );
	}

	/**
	 * 对一个列表进行修剪(trim)，就是说，让列表只保留指定区间内的元素，不在指定区间之内的元素都将被删除。
	 * 操作成功返回true
	 * @param string $key
	 * @param int $start
	 * @param int $stop
	 */
	public function lTrim($key, $start, $stop) {
		return self::$handler->lTrim ( $key, $start, $stop );
	}

	/**
	 * 首先要去判断count参数，
	 * 如果count参数为0，那么所有符合删除条件的元素都将被移除。
	 * 如果count参数为整数,将从左至右删除count个符合条件的元素，
	 * 如果为负数则从右至左删除count个符合条件的元素。
	 * 删除成功，返回删除个数值
	 * @param string $key
	 * @param string $val
	 * @param int $count
	 */
	public function lRem($key, $val, $count) {
		return self::$handler->lRem ( $key, $val, $count );
	}

	/**
	 * 返回列表key中，下标为index的元素。
	 * 如果index参数的值不在列表的区间范围内(out of range)，返回false。
	 * 如果key不存在，则返回false
	 * @param string $key
	 * @param int $index
	 */
	public function lIndex($key, $index) {
		return self::$handler->lIndex ( $key, $index );
	}

	/**
	 * 从源key0的最后弹出一个元素，并且把这个元素从目标key1的顶部（左侧）压入目标key1。
	 * 成功返回被弹出的元素。
	 * 失败返回false。
	 * @param string $key0
	 * @param string $key1
	 */
	public function rpoplpush($key1, $key2) {
		return self::$handler->rpoplpush ( $key1, $key2 );
	}

	/**
	 * 移除列表中最后一个元素，将其插入另一个列表头部，并返回这个元素。
	 * 如果列表没有元素则会阻塞列表直到等待超时或发现可弹出元素为止。
	 * 参数：源列表，目标列表，超时时间（单位：秒）
	 * 超时返回false
	 * @param string $key0
	 * @param string $key1
	 * @param number $time
	 */
	public function brpoplpush($key1, $key2, $time) {
		if (empty ( $time )) {
			$time = 3;
		}
		return self::$handler->brpoplpush ( $key1, $key2, $time );
	}

	/**
	 * ***********************操作链表(List)类型数据 end*****************************************
	 */

	/**
	 * ***********************操作集合(Set)类型数据 begin***************************************
	 */

	/**
	 * 添加一个val到key容器中
	 * 如果val不在key容器中，返回1；
	 * 如果val已经在key容器中，返回0；
	 * 如果key不是set类型，则返回false;
	 * @param string $key
	 * @param string $val
	 */
	public function sAdd($key, $val) {
		return self::$handler->sAdd ( $key, $val );
	}

	/**
	 * 移除指定的val从key容器中
	 * 如果val在key容器中，返回1。
	 * 如果val不存在key容器中，返回0。
	 * 如果key不是集合类型，返回false。
	 * @param string $key
	 * @param string $val
	 */
	public function sRem($key, $val) {
		return self::$handler->sRem ( $key, $val );
	}

	/**
	 * 将val元素从key1集合移动到key2集合。
	 * 如果val元素被成功移除，返回true。
	 * 如果val元素不是key1集合的成员，并且没有任何操作对key2集合执行，那么返回false。
	 * @param string $key1
	 * @param string $key2
	 * @param string $val
	 */
	public function sMove($key1, $key2, $val) {
		return self::$handler->sMove ( $key1, $key2, $val );
	}

	/**
	 * 判断val元素是否是集合key的成员。
	 * 如果val元素是集合的成员，返回true。
	 * 如果val元素不是集合的成员，或key不存在，返回false。
	 * @param string $key
	 * @param string $val
	 */
	public function sIsMember($key, $val) {
		return self::$handler->sIsMember ( $key, $val );
	}

	/**
	 * 返回集合key的基数(集合中元素的数量)。
	 * 当key存在时，返回集合的基数。
	 * 当key不存在时，返回0。
	 * 当key不是集合类型时，返回false。
	 * @param string $key
	 */
	public function sCard($key) {
		return self::$handler->sCard ( $key );
	}

	/**
	 * 随机返回一个元素，并且在SET容器中移除该元素。
	 * 当key存在时吗，返回被移除的随机元素。
	 * 当key不存在或key是空集时，返回false。
	 * 当key不是集合时，返回false。
	 * @param string $key
	 */
	public function sPop($key) {
		return self::$handler->sPop ( $key );
	}

	/**
	 * 随机返回集合中的一个元素。
	 * 当key不存在或key是空集时，返回false。
	 * 当key不是集合时，返回false。
	 * @param string $key
	 */
	public function sRandMember($key) {
		return self::$handler->sRandMember ( $key );
	}

	/**
	 * 求两个集合的交集
	 * 返回一个集合的全部成员，该集合是所有给定集合的交集。
	 * 不存在的key被视为空集。
	 * 当给定集合当中有一个空集时，结果也为空集。
	 * 当给的两个集合中有一个不是集合类型，则返回false。
	 * @param string $key1
	 * @param string $key2
	 */
	public function sInter($key1, $key2) {
		return self::$handler->sInter ( $key1, $key2 );
	}

	/**
	 * 执行一个交集操作，并把结果存储到一个新的SET容器中。
	 * 当执行成时，返回交集中元素的数量。
	 * 当给的两个集合中有一个不是集合类型，则返回false。
	 * @param string $new_key
	 * @param string $key1
	 * @param string $key2
	 */
	public function sInterStore($new_key, $key1, $key2) {
		return self::$handler->sInterStore ( $new_key, $key1, $key2 );
	}

	/**
	 * 返回一个集合的全部成员，该集合是所有给定集合的并集。
	 * 当执行成时，返回并集中元素的数量。
	 * 不存在的key被视为空集。
	 * 当给的两个集合中有一个不是集合类型，则返回false。
	 * @param string $key1
	 * @param string $key2
	 */
	public function sUnion($key1, $key2) {
		return self::$handler->sUnion ( $key1, $key2 );
	}

	/**
	 * 执行一个并集操作就和sUnion()一样，但是结果储存在新的集合中。
	 * @param string $new_key
	 * @param string $key1
	 * @param string $key2
	 */
	public function sUnionStore($new_key, $key1, $key2) {
		return self::$handler->sUnionStore ( $new_key, $key1, $key2 );
	}

	/**
	 * 执行差集操作在N个不同的SET容器之间，并返回结果。
	 * 这个操作取得结果是第一个SET相对于其他参与计算的SET集合的差集。
	 * 当给的两个集合中有一个不是集合类型，则返回false。
	 * @param string $key1
	 * @param string $key2
	 */
	public function sDiff($key1, $key2) {
		return self::$handler->sDiff ( $key1, $key2 );
	}

	/**
	 * 与sDiff函数功能一直，只是结果为一个新的SET集合，存储到新的集合中。
	 * 当执行成功时，返回差集中元素的数量。
	 * 当给的两个集合中有一个不是集合类型，则返回false。
	 * @param string $new_key
	 * @param string $key1
	 * @param string $key2
	 */
	public function sDiffStore($new_key, $key1, $key2) {
		return self::$handler->sDiffStore ( $new_key, $key1, $key2 );
	}

	/**
	 * 返回SET集合中的所有元素。
	 * @param string $key
	 */
	public function sMembers($key) {
		return self::$handler->sMembers ( $key );
	}

	/**
	 * ***********************操作集合(Set)类型数据 end***************************************
	 */

	/**
	 * ***********************操作有序集合(zSet)类型数据 begin***************************************
	 */

	/**
	 * 增加一个或多个元素，如果该元素已经存在，更新它的socre值
	 * 虽然有序集合有序，但它也是集合，不能重复元素，添加重复元素只会更新原有元素的score值
	 * 当val不存在key中，则返回1；
	 * 当val已经存在key中，则返回0；
	 * 当key存在但不是有序集类型时，返回一个false。
	 * @param  $key
	 * @param  $score
	 * @param  $val
	 */
	public function zAdd($key, $score, $val) {
		return self::$handler->zAdd ( $key, $score, $val );
	}

	/**
	 * 取得特定范围内的排序元素,0代表第一个元素,1代表第二个以此类推。-1代表最后一个,-2代表倒数第二个...
	 * 按升序排序
	 * @param string $key
	 * @param int $start
	 * @param int $end
	 * @param bool $withscores
	 *        	true返回score信息
	 */
	public function zRange($key, $start, $end, $withscores = false) {
		return self::$handler->zRange ( $key, $start, $end, $withscores );
	}

	/**
	 * 从有序集合中删除指定的成员。
	 * 当val存在key中，删除成功，则返回1；
	 * 当val不存在key中，删除失败，则返回0；
	 * 当key不存在时，则返回0。
	 * 当key存在但不是有序集类型时，返回一个false。
	 * @param string $key
	 * @param string $val
	 */
	public function zDelete($key, $val) {
		return self::$handler->zDelete ( $key, $val );
	}

	/**
	 * 返回key对应的有序集合中指定区间的所有元素。
	 * 这些元素按照score从高到低的顺序进行排列。
	 * 对于具有相同的score的元素而言，将会按照递减的字典顺序进行排列。
	 * 该命令与ZRANGE类似，只是该命令中元素的排列顺序与前者不同。
	 * 按降序排序
	 * @param string $key
	 * @param int $start
	 * @param int $end
	 * @param bool $withscores
	 *        	true返回score信息
	 */
	public function zRevRange($key, $start, $end, $withscores = false) {
		return self::$handler->zRevRange ( $key, $start, $end, $withscores );
	}

	/**
	 * 返回key对应的有序集合中score介于min和max之间的所有元素（包哈score等于min或者max的元素）。
	 * 元素按照score从低到高的顺序排列。
	 * 如果元素具有相同的score，那么会按照字典顺序排列。
	 *
	 * @param string $key
	 * @param int $min
	 * @param int $max
	 * @param array $options
	 *        	=> TRUE, 'limit' => array(1, 1));
	 */
	public function zRangeByScore($key, $min, $max, $options = array()) {
		return self::$handler->zRangeByScore ( $key, $min, $max, $options );
	}

	/**
	 * 返回key对应的有序集合中score介于min和max间的元素的个数。
	 *
	 * @param string $key
	 * @param int $min
	 * @param int $max
	 */
	public function zCount($key, $min, $max) {
		return self::$handler->zCount ( $key, $min, $max );
	}

	/**
	 * 移除key对应的有序集合中scroe位于min和max（包含端点）之间的所有元素。
	 * 移除成功，则返回移除个数；
	 * 如果区间内没有score值，则返回0；
	 * 如果key不存在，则返回0；
	 * 如果key不是有序集合，则返回false；
	 * @param string $key
	 * @param int $min
	 * @param int $max
	 */
	public function zRemRangeByScore($key, $min, $max) {
		return self::$handler->zRemRangeByScore ( $key, $min, $max );
	}

	/**
	 * 移除key对应的有序集合中rank值介于start和end之间的所有元素。
	 * start和end均是从0开始的，并且两者均可以是负值。
	 * 当索引值为负值时，表明偏移值从有序集合中score值最高的元素开始。
	 * 例如：-1表示具有最高score的元素，而-2表示具有次高score的元素，以此类推。
	 *
	 * 移除成功，则返回移除个数；
	 * 如果区间内没有值，则返回0；
	 * 如果key不存在，则返回0；
	 * 如果key不是有序集合，则返回false；
	 *
	 * @param string $key
	 * @param int $start
	 * @param int $end
	 */
	public function zRemRangeByRank($key, $start, $end) {
		return self::$handler->zRemRangeByRank ( $key, $start, $end );
	}

	/**
	 * 返回存储在key对应的有序集合中的元素的个数。
	 * 如果key不存在，则返回0；
	 * 如果key不是有序集合，则返回false；
	 *
	 * @param string $key
	 */
	public function zSize($key) {
		return self::$handler->zSize ( $key );
	}

	/**
	 * 返回key对应的有序集合中val的score值。
	 * 如果val在有序集合中不存在，那么将会返回false。
	 * 如果key不存在，则返回false；
	 * 如果key不是有序集合，则返回false；
	 *
	 * @param string $key
	 * @param string $val
	 */
	public function zScore($key, $val) {
		return self::$handler->zScore ( $key, $val );
	}

	/**
	 * 返回key对应的有序集合中val元素的索引值，元素按照score从低到高进行排列。
	 * rank值（或index）是从0开始的，这意味着具有最低score值的元素的rank值为0。
	 * 使用zRank可以获得从低到高排列的元素的rank（或index）。
	 * val存在key有序集合中，则返回从低到高排列的元素的rank；
	 * key不存在或则不是有序集合，则返回false；
	 * 如果val在有序集合中不存在，那么将会返回false。
	 *
	 * @param string $key
	 * @param string $val
	 */
	public function zRank($key, $val) {
		return self::$handler->zRank ( $key, $val );
	}

	/**
	 * 返回key对应的有序集合中val元素的索引值，元素按照score从低到高进行排列。
	 * rank值（或index）是从0开始的，这意味着具有最低score值的元素的rank值为0。
	 * 使用zRevRank可以获得从高到低排列的元素的rank（或index）。
	 * val存在key有序集合中，则返回从低到高排列的元素的rank；
	 * key不存在或则不是有序集合，则返回false；
	 * 如果val在有序集合中不存在，那么将会返回false。
	 *
	 * @param string $key
	 * @param string $val
	 */
	public function zRevRank($key, $val) {
		return self::$handler->zRevRank ( $key, $val );
	}

	/**
	 * 将key对应的有序集合中val元素的scroe加上increment。
	 * 如果指定的val不存在，那么将会添加该元素，并且其score的初始值为increment。
	 * 如果key不存在，那么将会创建一个新的有序列表，其中包含val这一唯一的元素。
	 * 如果key对应的值不是有序列表，返回false。
	 * 指定的score的值应该是能够转换为数字值的字符串，并且接收双精度浮点数。
	 * 同时，你也可用提供一个负值，这样将减少score的值。
	 * 操作成功，返回score值
	 *
	 * @param unknown $key
	 * @param unknown $score
	 * @param unknown $val
	 */
	public function zIncrBy($key, $increment, $val) {
		return self::$handler->zIncrBy ( $key, $increment, $val );
	}

	/**
	 * 对keys对应的多个有序集合计算合集，并将结果存储在new_key中。
	 * 在传递输入keys之前必须提供输入keys的个数和其它可选参数。
	 * 在默认情况下，元素的结果score是包含该元素的所有有序集合中score的和。
	 * 如果new_key已经存在，那么它将会被重写。
	 * 操作成功，返回合集集合中元素个数;
	 * 如果keys中有一个不是有序集合，则返回false;
	 *
	 * @param string $new_key
	 * @param array $keys
	 * @param array $weights
	 */
	public function zUnion($new_key, $keys) {
		return self::$handler->zUnion ( $new_key, $keys );
	}

	/**
	 * 计算多个由keys指定的有序集合的交集，并且将结果存储在new_key中。
	 * 在该命令中，在你传递输入keys之前，必须提供输入keys的个数和其它可选的参数。
	 * 在默认情况下，一个元素的结果score是具有该元素的所有有序集合的score的和。
	 * 如果目标已经存在，那么它将会被重写。
	 * 操作成功，返回合集集合中元素个数。
	 * 如果keys中有一个不是有序集合，则返回false;
	 *
	 * @param string $new_key
	 * @param array $keys
	 */
	public function zInter($new_key, $keys) {
		return self::$handler->zInter ( $new_key, $keys );
	}
	/**
	 * ***********************操作有序集合(zSet)类型数据 end***************************************
	 */

	/**
	 * ************************example***************************************
	 *
	 * $redis = new \com\RedisDrive();
	 * $res = $redis->set('name','123');
	 * var_dump($res); //bool(true)
	 */

	/*
	 * $res = $redis->setnx('name','123');
	 * var_dump($res); //bool(false)
	 * $res1 = $redis->setnx('name1','123');
	 * var_dump($res1); //bool(true)
	 */

	/*
	 * $res = $redis->setex('name','123',5);
	 * var_dump($res); //bool(true)
	 * sleep(5);
	 * $res = $redis->setex('name','123','aa');
	 * var_dump($res); //bool(false)
	 */

	/*
	 * $redis->set('name','Hello World!');
	 * $res = $redis->setRange('name','Kitty',6);
	 * var_dump($res); //int(12)
	 * echo $redis->get('name'); //Hello Kitty!
	 */

	/*
	 * $res = $redis->getKeys();
	 * var_dump($res);
	 *
	 * $res = $redis->delKeys('name1');
	 *
	 * $res = $redis->getKeys();
	 * var_dump($res);
	 */
	/*
	 * var_dump($redis->type('first_name')); //1 string
	 * echo '<br/><br/>';
	 * var_dump($redis->type('user1')); //5 hash
	 * echo '<br/><br/>';
	 * var_dump($redis->type('list1')); //3 list
	 * echo '<br/><br/>';
	 * var_dump($redis->type('set')); //2 set
	 * echo '<br/><br/>';
	 */

	/*
	 * $res = $redis->getKeys();
	 * var_dump($res);
	 * $arrays = array('first_name','secode_name');
	 * $res = $redis->delKeys($arrays);
	 * var_dump($res);
	 * $res = $redis->getKeys();
	 * var_dump($res);
	 */

	/*
	 * $array_mset = array('first_name'=>'123',
	 * 'secode_name'=>'456',
	 * 'third_name'=>'789'
	 * );
	 * $res = $redis->mset($array_mset);
	 * var_dump($res);
	 */

	/*
	 * $res = $redis->exists('third_name');
	 * var_dump($res);
	 */

	/*
	 * $array_mset = array('first_name'=>'123',
	 * 'secode_name'=>'456'
	 * );
	 * $res = $redis->msetnx($array_mset);
	 * var_dump($res);
	 */
	/*
	 * $res = $redis->get('first_name');
	 * var_dump($res);
	 * $res = $redis->appendKey('first_name','test');
	 * var_dump($res);
	 */
	/*
	 * $arrays = array('first_name','third_name','test');
	 * $res = $redis->mget($arrays);
	 * var_dump($res);
	 */
	/*
	 * $res = $redis->get('first_name');
	 * var_dump($res);
	 * $res = $redis->getrange('first_name',-4,-2);
	 * var_dump($res);
	 */
	/*
	 * $res = $redis->get('first_name');
	 * var_dump($res);
	 * echo '<br/>';
	 * $res = $redis->getset('first_name','first_name');
	 * var_dump($res);
	 * echo '<br/>';
	 * $res = $redis->get('first_name');
	 * var_dump($res);
	 */

	/*
	 * $res = $redis->strlen('first_name');
	 * var_dump($res);
	 */
	/*
	 * $redis->set('count',1);
	 * var_dump($redis->incr('count'));
	 * var_dump($redis->get('count'));
	 */

	/*
	 * $redis->set('count',1);
	 * var_dump($redis->incrby('count11',5));
	 * var_dump($redis->get('count'));
	 */

	/*
	 * var_dump($redis->exists('counter'));
	 * var_dump($redis->incrby('counter',5));
	 * var_dump($redis->get('counter'));
	 */

// $redis->set('count',1);
// var_dump($redis->decrby('count',10));

// var_dump($redis->hset('user1','name','test11'));

// var_dump($redis->hsetnx('user1','name11','test123456'));

// var_dump($redis->hget('user1','name')); //bool(false)

// var_dump($redis->hget('user1','name')); //string(6) "test11"

// $vals = array('name'=>'test','age'=>25,'sex'=>'男','profit'=>'软件工程师');
// var_dump($redis->hmset('user1',$vals));

// $fields = array('name','age','sex','profit');
// var_dump($redis->hmget('user1',$fields));

// var_dump($redis->hGetAll('user1'));
// var_dump($redis->hLen('user0'));
// var_dump($redis->hGetAll('user1'));
// var_dump($redis->hDel('user1','name1'));
// var_dump($redis->hGetAll('user1'));

// var_dump($redis->hKeys('user1'));
// var_dump($redis->hVals('user0'));
// var_dump($redis->hGetAll('user1'));
// echo '<br/>';
// var_dump($redis->hExists('user0','name11111'));

	/*
	 * var_dump($redis->hGetAll('user1'));
	 * echo '<br/>';
	 * var_dump($redis->hIncrBy('user1','sex1',-5));
	 * echo '<br/>';
	 * var_dump($redis->hGetAll('user1'));
	 */

	/*
	 * var_dump($redis->hGetAll('user1'));
	 * echo '<br/>';
	 * var_dump($redis->hIncrByFloat('user0','sex',5.2));
	 * echo '<br/>';
	 * var_dump($redis->hGetAll('user1'));
	 */

	/**
	 * 链表list操作***
	 */
// var_dump($redis->lPush('list','test'));
// var_dump($redis->lPush('list',array('test2','test3')));
// var_dump($redis->lPushx('list','aaa'));
// var_dump($redis->rPush('list1','bbb'));
// var_dump($redis->rPushx('list1','ccc'));
// var_dump($redis->rPushx('list2','bbb'));

// var_dump($redis->lPop('list'));
// var_dump($redis->lPop('list0'));

// var_dump($redis->rPop('list'));
// var_dump($redis->rPop('list0'));
// var_dump($redis->blPop('list123',2));

	/*
	 * var_dump($redis->lPush('list','test1'));
	 * var_dump($redis->lPush('list','test2'));
	 * var_dump($redis->lPush('list','test3'));
	 * var_dump($redis->brPop('list',2));
	 */

// var_dump($redis->lSize('list')); int(2)
// var_dump($redis->lSize('list1')); list1没有值：int(0)
// var_dump($redis->lSize('user1')); user1不是list类型：bool(false)
// var_dump($redis->lSize('list112121')); list112121不存在：int(0)

	/*
	 * var_dump($redis->lLen('list'));
	 * var_dump($redis->lLen('list1'));
	 * var_dump($redis->lLen('user1'));
	 * var_dump($redis->lLen('test123'));
	 */

	/*
	 * var_dump($redis->delKeys('list'));
	 * var_dump($redis->lPush('list','test1'));
	 * var_dump($redis->lPush('list','test2'));
	 * var_dump($redis->lPush('list','test3'));
	 * var_dump($redis->lPush('list','test4'));
	 */
	/*
	 * echo '<br/><br/>';
	 * var_dump($redis->lGet('list',1)); string(5) "test3"
	 * echo '<br/><br/>';
	 * var_dump($redis->lGet('list',0)); string(5) "test4"
	 *
	 * echo '<br/><br/>';
	 * var_dump($redis->lGet('list',-1));string(5) "test1"
	 * echo '<br/><br/>';
	 * var_dump($redis->lGet('list',-2));string(5) "test2"
	 */

// var_dump($redis->lGet('list',-2));
// var_dump($redis->lSet('list',5,'aaa')); //bool(true)
// var_dump($redis->lGet('list',-2));

// var_dump($redis->lRange('list',7,10));
// var_dump($redis->lRange('list00',0,4));

	/*
	 * var_dump($redis->lRange('list',0,10));
	 * echo '<br/><br/>';
	 * var_dump($redis->lTrim('list',-6,2));
	 * echo '<br/><br/>';
	 * var_dump($redis->lRange('list',0,10));
	 * var_dump($redis->lPush('list','test'));
	 * var_dump($redis->lPush('list','test'));
	 * var_dump($redis->lPush('list','test'));
	 * var_dump($redis->lPush('list','test'));
	 * var_dump($redis->lRange('list',0,-1));
	 * echo '<br/><br/>';
	 * var_dump($redis->lRem('list11111','test',0));
	 * echo '<br/><br/>';
	 * var_dump($redis->lRange('list',0,-1));
	 */

// var_dump($redis->lLen('user1'));
	/*
	 * var_dump($redis->lRange('list',0,-1));
	 * var_dump($redis->lIndex('list',12));
	 */
	/*
	 * $redis->delete(array('list','list1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->lPush('list','list1'));
	 * var_dump($redis->lPush('list','list2'));
	 * var_dump($redis->lPush('list1','list11'));
	 * var_dump($redis->lPush('list1','list12'));
	 * echo '<br/><br/>';
	 * var_dump($redis->lRange('list',0,-1));
	 * echo '<br/><br/>';
	 * var_dump($redis->lRange('list1',0,-1));
	 * echo '<br/><br/>';
	 */
// var_dump($redis->rpoplpush('list','list1'));
	/*
	 * var_dump($redis->lRange('list',0,-1));
	 * echo '<br/><br/>';
	 * var_dump($redis->lRange('list1',0,-1));
	 */
	/*
	 * echo '<br/><br/>';
	 * var_dump($redis->lRange('list',0,-1));
	 * echo '<br/><br/>';
	 * var_dump($redis->lRange('list1',0,-1));
	 */
	/*
	 * var_dump($redis->brpoplpush('list','list1',3));
	 * echo '<br/><br/>';
	 * var_dump($redis->lRange('list',0,-1));
	 * echo '<br/><br/>';
	 * var_dump($redis->lRange('list1',0,-1));
	 */

	/*
	 * var_dump($redis->sAdd('set','1'));
	 * echo '<br/>';
	 * var_dump($redis->sAdd('set','1'));
	 * echo '<br/>';
	 * var_dump($redis->sAdd('user1','1'));
	 */

	/*
	 * var_dump($redis->sRem('set','1'));
	 * echo '<br/>';
	 * var_dump($redis->sRem('set','1'));
	 * echo '<br/>';
	 * var_dump($redis->sRem('user1','1'));
	 */

	/*
	 * var_dump($redis->sAdd('set','1'));
	 * var_dump($redis->sAdd('set','2'));
	 * var_dump($redis->sAdd('set','3'));
	 * echo '<br/>';
	 * var_dump($redis->sAdd('set1','11'));
	 * var_dump($redis->sAdd('set1','22'));
	 * var_dump($redis->sAdd('set1','33'));
	 * echo '<br/>';
	 * var_dump($redis->sMembers('set'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('set1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMove('set','set1','2222'));
	 * echo '<br/>';
	 * var_dump($redis->sMembers('set'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('set1'));
	 * echo '<br/><br/>';
	 */

	/*
	 * var_dump($redis->sIsMember('set','3'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sIsMember('set','3333'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sIsMember('user1','3333'));
	 */

	/*
	 * var_dump($redis->sCard('set'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sCard('set1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sCard('set12'));
	 */

	/*
	 * var_dump($redis->sPop('set'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sPop('set12'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sPop('user1'));
	 * echo '<br/><br/>';
	 */

// var_dump($redis->sMembers('set'));

	/**
	 * var_dump($redis->sRandMember('set'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sRandMember('set123'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sRandMember('user1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('set'));
	 */

	/*
	 * var_dump($redis->sMembers('set'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('set1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sInterStore('new_set','set','set1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('new_set'));
	 */

	/*
	 * var_dump($redis->sMembers('set'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('set1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sUnion('set','set1'));
	 */

	/*
	 * var_dump($redis->sMembers('set'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('set1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('new_set1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sUnionStore('new_set1','set','set1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('new_set1'));
	 */

	/*
	 * var_dump($redis->sMembers('set'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('set1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('new_set'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sDiffStore('new_set','set1','user1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->sMembers('new_set'));
	 */
	/*
	 * var_dump($redis->delete(array('zset')));
	 * echo '<br/>';
	 * var_dump($redis->zAdd('zset',5,'zset5'));
	 * echo '<br/>';
	 * var_dump($redis->zAdd('zset',2,'zset2'));
	 * echo '<br/>';
	 * var_dump($redis->zAdd('zset',4,'zset4'));
	 * echo '<br/>';
	 * var_dump($redis->zAdd('zset',8,'zset8'));
	 * echo '<br/>';
	 * var_dump($redis->zAdd('zset',10,'zset10'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zRange('zset',0,-1,true));
	 */

	/*
	 * var_dump($redis->zRange('zset',1,-1,true));
	 * echo '<br/><br/>';
	 * var_dump($redis->zDelete('zset','zset1'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zRange('zset',1,-1,true));
	 */

// var_dump($redis->zDelete('zset11','zset1'));
// var_dump($redis->zDelete('user1','zset1'));

	/*
	 * var_dump($redis->zRange('zset',0,-1,true));
	 * echo '<br/><br/>';
	 * var_dump($redis->zRevRange('zset',0,-1,true));
	 * echo '<br/><br/>';
	 */

// var_dump($redis->zRange('zset',0,-1,true));
// echo '<br/><br/>';
// $option = array('withscores' => TRUE, 'limit' => array(0, 4));
// $option = array('withscores' => TRUE);
// $option = array();
// var_dump($redis->zRangeByScore('zset',5,10,$option));

	/*
	 * var_dump($redis->zRange('zset',0,-1,true));
	 * echo '<br/><br/>';
	 * //var_dump($redis->zCount('zset',2,10));
	 * var_dump($redis->zRemRangeByScore('zset11',11,14));
	 * var_dump($redis->zRemRangeByScore('user1',11,14));
	 * echo '<br/><br/>';
	 * var_dump($redis->zRange('zset',0,-1,true));
	 * echo '<br/><br/>';
	 */
	/*
	 * var_dump($redis->zAdd('zset',3,'zset3'));
	 * var_dump($redis->zAdd('zset',9,'zset9'));
	 * var_dump($redis->zAdd('zset',12,'zset12'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zRange('zset',0,-1,true));
	 * echo '<br/><br/>';
	 * var_dump($redis->zRemRangeByRank('zset',0,1));
	 * var_dump($redis->zRemRangeByRank('zset111',10,11));
	 * var_dump($redis->zRemRangeByRank('user1',10,11));
	 * echo '<br/><br/>';
	 */
// var_dump($redis->zRange('zset',0,-1,true));
	/*
	 * var_dump($redis->zSize('zset'));
	 * var_dump($redis->zSize('zset111'));
	 * var_dump($redis->zSize('user1'));
	 */

	/*
	 * var_dump($redis->zScore('zset','zset3'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zScore('zset','zset12'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zScore('zset','zset11112'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zScore('zset12','zset11112'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zScore('user1','zset3'));
	 */

	/*
	 * var_dump($redis->zRank('zset','zset31'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zRevRank('zset1','zset3'));
	 */

	/*
	 * var_dump($redis->zRange('zset',0,-1,true));
	 * echo '<br/><br/>';
	 * var_dump($redis->zRange('zset111',0,-1,true));
	 */
	/*
	 * echo '<br/><br/>';
	 * var_dump($redis->zIncrBy('zset',1,'zset3'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zIncrBy('zset',1,'zset34'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zIncrBy('zset111',1,'zset34'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zIncrBy('user1',1,'zset34'));
	 */

// var_dump($redis->delete(array('zset','zset1','zset2')));

	/*
	 * var_dump($redis->zRange('zset',0,-1,true));
	 * var_dump($redis->zRange('zset1',0,-1,true));
	 * var_dump($redis->zRange('zset2',0,-1,true));
	 */
	/*
	 * var_dump($redis->zRange('zset2',0,-1,true));
	 * echo '<br/><br/>';
	 * var_dump($redis->delete(array('zset','zset1')));
	 * var_dump($redis->zAdd('zset',0,'zset0'));
	 * var_dump($redis->zAdd('zset',1,'zset1'));
	 * var_dump($redis->zAdd('zset',10,'zset2'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zAdd('zset1',2,'zset2'));
	 * var_dump($redis->zAdd('zset1',3,'zset3'));
	 * echo '<br/><br/>';
	 * var_dump($redis->zUnion('zset2',array('zset','zset1')));
	 * echo '<br/><br/>';
	 * var_dump($redis->zRange('zset',0,-1,true));
	 * echo '<br/><br/>';
	 * var_dump($redis->zRange('zset1',0,-1,true));
	 * echo '<br/><br/>';
	 * var_dump($redis->zRange('zset2',0,-1,true));
	 */


//    /**
//     * @param $id
//     * @param int $m
//     * @return int
//     * 根据ID得到 hash 后 0～m-1 之间的值
//     */
//    private static function _hashId($id,$m=10){
//        //把字符串K转换为 0～m-1 之间的一个值作为对应记录的散列地址
//        $k = md5($id);
//        $l = strlen($k);
//        $b = bin2hex($k);
//        $h = 0;
//        for($i=0;$i<$l;$i++){
//            //相加模式HASH
//            $h += substr($b,$i*2,2);
//        }
//        $hash = ($h*1)%$m;
//        return $hash;
//    }
//
//
//    /**
//     * @return mixed
//     * 随机 HASH 得到 Redis Slave 服务器句柄
//     */
//    private function _getSlaveRedis(){
//        // 就一台 Slave 机直接返回
//        if($this->_sn <= 1){
//            return $this->_linkHandle['slave'][0];
//        }
//        // 随机 Hash 得到 Slave 的句柄
//        $hash = $this->_hashId(mt_rand(), $this->_sn);
//        return $this->_linkHandle['slave'][$hash];
//    }
//
//    /**
//     * 写入缓存
//     * @param string $key 键名
//     * @param string $value 键值
//     * @param int $exprie 过期时间 0:永不过期
//     * @return bool
//     */
//    public static function set($key, $value, $exprie = 0){
//        if ($exprie == 0) {
//            $set = self::$handler->set($key, $value);
//        } else {
//            $set = self::$handler->setex($key, $exprie, $value);
//        }
//        return $set;
//    }
//
//    /**
//     * 读取缓存
//     * @param string $key 键值
//     * @return mixed
//     */
//    public static function get($key){
//        $fun = is_array($key) ? 'Mget' : 'get';
//        return self::$handler->{$fun}($key);
//    }
//
//    /**
//     * 获取值长度
//     * @param string $key
//     * @return int
//     */
//    public static function lLen($key){
//        return self::$handler->lLen($key);
//    }
//
//    /**
//     * 将一个或多个值插入到列表头部
//     * @param $key
//     * @param $value
//     * @return int
//     */
//    public static function LPush($key, $value, $value2 = null, $valueN = null){
//        return self::$handler->lPush($key, $value, $value2, $valueN);
//    }
//
//    /**
//     * 移出并获取列表的第一个元素
//     * @param string $key
//     * @return string
//     */
//    public static function lPop($key){
//        return self::$handler->lPop($key);
//    }
//
//    /**
//     * @param $key
//     * @param $value
//     * @param null $value2
//     * @param null $valueN
//     * @return bool|int
//     *
//     */
//    public static function RPush($key, $value, $value2 = null, $valueN = null){
//        return self::$handler->rPush($key, $value, $value2, $valueN);
//    }
//
//
//    /**
//     * @param $key
//     * @param $start
//     * @param $end
//     * @return array
//     *
//     */
//    public static function lrange($key,$start,$end){
//        return self::$handler->lrange($key,$start,$end);
//    }
//
//    /**
//     *
//     */
//    public static function close(){
//        return self::$handler->close();
//    }
//
//    /**
//     *
//     */
//    public static function hset($name,$key,$value){
//        if(is_array($value)){
//            return self::$handler->hset($name,$key,serialize($value));
//        }
//        return self::$handler->hset($name,$key,$value);
//    }
//
//    /**
//     *
//     */
//    public static function hget($name,$key = null,$serialize=true){
//        if($key){
//            $row = self::$handler->hget($name,$key);
//            if($row && $serialize){
//                unserialize($row);
//            }
//            return $row;
//        }
//        return self::$handler->hgetAll($name);
//    }
//
//    /**
//     * 删除指定key中的指定字段
//     */
//    public static function hdel($name,$key = null){
//        if($key){
//            return self::$handler->hdel($name,$key);
//        }
//        return self::$handler->hdel($name,null);
//    }
//
//    /**
//     *
//     */
//    public static function del($name){
//        return self::$handler->del($name);
//    }
//
//
//    /**
//     * Transaction start
//     */
//    public static function multi(){
//        return self::$handler->multi();
//    }
//
//    /**
//     * Transaction send
//     */
//
//    public static function exec(){
//        return self::$handler->exec();
//    }
//
//    /**
//     * 添空当前数据库
//     *
//     * @return boolean
//     */
//    public static function clear(){
//        return self::$handler->flushDB();
//    }
//
//    /**
//     * 条件形式设置缓存，如果 key 不存时就设置，存在时设置失败
//     * @param string $key 缓存KEY
//     * @param string $value 缓存值
//     * @return boolean
//     */
//    public static function setnx($key, $value){
//        return self::$handler->setnx($key, $value);
//    }
//
//    /**
//     * 删除缓存
//     * @param string || array $key 缓存KEY，支持单个健:"key1" 或多个健:array('key1','key2')
//     * @return int 删除的健的数量
//     */
//    public static function remove($key){
//        // $key => "key1" || array('key1','key2')
//        return self::$handler->delete($key);
//    }
//
//    /**
//     * 值加加操作,类似 ++$i ,如果 key 不存在时自动设置为 0 后进行加加操作
//     * @param string $key 缓存KEY
//     * @param int $default 操作时的默认值
//     */
//    public static function incr($key,$default=1){
//        if($default == 1){
//            return self::$handler->incr($key);
//        }else{
//            return self::$handler->incrBy($key, $default);
//        }
//    }
//
//    /**
//     * 值减减操作,类似 --$i ,如果 key 不存在时自动设置为 0 后进行减减操作
//     *
//     * @param string $key 缓存KEY
//     * @param int $default 操作时的默认值
//     */
//    public static function decr($key,$default=1){
//        if($default == 1){
//            return self::$handler->decr($key,0);
//        }else{
//            return self::$handler->decrBy($key, $default);
//        }
//    }
}


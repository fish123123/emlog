<?php
/**
 * user model
 * @package EMLOG (www.emlog.net)
 */

class User_Model {

	private $db;

	function __construct() {
		$this->db = Database::getInstance();
	}

	function getUsers($page = null) {
		$condition = '';
		if ($page) {
			$perpage_num = Option::get('admin_perpage_num');
			$startId = ($page - 1) * $perpage_num;
			$condition = "LIMIT $startId, " . $perpage_num;
		}
		$res = $this->db->query("SELECT * FROM " . DB_PREFIX . "user $condition");
		$users = [];
		while ($row = $this->db->fetch_array($res)) {
			$row['name'] = htmlspecialchars($row['nickname']);
			$row['login'] = htmlspecialchars($row['username']);
			$row['email'] = htmlspecialchars($row['email']);
			$row['description'] = htmlspecialchars($row['description']);
			$row['ip'] = $row['ip'];
			$row['create_time'] = smartDate($row['create_time']);
			$row['update_time'] = smartDate($row['update_time']);
			$users[] = $row;
		}
		return $users;
	}

	function getOneUser($uid) {
		$row = $this->db->once_fetch_array("select * from " . DB_PREFIX . "user where uid=$uid");
		$userData = [];
		if ($row) {
			$userData = array(
				'username'    => htmlspecialchars($row['username']),
				'nickname'    => htmlspecialchars($row['nickname']),
				'email'       => htmlspecialchars($row['email']),
				'photo'       => htmlspecialchars($row['photo']),
				'description' => htmlspecialchars($row['description']),
				'role'        => $row['role'],
				'ischeck'     => $row['ischeck'],
			);
		}
		return $userData;
	}

	function updateUser($userData, $uid) {
		$utctimestamp = time();
		$Item = ["update_time=$utctimestamp"];
		foreach ($userData as $key => $data) {
			$Item[] = "$key='$data'";
		}
		$upStr = implode(',', $Item);
		$this->db->query("update " . DB_PREFIX . "user set $upStr where uid=$uid");
	}

	function addUser($login, $password, $role, $ischeck) {
		$utctimestamp = time();
		$sql = "insert into " . DB_PREFIX . "user (username,password,role,ischeck, create_time, update_time) values('$login','$password','$role','$ischeck', $utctimestamp, $utctimestamp)";
		$this->db->query($sql);
	}

	function deleteUser($uid) {
		$this->db->query("update " . DB_PREFIX . "blog set author=1, checked='y' where author=$uid");
		$this->db->query("delete from " . DB_PREFIX . "user where uid=$uid");
	}

	/**
	 * 判断用户名是否存在
	 *
	 * @param string $login
	 * @param int $uid 兼容更新作者资料时用户名未变更情况
	 * @return boolean
	 */
	function isUserExist($login, $uid = '') {
		$subSql = $uid ? 'and uid!=' . $uid : '';
		$data = $this->db->once_fetch_array("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user WHERE username='$login' $subSql");
		if ($data['total'] > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 判断用户昵称是否存在
	 *
	 * @param string $nickname
	 * @param int $uid 兼容更新作者资料时用户名未变更情况
	 * @return boolean
	 */
	function isNicknameExist($nickname, $uid = '') {
		if (empty($nickname)) {
			return FALSE;
		}
		$subSql = $uid ? 'and uid!=' . $uid : '';
		$data = $this->db->once_fetch_array("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user WHERE nickname='$nickname' $subSql");
		if ($data['total'] > 0) {
			return true;
		} else {
			return false;
		}
	}

	function getUserNum() {
		$data = $this->db->once_fetch_array("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user");
		return $data['total'];
	}
}

<?php
/**
 * 视图控制
 * @package EMLOG (www.emlog.net)
 */

class View {
	public static function getView($template, $ext = '.php') {
		if (!is_dir(TEMPLATE_PATH)) {
			emMsg('当前使用的模板已被删除或损坏，请登录后台更换其他模板。', BLOG_URL . 'admin/template.php');
		}
		return TEMPLATE_PATH . $template . $ext;
	}

	public static function getAdmView($template, $ext = '.php') {
		if (!is_dir(ADMIN_TEMPLATE_PATH)) {
			emMsg('后台模板已损坏', BLOG_URL);
		}
		return ADMIN_TEMPLATE_PATH . $template . $ext;
	}

	public static function output() {
		$content = ob_get_clean();
		ob_start();
		echo $content;
		ob_end_flush();
		exit;
	}

}

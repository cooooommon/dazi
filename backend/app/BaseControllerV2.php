<?php

//decode by http://www.yunlu99.com/
declare (strict_types=1);
namespace app;

abstract class BaseControllerV2
{
	public $_uniacid = 0;
	public $_param = [];
	public $_token;
	public $_autograph;
	protected $request;
	protected $_request;
	public function __construct(\think\Request $request)
	{
		$this->request = $request;
		$this->_request = $this->request;
		$this->_param = $this->request->param();
		$this->_token = $this->request->header("token");
		$this->_autograph = $this->request->header("autograph");
		$this->_uniacid = intval($this->request->param("i"));
		$this->_uniacid = $this->_uniacid ? $this->_uniacid : intval($this->request->param("uniacid"));
		if (empty($this->_uniacid) && longbingIsWeiqin()) {
			global $_GPC;
			global $_W;
			$this->_uniacid = $_W["uniacid"];
		}
		if (empty($this->_uniacid)) {
			$user_info = getUserForToken($this->_token);
			$this->_uniacid = $user_info["uniacid"];
		}
	}
	protected function getUserId()
	{
		$value = getCache($this->_autograph, $this->_uniacid);
		if ($value === false) {
			return 0;
		}
		return $value["id"];
	}
}
const strategies = {
	// errorMsg参数，提升了适用性
	isNotNull: function(value, errorMsg, regType) { //去空字符，不为空
		// regType 2去除所有空格
		if (regType == 2 && value) {
			value = value.replace(/(^\s*)|(\s*$)/g, "")
		}
		if (value === '' || value === undefined || value === false || JSON.stringify(value) === '[]' || JSON
			.stringify(value) === '{}') {
			// 返回字符串true  错误信息
			return errorMsg;
		}
	},
	minLength: function(value, errorMsg, length) { //限制最小长度
		if (value.length < length) {
			return errorMsg;
		}
	},
	maxLength: function(value, errorMsg, length) { //限制最大长度
		if (value == null || value.length < 1) {
			return errorMsg;
		} else if (value.length > length) {
			return `${errorMsg}，最多¥${length}字`;
		}
	},
	isAllPhone: function(value, errorMsg, regType, regText) {
		regText = regText || '电话号码'
		let reg = /((^400)-([0-9]{7})$)|(^1[3-9]\d{9}$)|((^0\d{2,3})-(\d{7,8})$)/
		if (value == null || value.length < 1) {
			return errorMsg;
		} else if (!reg.test(value)) {
			return `${value} ${regText}无效`
		}
	},
	isMobile: function(value, errorMsg) {
		//   if (!/^((\+?86)|(\(\+86\)))?(13[012356789][0-9]{8}|15[012356789][0-9]{8}|18[02356789][0-9]{8}|147[0-9]{8}|1349[0-9]{7}|([0-9]{3,4}-)?[0-9]{7,8})$/.test(value)) { //电话号码校验
		if (value == null || value.length < 1) {
			return errorMsg;
		}
		if (!/^(1[0-9]{10})$/.test(value)) { //电话号码校验
			return `${value} 手机号无效`;
		}
	},
	isZuoji: function(value, errorMsg) {
		if (!/0\d{2,3}-\d{7,8}|\(?0\d{2,3}[)-]?\d{7,8}|\(?0\d{2,3}[)-]*\d{7,8}$/.test(value)) { //座机号码校验
			return errorMsg;
		}
	},
	isPh_zj: function(value, errorMsg) {
		if (!value) {
			return errorMsg;
		} else if (!/^(1[0-9]{10})$/.test(value) && !
			/0\d{2,3}-\d{7,8}|\(?0\d{2,3}[)-]?\d{7,8}|\(?0\d{2,3}[)-]*\d{7,8}$/.test(value) && !
			/^400[0-9]{7}/.test(value)) { //座机号码校验
			return `${value} 电话号码无效`;
		}
	},
	isNotText: function(value, errorMsg) {
		if (!/^[\u4e00-\u9fa5]+$/.test(value)) { //非中文校验
		} else {
			return errorMsg;
		}
	},
	isWechat: function(value, errorMsg) {
		if (!/^[a-zA-Z]{1}[-_a-zA-Z0-9]{5,29}$/.test(value)) { //微信号码校验
			return errorMsg;
		}
	},
	isEmail: function(value, errorMsg) {
		if (!/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/.test(value)) { //邮箱校验
			return errorMsg;
		}
	},
	isMoney: function(value, errorMsg, regType) {
		// regType 1大于0；2可为0
		let regArr = {
			1: /^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/,
			2: /^(([0-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/
		}
		let reg = regArr[regType]
		if (value == '') {
			return errorMsg;
		} else if (regType == 1 && value * 1 === 0) {
			return `${errorMsg}，不可为0`;
		} else if (!reg.test(value)) {
			return `${errorMsg}，最多2位小数`;
		}
	},
	isUrl: function(value, errorMsg) {
		if (value.indexOf('http') < 0) {
			return errorMsg;
		}
	},
	isNumber: function(value, errorMsg, regType) {
		console.log(value, errorMsg, regType)
		if(regType){
			if(value === null || value === ''){
				return `请输入${errorMsg}`;
			}else if (!/^[1-9]+[0-9]*]*$/.test(value)) {
				return `请输入正确的${errorMsg}`;
			}
		}else{
			if (!/^[1-9]+[0-9]*]*$/.test(value)) {
				return errorMsg;
			}
		}
	},
	isZeroNumber: function(value, errorMsg) {
		if (!/^([0]|[1-9][0-9]*)$/.test(value)) {
			return errorMsg;
		}
	},
	// 验证是否为百分比
	isPercent: (value, errorMsg, regType) => {
		regType = regType || 1 // 1 0-100；2 1-100
		let reg = regType === 1 ? /^(?:0|[1-9][0-9]?|100)$/ : /^(?:[1-9][0-9]?|100)$/
		let msg = regType === 1 ? `0至100` : `1至100`
		if (value === null || value === '') {
			return errorMsg;
		} else if (!reg.test(value)) {
			return `请输入${msg}的整数`
		}
	},
	isFloatNum: function(value, errorMsg, regType, dotLen) {
		// dotLen 保留几位小数
		// regType 1大于0；2可为0 
		let regArr = {
			1: {
				1: /^(([1-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/,
				2: /^(([0-9][0-9]*)|(([0]\.\d{1}|[1-9][0-9]*\.\d{1})))$/
			},
			2: {
				1: /^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/,
				2: /^(([0-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/
			}
		}
		let reg = regArr[dotLen][regType]
		if (value == '') {
			return errorMsg;
		} else if (regType == 1 && value * 1 === 0) {
			return `${errorMsg}，不可为0`;
		} else if (!reg.test(value)) {
			return `${errorMsg}，最多${dotLen}位小数`;
		}
	},
	isIdCard: function(value, errorMsg) {
		var regIdCard =
			/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;
		if (value == null || value.length < 1) {
			return errorMsg;
		}
		if (regIdCard.test(value)) {
			if (value.length == 18) {
				var idCardWi = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4,
					2); //将前17位加权因子保存在数组里
				var idCardY = new Array(1, 0, 10, 9, 8, 7, 6, 5, 4, 3,
					2); //这是除以11后，可能产生的11位余数、验证码，也保存成数组
				var idCardWiSum = 0; //用来保存前17位各自乖以加权因子后的总和
				for (var i = 0; i < 17; i++) {
					idCardWiSum += value.substring(i, i + 1) * idCardWi[i];
				}
				var idCardMod = idCardWiSum % 11; //计算出校验码所在数组的位置
				var idCardLast = value.substring(17); //得到最后一位身份证号码
				//如果等于2，则说明校验码是10，身份证号码最后一位应该是X
				if (idCardMod == 2) {
					if (idCardLast == "X" || idCardLast == "x") {} else {
						return `${value} 身份证号码最后一位应为X`;
					}
				} else {
					//用计算出的验证码与最后一位身份证号码匹配，如果一致，说明通过，否则是无效的身份证号码
					if (idCardLast == idCardY[idCardMod]) {} else {
						return `${value} 身份证号码无效`;
					}
				}
			}
		} else {
			return `${value} 身份证号码无效`;
		}
	}
};
class Validate {
	constructor() {
		this.cache = []
	}
}
Validate.prototype.add = function(value, item = {}) {
	let {
		checkType,
		errorMsg,
		length,
		regType = 1,
		regText,
		dotLen = 2
	} = item
	this.cache.push(function() {
		// 规则
		let method, arr;
		//判断为已有的策略还是新增的
		if (typeof checkType === 'string') {
			arr = checkType.split(':');
			let strategy = arr.shift();
			method = strategies[strategy];
		} else {
			arr = [];
			method = checkType;
		}
		arr.unshift(value);
		arr.push(errorMsg);
		let lengthArr = ['minLength', 'maxLength']
		if (lengthArr.includes(checkType) && length) {
			arr.push(length);
		}
		let regTypeArr = ['isNotNull', 'isAllPhone', 'isMoney', 'isFloatNum', 'isNumber']
		if (regTypeArr.includes(checkType)) {
			arr.push(regType);
		}
		let regTextArr = ['isAllPhone']
		if (regTextArr.includes(checkType)) {
			arr.push(regText);
		}
		let dotLenArr = ['isFloatNum']
		if (dotLenArr.includes(checkType)) {
			arr.push(dotLen);
		}
		return method.apply(null, arr);
	});
};

Validate.prototype.start = function() {
	for (let i = 0, validatorFunc; validatorFunc = this.cache[i++];) {
		let msg = validatorFunc();
		if (msg) {
			return msg;
		}
	}
};

export default Validate

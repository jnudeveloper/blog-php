<?php
/**
 * Created by PhpStorm.
 * User: michael.shi
 * Date: 2017/12/18
 * Time: 20:11
 */

namespace common\util;


class Code{
    //正常
    const OK  					=  10000;			//正常
    //公用错误
    const ERROR					= -10000;			//未知错误类型
    const VERIFY_FAIL			= -10001;           //验证错误
    const PARAM_ERR				= -10002;           //参数错误
    const NO_RECORD				= -10003;           //找不到记录
    const CONF_MISS				= -10004;			//配置缺失
    const SMS_FAIL              = -10005;           //短信发送失败
    const THRIFT_FAIL           = -10006;           //thrift调用失败
    const EMAIL_FAIL            = -10007;           //邮件发送失败
    const NEED_LOGIN            = -10008;           //需要登录
    const DB_ERROR              = -10009;           //数据库错误
    const BAD_SERVICE           = -10010;           //service层调用异常
    const SEARCH_ENGINE_ERROR   = -10011;           //搜索引擎服务异常
    const CACHE_WRITE_ERROR     = -10012;           //写入数据到cache失败
    const ILLEGAL_OP            = -10013;           //非法操作
    const ILLEGAL_REQUEST       = -10014;           //非法请求

//    const BAD_ACCOUNT_TYPE      = -20000;           //错误的帐号类型
//    const BAD_USER_INFO         = -20001;           //错误的用户信息
//    const TOKEN_EXPIRED         = -20002;           //授权token已经过期
//    const AUTH_TOKEN_NEEDED     = -20003;           //需要提供授权token
//    const ACCESS_TOEKN_FAIL     = -20004;           //access token 分配失败
//    const AUTH_TOKEN_FAIL       = -20005;           //auth token分配失败
//    const RELOGIN               = -20006;           //重复登录
//    const FORM_ERROR            = -20007;           //表单数据有误
//    const W_COOKIE_E            = -20008;           //cookie 写入失败
//    const WRONG_PASSWORD        = -20009;           //帐号密码错误
//    const PHONE_BIND            = -20010;           //已经绑定了手机号
//    const PHONE_EXISTS          = -20011;           //帐号已经绑定
//    const ACCOUNT_FORBID        = -20012;           //帐号被禁用
//    const ACCOUNT_LOCK          = -20013;           //帐号被锁定
//    const EMAIL_EXISTS          = -20014;           //邮箱已绑定到其他用户
//    const EMAIL_BIND            = -20015;           //用户已经绑定了邮箱
//    const Err_TOKEN_INFO        = -20016;           //刷新token错误
//    const SECRETKEY_ERROR       = -20017;           //请求来源或用户IP校验失败
//
//    //用户帐号错误码
//    const USER_IS_SIGNUPED      = -30000;           //用户已经在用户中心注册
//    const USER_IS_UNNORMAL      = -30001;           //异常用户
//    const USER_NOT_EXISTS       = -30002;           //用户不存在
//    const USER_REGISTER_ERROR   = -30003;           //用户注册失败
//    const MOBILE_INVALID        = -30004;           //手机号格式错误
//    const EMAIL_INVALID         = -30005;           //邮件不可用
}
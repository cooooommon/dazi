/**
 * Created by guohongjun on 2018/4/18.
 * 用户相关api
 */
import CryptoJS from 'crypto-js'
/* utf.js - UTF-8 <=> UTF-16 convertion
     *
     * Copyright (C) 1999 Masanao Izumo <iz@onicos.co.jp>
     * Version: 1.0
     * LastModified: Dec 25 1999
     * This library is free. You can redistribute it and/or modify it.
     */
/*
  * Interfaces:
  * utf8 = utf16to8(utf16);
  * utf16 = utf8to16(utf8);
  */

function utf16to8 (str) {
  var out, i, len, c
  out = ''
  len = str.length
  for (i = 0; i < len; i++) {
    c = str.charCodeAt(i)
    if ((c >= 0x0001) && (c <= 0x007F)) {
      out += str.charAt(i)
    } else if (c > 0x07FF) {
      out += String.fromCharCode(0xE0 | ((c >> 12) & 0x0F))
      out += String.fromCharCode(0x80 | ((c >> 6) & 0x3F))
      out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F))
    } else {
      out += String.fromCharCode(0xC0 | ((c >> 6) & 0x1F))
      out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F))
    }
  }
  return out
}

/*
  * Interfaces:
  * b64 = base64encode(data);
  * data = base64decode(b64);
  */
var base64EncodeChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_'

function base64encode (str) {
  var out, i, len
  var c1, c2, c3
  len = str.length
  i = 0
  out = ''
  while (i < len) {
    c1 = str.charCodeAt(i++) & 0xff
    if (i === len) {
      out += base64EncodeChars.charAt(c1 >> 2)
      out += base64EncodeChars.charAt((c1 & 0x3) << 4)
      out += '=='
      break
    }
    c2 = str.charCodeAt(i++)
    if (i === len) {
      out += base64EncodeChars.charAt(c1 >> 2)
      out += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4))
      out += base64EncodeChars.charAt((c2 & 0xF) << 2)
      out += '='
      break
    }
    c3 = str.charCodeAt(i++)
    out += base64EncodeChars.charAt(c1 >> 2)
    out += base64EncodeChars.charAt(((c1 & 0x3) << 4) | ((c2 & 0xF0) >> 4))
    out += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >> 6))
    out += base64EncodeChars.charAt(c3 & 0x3F)
  }
  return out
}
var safe64 = function (base64) {
  base64 = base64.replace(/\+/g, '-')
  base64 = base64.replace(/\//g, '_')
  return base64
}

var genUpToken = function (accessKey, secretKey, putPolicy) {
  // SETP 2
  var resPutPolicy = JSON.stringify(putPolicy)
  // SETP 3
  var encoded = base64encode(utf16to8(resPutPolicy))
  // SETP 4
  var hash = CryptoJS.HmacSHA1(encoded, secretKey)
  var encodedSigned = hash.toString(CryptoJS.enc.Base64)
  // SETP 5
  var uploadToken = accessKey + ':' + safe64(encodedSigned) + ':' + encoded
  return uploadToken
}
export {
  genUpToken
}

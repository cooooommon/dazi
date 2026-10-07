/*
 * @Descripttion:
 * @Author: xiao li
 * @Date: 2020-07-06 12:17:06
 * @LastEditors: wen kun
 * @LastEditTime: 2023-12-13 15:01:11
 */
import base from './base'
import mall from './mall'
import survey from './survey'
import service from './service'
import technician from './technician'
import market from './market'
import shop from './shop'
import distribution from './distribution'
import channel from './channel'
import agent from './agent'
import finance from './finance'
import dynamic from './dynamic'
import store from './store'
import custom from './custom'
import notice from './notice'
import account from './account'
import invitation from './invitation'
import system from './system'
import upload from './upload'
import storeshop from './storeshop'
import diy from './diy'
import economy from './economy'
import memberdiscount from './memberdiscount'

let modules = {
  base, survey, mall, service, technician, market, shop, distribution, channel, agent, finance, dynamic, store, custom, notice, account, invitation, system, upload, storeshop, diy, economy, memberdiscount
}
export default {
  ...modules
}

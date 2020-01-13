'use strict';
import {Utils} from '../util'

let utils = new Utils();

export const actions = {
  /**
   * 通过手机验证码获取jwt token
   * @param commit
   * @param state
   * @param verify_payload
   */
  getAuthToken ({commit, state}, verify_payload) {
    return new Promise((resolve, reject) => {
      utils.post('/auth/verifysms', {phone_number: verify_payload.phone, verify_number: verify_payload.code, unbind: verify_payload.unbind}).then(res => {
        resolve(res.data);
      }).catch(res => {
        reject(res);
      })
    });
  },

  /**
   * 发送验证短信
   * @param commit
   * @param state
   * @param sendsms_payload
   * @returns {Promise}
   */
  sendSMS ({commit, state}, sendsms_payload) {
    return new Promise((resolve, reject) => {
      utils.post('/auth/sendsms', {phone: sendsms_payload.phone}).then(res => {
        resolve(res);
      }).catch(res => {
        reject(res);
      });
    })
  },

  /**
   * 初始化微信接口
   * @param commit
   * @param state
   * @param sign_url
   */
  getWeChatSign ({commit, state}, sign_url) {
    return new Promise((resolve, reject) => {
      utils.post('/base/wechat', {
        sign_url: sign_url
      }).then(res => {
        resolve(res);
      }).catch(res => {
        reject({status: res.status, res: res})
      });
    })
  }
};

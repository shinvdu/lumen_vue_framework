import {app} from '../app'

const configPath = '/api';

export class Utils {
  get (url, data = {}) {
    url = configPath + url;
    return new Promise((resolve, reject) => {
      app.$http.get(url, {params: data}).then((response) => {
        resolve(response.body)
      }).catch((response) => {
        this.checkResponse(response);
        reject(response.data);
      })
    })
  }

  post (url, body = {}) {
    url = configPath + url;
    return new Promise((resolve, reject) => {
      app.$http.post(url, body).then((response) => {
        resolve(response.body)
      }).catch((response) => {
        this.checkResponse(response);
        reject(response.data);
      })
    })
  }

  put (url, body = {}) {
    url = configPath + url;
    return new Promise((resolve, reject) => {
      app.$http.put(url, body).then((response) => {
        resolve(response.body)
      }).catch((response) => {
        this.checkResponse(response);
        reject(response.data);
      })
    })
  }

  deleteApi (url, body = {}) {
    url = configPath + url;
    return new Promise((resolve, reject) => {
      app.$http.delete(url, body).then((response) => {
        resolve(response.body)
      }).catch((response) => {
        this.checkResponse(response);
        reject(response.data);
      })
    })
  }

  // 设置cookie
  // ex大于1000表示设置毫秒，否则设置天数
  setCookie (cname, cvalue, ex) {
    let d = new Date();
    if (ex < 1000) {
      d.setTime(d.getTime() + (ex * 24 * 60 * 60 * 1000));
    } else {
      d.setTime(d.getTime() + ex);
    }
    let expires = 'expires=' + d.toUTCString();
    document.cookie = cname + '=' + cvalue + '; ' + expires;
  }

  getCookie (cname) {
    var name = cname + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) === ' ') c = c.substring(1);
      if (c.indexOf(name) !== -1) {
        let rs = c.substring(name.length, c.length);
        if (rs && rs !== 'undefined') { return rs; }
      }
    }
    return false;
  }

  clearCookie (name) {
    this.setCookie(name, '', -1);
  }
  checkResponse (response) {
    if (response.status === 500) {
      if (response.data) {
        response.data.message = '服务器发生错误，请稍后重试';
      }
    }
    if (response.status === 0) {
      console.log(response.status);
      response.data = '当前网络条件不好，您现在无法提交.';
    }
    return response;
  }
}

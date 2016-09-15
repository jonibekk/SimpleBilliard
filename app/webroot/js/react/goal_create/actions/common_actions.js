import axios from "axios";
import qs from "qs"

// TODO:いずれreact全体の共通処理として配置(js/react/common/**)

export function getBaseUrl() {
  // テストにおけるモックのURLを定義
  if (typeof cake === "undefined") {
    return 'http://localhost'
  } else {
    return cake.url.route_url
  }
}

export function getCsrfTokenKey() {
  if (typeof cake === "undefined") {
    return 'csrf_token_key'
  } else {
    return cake.data.csrf_token.key
  }
}

export function post(uri, data, options, success_callback, error_callback) {
  options = options || {};
  const csrfToken = getCsrfTokenKey()
  const postData = Object.assign(data, {
    data: {
      _Token: {
        key:csrfToken
      }
    }
  })

  const url = getBaseUrl() + uri;
  return axios.post(url, qs.stringify(postData), options)
    .then(success_callback, error_callback)
}

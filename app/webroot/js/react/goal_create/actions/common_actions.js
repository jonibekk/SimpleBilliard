import axios from "axios";
import FormData from 'form-data'

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
  options = options || {}
  const base_options = {
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    },
    dataType: 'json'
  }
  options = Object.assign(base_options, options)

  /* Create request parameter */
  const csrf_token_key = getCsrfTokenKey()
  const post_data = Object.assign({
    'data[_Token][key]': csrf_token_key
  }, data)
  let form_data = new FormData()
  for (const key in post_data) {
    if (Array.isArray(post_data[key])) {
      form_data.append(`${key}[]`, post_data[key])
    } else {
      form_data.append(key, post_data[key])
    }
  }

  const url = getBaseUrl() + uri;
  return axios.post(url, form_data, options)
    .then(success_callback, error_callback)
}

// TODO:
// ・dev環境の場合basic認証突破
// ・ログイン情報を外から設定
// ・csrfチェック突破

// ES6で書きたいけど、jasmin-nodeでES6で書く為には
// ゴニョゴニョしなくちゃいけないからとりあえずES5で書く

var frisby = require("frisby"),
  common = require("./common"),
  qs = require("querystring"),
  readDir = require('fs-readdir-recursive'),
  config = require('./config.json');

var env = common.getEnv(process.argv);
var baseUrl = config[env].baseUrl;
var caseDir = "./cases/";
var filePaths = readDir(caseDir);

frisby.create("login before test")
  .post(baseUrl + "/api/v1/users/login/"
    , {"email": "yoshidam2@isao.co.jp", "password": "ISAOisao1"}
  )
  .after(function (body, res) {
    var cookie = res.headers['set-cookie'][0].split(';')[0];
    console.log("cookie:" + cookie);

    for (var fpIdx in filePaths) {
      var filePath = filePaths[fpIdx];
      // テストケースグループ読み込み
      var test = require(caseDir + filePath);

      // 各テストケース実行
      for (var cIdx in test.cases) {
        var testCase = test.cases[cIdx];
        var apiUrl = baseUrl + test.url;
        console.log(apiUrl);
        // テストケース名設定
        var f = frisby.create(testCase.name);

        /* リクエスト設定 */
        // リクエストヘッダー設定
        // 要ログインのAPIであればCookie設定
        if (test.needLogin) {
          f.addHeader("Cookie", cookie);
        }
        // リクエストメソッド・パラメータ設定
        switch (test.requestMethod) {
          case "GET":
            if (testCase.requestParameter) {
              f.get(apiUrl + "?" + qs.stringify(testCase.requestParameter));
            } else {
              f.get(apiUrl);
            }
            break;
          case "POST":
            f.post(apiUrl, testCase.requestParameter);
            break;
          case "PUT":
            f.put(apiUrl, testCase.requestParameter);
            break;
          case "DELETE":
            f.delete(apiUrl, testCase.requestParameter);
            break;
        }

        /* レスポンス期待値設定 */
        f.expectStatus(testCase.expectStatus);
        if (testCase.expectJSONTypes) {
          if (testCase.expectJSONTypes.path) {
            f.expectJSONTypes(testCase.expectJSONTypes.path, testCase.expectJSONTypes.json);
          } else {
            f.expectJSONTypes(testCase.expectJSONTypes.json);
          }
        }
        if (testCase.expectJSONLength) {
          if (testCase.expectJSONLength.path) {
            f.expectJSONLength(testCase.expectJSONLength.path, testCase.expectJSONLength.length);
          } else {
            f.expectJSONLength(testCase.expectJSONLength.length);
          }
        }
        f.toss();
      }
    }
  })
  .toss();



// TODO:dev環境の場合basic認証突破
/*
例
frisby.create('Basic認証を突破！')
  .auth('authname', 'authpassword')
  .get('http://animemap.net/api/table/tokyo.json')
  .toss();
*/

// ES6で書きたいけど、jasmin-nodeでES6で書く為には
// ゴニョゴニョしなくちゃいけないからとりあえずES5で書く

var frisby = require("frisby"),
  common = require("./common"),
  qs = require("querystring"),
  readDir = require('fs-readdir-recursive'),
  config = require('./config.json');

// 環境のURL取得
var env = common.getEnv(process.argv);
var baseUrl = config[env].baseUrl;
var filePaths = readDir("./cases");



for (var fpIdx in filePaths) {
  var filePath = filePaths[fpIdx];
  // テストケースグループ読み込み
  var test = require("./" + path);
  var test = require("./cases/users/api1");

  // 各テストケース実行
  for (var cIdx in test.cases) {
    var testCase = test.cases[cIdx];
    var apiUrl = baseUrl + test.url;
    var f = frisby.create(testCase.name);
    switch (test.requestMethod){
      case "GET":
        if (testCase.requestParameter) {
          f.get(apiUrl + "&" + qs.stringify(testCase.requestParameter));
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
    f.expectStatus(testCase.expectStatus);
    f.toss();
  }
}


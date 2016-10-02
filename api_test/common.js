module.exports = {
  /**
   * 環境取得
   *
   * @param args
   * @returns {string}
   */
  getEnv: function (args) {
    var idx = args.indexOf('ENV');
    if (idx == -1 || !args[idx + 1]) {
      return "local";
    }
    if (args[idx + 1] == "dev") {
      return "dev";
    }
    return "local";
  },
  /**
   * ランダム文字列作成
   *
   * @param length
   * @returns {string}
   */
  makeRandStr: function (length) {
    // 生成する文字列の長さ
    length = length || 20;
    // 生成する文字列に含める文字セット
    var charset = "abcdefghijklmnopqrstuvwxyz0123456789";
    var cl = charset.length;
    var str = "";
    for (var i = 0; i < l; i++) {
      str += charset[Math.floor(Math.random() * cl)];
    }
    return str;
  }
}

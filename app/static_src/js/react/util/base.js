/**
 * オブジェクトか判定
 * @param val
 * @returns {boolean}
 */
export function isObject(val) {
  return Object.prototype.toString.call(val) == "[object Object]";
}

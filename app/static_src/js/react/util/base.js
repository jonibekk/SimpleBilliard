/**
 * オブジェクトか判定
 * @param val
 * @returns {boolean}
 */
export function isObject(val) {
  return Object.prototype.toString.call(val) == "[object Object]";
}

/**
 * Format file size
 * @param size
 * @returns array
 */
export function formatFileSize(size) {
  const units = ['TB', 'GB', 'MB', 'KB', 'b'];
  let selectedSize = null;
  let selectedUnit = null;
  for (let i = 0; i < units.length; i++) {
    let unit = units[i];
    let cutoff = Math.pow(1000, 4 - i) / 10;
    if (size >= cutoff) {
      selectedSize = size / Math.pow(1000, 4 - i);
      selectedUnit = unit;
      break;
    }
  }
  selectedSize = Math.round(10 * selectedSize) / 10;
  const ret = [selectedSize, selectedUnit];
  return ret;
}




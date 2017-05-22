import { MonthNameListEn } from "~/common/constants/Date";

export function generateCurrentRange() {
  const current_start_date = new Date()
  let current_range = []

  for (let i = 0; i < 12; i++) {
    const current_end_date = new Date(current_start_date.getFullYear(), parseInt(current_start_date.getMonth()) + i)
    const next_start_date = new Date(current_end_date.getFullYear(), parseInt(current_end_date.getMonth()) + i)
    const range = generateTermRangeFormat(current_start_date, current_end_date);

    current_range.push({
      // TODO: getMonth()メソッドは0~11の値を返すので、正しい数値を取得するためには+1する必要がある。
      //       毎回これを気にするのはつらいので、ラッパーを作る。
      next_start_ym: `${next_start_date.getFullYear()}-${toDigit(parseInt(next_start_date.getMonth()) + 1)}`,
      range
    })
  }
  console.log(current_range)
  return current_range
}

/**
 * termを元に、現在日時から評価開始年月の一覧を生成する
 *
 * @param  integer term 3|4|6|12
 * @return start_month_list
 */
export function generateNextRange(current_end_date) {
  const next_start_date = new Date(current_end_date.getFullYear(), current_end_date.getMonth() + 1)
  let next_range = []

  for ( const term_length of [3, 6, 12] ) {
    const next_end_date = new Date(next_start_date.getFullYear(), next_start_date.getMonth() + term_length)
    const range = generateTermRangeFormat(next_start_date, next_end_date);
    next_range.push({
      // TODO: getMonth()メソッドは0~11の値を返すので、正しい数値を取得するためには+1する必要がある。
      //       毎回これを気にするのはつらいので、ラッパーを作る。
      term_length,
      range
    })
  }

  return next_range
}

/**
 * 期間を表示用にフォーマット
 * 「2016/4/1」, 「2016/9/30」 -> 「04月 1, 2016 - 09月 30, 2016」
 *
 * @param  date start_month
 * @param  date end_month
 * @return formatted
 */
export function generateTermRangeFormat(start_month, end_month) {
  start_month = new Date(start_month)
  end_month = new Date(end_month)
  const formatted_start_date = dateFormatYm(start_month.getFullYear(), parseInt(start_month.getMonth()) + 1)
  const formatted_end_date = dateFormatYm(end_month.getFullYear(), parseInt(end_month.getMonth()) + 1)
  const formatted = `${formatted_start_date} - ${formatted_end_date}`
  return formatted
}

/**
 * 年月を表示用にフォーマット
 * 「2016/4/1」->「04月 1, 2016」
 * @param  integer year
 * @param  integer month
 * @param  integer day
 */
export function dateFormatYm(year, month) {
  const double_digit_month = toDigit(month)
  // TODO: ログイン前とログイン後でlang設定の表記揺れがあるので、表記揺れをなくす。
  //       ログイン前だと日本語の場合'ja', ログイン後だと'jpn'が格納されている。
  if (cake.lang === 'ja' || cake.lang === 'jpn') {
    return `${year}年${double_digit_month}月`
  } else {
    return `${MonthNameListEn[double_digit_month]} ${year}`
  }
}

export function toDigit(number) {
  return ('00' + number).slice(-2);
}

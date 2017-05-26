import { MonthNameListEn } from "~/common/constants/Date";

export function generateCurrentRangeList() {
  const current_start_date = new Date()
  let current_range_list = []

  for (let i = 0; i < 12; i++) {
    const current_end_date = dateMonthAfter(current_start_date, i)
    const next_start_date = dateMonthAfter(current_end_date, 1)
    const range = generateTermRangeFormat(current_start_date, current_end_date)

    current_range_list.push({
      next_start_ym: `${year(next_start_date)}-${toDigit(month(next_start_date))}`,
      range
    })
  }
  return current_range_list
}

/**
 * termを元に、現在日時から評価開始年月の一覧を生成する
 *
 * @param  integer term 3|4|6|12
 *
 * @return start_month_list
 */
export function generateNextRangeList(next_start_date) {
  let next_range_list = []

  for (const term_length of [3, 6, 12]) {
    const next_end_date = dateMonthAfter(next_start_date, term_length - 1);
    let range = generateTermRangeFormat(next_start_date, next_end_date)
    range = `${range} (${__(term_length + ' months')})`
    next_range_list.push({
      term_length,
      range
    })
  }

  return next_range_list
}

/**
 * 期間を表示用にフォーマット
 * 「2016/4/1」, 「2016/9/30」 -> 「04月 1, 2016 - 09月 30, 2016」
 *
 * @param  date start_month
 * @param  date end_month
 *
 * @return formatted
 */
export function generateTermRangeFormat(start_month, end_month) {
  start_month = new Date(start_month)
  end_month = new Date(end_month)
  const formatted_start_date = dateFormatYm(year(start_month), month(start_month))
  const formatted_end_date = dateFormatYm(year(end_month), month(end_month))
  const formatted = `${formatted_start_date} - ${formatted_end_date}`
  return formatted
}

/**
 * 年月を表示用にフォーマット
 *
 * @param  integer year
 * @param  integer month
 *
 * @return string
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

/**
 * get date after n months
 *
 * @param  string  date
 * @param  integer addNumber
 *
 * @return string
 */
export function dateMonthAfter(date, addNumber) {
  return new Date(year(date), month(date) - 1 + addNumber)
}

/**
 * get month by date object
 *
 * @param  string  date
 *
 * @return integer
 */
export function month(date) {
  return parseInt(date.getMonth()) + 1
}

/**
 * get year by date object
 *
 * @param  string date
 *
 * @return integer
 */
export function year(date) {
  return parseInt(date.getFullYear())
}

/**
 * to digit number
 * 1 -> 01, 10 -> 10
 *
 * @param  integer number
 *
 * @return integer
 */
export function toDigit(number) {
  return ('00' + number).slice(-2);
}

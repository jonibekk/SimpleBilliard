import { MonthNameListEn } from "~/common/constants/Date";

/**
 * termを元に、現在日時から評価開始年月の一覧を生成する
 *
 * @param  integer term 3|4|6|12
 * @return start_month_list
 */
export function generateStartMonthList(term) {
  const date = new Date()
  const this_month = date.getMonth()
  const this_year = date.getFullYear()
  const start_month_list = []

  for(let i = parseInt(term); i > 0; i--) {
    const start_date = new Date(this_year, this_month - (i - 1))
    const end_date = new Date(this_year, (this_month - i) + parseInt(term))
    const range = generateTermRangeFormat(start_date, end_date);
    const next_start_date = new Date(end_date.getFullYear(), parseInt(end_date.getMonth() + 1))

    start_month_list.push({
      next_start_ym: `${next_start_date.getFullYear()}-${toDigit(parseInt(next_start_date.getMonth()) + 1)}`,
      range
    })
  }

  return start_month_list
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
  if (cake.lang === 'jpn' || cake.lang === 'ja') {
    return `${year}年${double_digit_month}月`
  } else {
    return `${MonthNameListEn[double_digit_month]} ${year}`
  }
}

export function toDigit(number) {
  return ('00' + number).slice(-2);
}

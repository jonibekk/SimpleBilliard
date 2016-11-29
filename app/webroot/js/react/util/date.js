import { MonthName } from "~/common/constants/Date";

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
    const start_month = new Date(this_year, this_month - (i - 1), 1)
    const end_month = new Date(this_year, (this_month - (i - 1)) + parseInt(term), 0)
    const range = generateTermRangeFormat(start_month, end_month);

    start_month_list.push({
      start_month: parseInt(start_month.getMonth()) + 1,
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
  const formatted_start_date = dateFormat(start_month.getFullYear(), parseInt(start_month.getMonth()) + 1, 1)
  const formatted_end_date = dateFormat(end_month.getFullYear(), parseInt(end_month.getMonth()) + 1, end_month.getDate())
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
export function dateFormat(year, month, day) {
  const double_digit_month = ('00' + month).slice(-2);
  const translated_month_name = MonthName[double_digit_month]

  // 日本語の翻訳は月の数字が「１月」のように大文字+1桁になるため、
  // ここで「01月」を月名として代入する
  let month_name = ''
  if(translated_month_name.match(/月/)) {
    month_name = double_digit_month + '月'
  } else {
    month_name = translated_month_name
  }

  return `${month_name} ${day}, ${year}`
}

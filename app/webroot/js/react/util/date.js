import { MonthName } from "~/common/constants/Date";

export function generateStartMonthList(term) {
  const date = new Date()
  const this_month = date.getMonth()
  const this_year = date.getFullYear()
  const start_month_list = []

  for(let i = parseInt(term); i > 0; i--) {
    const start_month = new Date(this_year, this_month - (i - 1), 1)
    const formatted_start_date = dateFormat(start_month.getFullYear(), parseInt(start_month.getMonth()) + 1, 1)
    const end_month = new Date(this_year, (this_month - (i - 1)) + parseInt(term), 0)
    const formatted_end_date = dateFormat(end_month.getFullYear(), parseInt(end_month.getMonth()) + 1, end_month.getDate())

    start_month_list.push({
      start_month: parseInt(start_month.getMonth()) + 1,
      range: `${formatted_start_date} - ${formatted_end_date}`
    })
  }

  return start_month_list
}

export function dateFormat(year, month, day) {
  const double_digit_month = month < 10 ? "0" + String(parseInt(month)) : month
  const translated_month_name = MonthName[double_digit_month]
  let month_name = ''

  if(translated_month_name.match(/月/)) {
    month_name = double_digit_month + '月'
  } else {
    month_name = translated_month_name
  }

  return `${month_name} ${day}, ${year}`
}

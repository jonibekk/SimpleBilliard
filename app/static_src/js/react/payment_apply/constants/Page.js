export const COUNTRY = 'country'
export const COMPANY = 'company'
export const CREDIT_CARD = 'credit_card'
export const INVOICE = 'invoice'
export const CONFIRM = 'confirm'
export const COMPLETE = 'complete'

export const URL_COUNTRY = '/payments/apply'
export const URL_COMPANY = '/payments/apply/company'
export const URL_INVOICE = '/payments/apply/invoice'
export const URL_CREDIT_CARD = '/payments/apply/credit_card'
export const URL_COMPLETE = '/payments/apply/complete'

export const INITIAL_DATA_TYPES = {
  [COUNTRY]: [
    "countries",
    'lang_code'
  ],
  [CREDIT_CARD]: [
    "charge",
  ],
}

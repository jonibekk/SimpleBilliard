export const COUNTY = 'COUNTY'
export const COMPANY = 'COMPANY'
export const CREDIT_CARD = 'CREDIT_CARD'
export const COMPLETE = 'COMPLETE'

export const URL_COUNTY = '/payments/apply'
export const URL_COMPANY = '/payments/apply/company'
export const URL_INVOICE = '/payments/apply/invoice'
export const URL_CREDIT_CARD = '/payments/apply/credit_card'
export const URL_COMPLETE = '/payments/apply/compete'

export const VALIDATION_FIELDS = {
  COUNTY: [
    "county",
    "payment_type"
  ],
  COMPANY: [
    "goal_category_id",
    "labels",
  ],
  CREDIT_CARD: [
    ''
  ],
}
export const INITIAL_DATA_TYPES = {
  COUNTY: [
    "countries",
    'lang_code'
  ],
  CREDIT_CARD: [
    "terms",
    "priorities",
    "default_end_dates",
    "can_approve"
  ]
}

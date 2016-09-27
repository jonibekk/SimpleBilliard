export const STEP1 = 'STEP1'
export const STEP2 = 'STEP2'
export const STEP3 = 'STEP3'
export const STEP4 = 'STEP4'

export const URL_STEP1 = '/goals/create/step1'
export const URL_STEP2 = '/goals/create/step2'
export const URL_STEP3 = '/goals/create/step3'
export const URL_STEP4 = '/goals/create/step4'

export const PAGE_FLOW = [
  URL_STEP1,
  URL_STEP2,
  URL_STEP3,
  URL_STEP4
]
export const VALIDATION_FIELDS = {
  STEP1: [
    "name"
  ],
  STEP2: [
    "goal_category_id",
    "labels",
  ],
  STEP3: [
    "photo",
    "term_type",
    "description",
    "end_date",
    "priority",
  ],
}
export const INITIAL_DATA_TYPES = {
  STEP1: [
    "visions"
  ],
  STEP2: [
    "categories",
    "labels",
  ],
  STEP3: [
    "terms",
    "priorities",
    "default_end_dates"
  ],
  STEP4: [
    "units",
  ],
}

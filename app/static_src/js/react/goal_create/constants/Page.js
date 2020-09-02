export const STEP1 = 'STEP1'
export const STEP2 = 'STEP2'
export const STEP3 = 'STEP3'
export const STEP4 = 'STEP4'
export const STEP5 = 'STEP5'

export const URL_STEP1 = '/goals/create/step1'
export const URL_STEP2 = '/goals/create/step2'
export const URL_STEP3 = '/goals/create/step3'
export const URL_STEP4 = '/goals/create/step4'
export const URL_STEP5 = '/goals/create/step5'

export const PAGE_FLOW = [
  URL_STEP1,
  URL_STEP2,
  URL_STEP3,
  URL_STEP4,
  URL_STEP5
]

export const VALIDATION_FIELDS = {
  STEP1: [
    "name"
  ],
  STEP2: [
    "goal_category_id",
    "labels"
  ],
  STEP3: [
    "photo",
    "term_type",
    "description",
    "end_date",
    "priority",
    "terms"
  ],
  STEP4: [
    "key_result" // placeholder to trigger validation and move to next page
  ] 
}
export const INITIAL_DATA_TYPES = {
  STEP1: [
    "visions"
  ],
  STEP2: [
    "categories",
    "labels"
  ],
  STEP3: [
    "terms",
    "priorities",
    "default_end_dates",
    "can_approve"
  ],
  STEP4: [
    "units",
    "groups_enabled"
  ],
  STEP5: [ 
    "groups"
  ]
}

// バックエンドのモデルの定数をReactでも使う

export const KeyResult = Object.freeze({
  Priority: {
    DEFAULT: 3
  },
  ValueUnit: {
    NONE: 2
  }
})

export const Collaborator = Object.freeze({
  ApprovalStatus: {
    NEW: 0,
    REAPPLICATION:1,
    DONE:2,
    WITHDRAW:3
  },
  Type: {
    TYPE_COLLABORATOR: 0,
    OWNER: 1
  },
  Evaluation: {
    IS_NOT_TARGET: 0,
    IS_TARGET: 1
  },
  // 第一フェーズではリスト全件表示の仕様のためこれは使わない
  NUMBER_OF_DISPLAY_LIST_CARD: 10
})

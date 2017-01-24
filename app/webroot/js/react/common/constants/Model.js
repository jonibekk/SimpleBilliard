// バックエンドのモデルの定数をReactでも使う

export const KeyResult = Object.freeze({
  Priority: {
    DEFAULT: 3
  },
  ValueUnit: {
    NONE: 2
  },
  MAX_LENGTH_VALUE: 15,
  // TODO: 右カラムトップにグラフが追加され、かつ右カラムだけのスクロールが実装されたら
  //       KR取得件数を20件->10件に変更する
  DASHBOARD_LIMIT: 20
})

export const GoalMember = Object.freeze({
  ApprovalStatus: {
    NEW: 0,
    REAPPLICATION: 1,
    DONE: 2,
    WITHDRAWN: 3
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

export const TopKeyResult = Object.freeze({
  IS_CLEAR: 1,
  IS_NOT_CLEAR: 2,
  IS_IMPORTANT: 1,
  IS_NOT_IMPORTANT: 2
})

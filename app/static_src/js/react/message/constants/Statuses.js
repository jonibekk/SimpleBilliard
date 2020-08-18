export const SearchType = Object.freeze({
  MESSAGES: 'messages',
  TOPICS: 'topics',
})


export const TopicTitleSettingStatus = Object.freeze({
  NONE: 0,
  EDITING: 1,
  SAVING: 2,
  SAVE_SUCCESS: 3,
  SAVE_ERROR: 4
})

export const FetchMoreMessages = Object.freeze({
  NONE: 0,
  LOADING: 1,
  SUCCESS: 2,
  ERROR: 3,
})

export const JumpToLatest = Object.freeze({
  NONE: 0,
  VISIBLE: 1,
  DONE: 2,
})


export const SaveMessageStatus = Object.freeze({
  NONE: 0,
  SAVING: 1,
  SUCCESS: 2,
  ERROR: 3,
})

export const LeaveTopicStatus = Object.freeze({
  NONE: 0,
  SAVING: 1,
  SUCCESS: 2,
  ERROR: 3,
})

export const FetchLatestMessageStatus = Object.freeze({
  NONE: 0,
  LOADING: 1,
  SUCCESS: 2,
  ERROR: 3,
})

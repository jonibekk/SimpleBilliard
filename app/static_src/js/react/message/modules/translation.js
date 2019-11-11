import {get} from "~/util/api";

const TRANSLATION_API_PATH = '/api/v1/translations';

export function translateMessage(messageId) {
  return get(TRANSLATION_API_PATH + '?type=5&id=' + messageId)
    .then((response) => {
      return response.data.data.translation;
    })
    .catch(() => {
      return '';
    });
}

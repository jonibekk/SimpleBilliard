import React from "react";

/**
 * 改行コードをbrタグに変換したComponentを返す
 * @param text
 * @returns {*}
 */
export function nl2br(text) {
  if (text === undefined) {
    return null;
  }
  if (!text) {
    return null;
  }
  const regex = /(\n)/g
  const lines =  text.split(regex).map(function (line, i) {
    if (line.match(regex)) {
      return <br key={i} />
    }
    else {
      return line;
    }
  });
  return lines;
}

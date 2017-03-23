import React from 'react'
import { Link } from "react-router";

export default class Topic extends React.Component {

  render() {
    const topic = this.props.topic
    return (
      <li className="topicList-item" key={ topic.id }>
        <Link to={ `/topics/${topic.id}/detail` }  className="topicList-item-link">
          <div className="avatorsBox">
            { topic.users.map((user, i) => {
              let size = ''
              if (topic.users.length > 3) {
                size = 'quarter'
              }
              if (topic.users.length == 3) {
                size = (i == 0) ? 'half' : 'quarter'
              }
              if (topic.users.length == 2) {
                size = 'half'
              }
              if (topic.users.length == 1) {
                size = 'one'
              }
              return (
                <div className={`avatorsBox-${size}`} key={ user.id }>
                  <img src={ user.medium_large_img_url } />
                </div>
              )
            })}
          </div>
          <div className="topicList-item-main">
            <div className="topicList-item-main-header">
              <div className="topicList-item-main-header-title oneline-ellipsis">
                <span>
                  { topic.display_title }
                </span>
              </div>
              <div className="topicList-item-main-header-count">
                { topic.members_count > 2 && `(${topic.members_count})` }
              </div>
            </div>
            <div className="topicList-item-main-body oneline-ellipsis">
              { topic.latest_message.body }
            </div>
            <div className="topicList-item-main-footer">
              {(() => {
                if ((topic.members_count - 1) == topic.read_count) {
                  return <span><i className="fa fa-check is-read"></i> </span>
                } else if (topic.members_count == 2) {

                } else {
                  return <span><i className="fa fa-check"></i> { topic.read_count }・</span>
                }
              })()}
              { topic.latest_message.display_created }
            </div>
          </div>
        </Link>
      </li>
    )
  }
}

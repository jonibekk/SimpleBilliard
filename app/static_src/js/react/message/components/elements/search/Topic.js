import React from 'react'
import { Link } from "react-router";
import AvatarsBox from "~/common/components/AvatarsBox";

export default class Topic extends React.Component {
  render() {
    const topic = this.props.topic
    return (
      <li className="topicSearchList-item" key={ topic.id }>
        <Link to={ `/topics/${topic.id}/detail` }  className="topicSearchList-item-link">
          <AvatarsBox users={ topic.users } />
          <div className="topicSearchList-item-main">
            <div className="topicSearchList-item-main-header">
              <div className="topicSearchList-item-main-header-title">
                { topic.display_title }
              </div>
              <div className="topicSearchList-item-main-header-count">
              </div>
            </div>
            <div className="topicSearchList-item-main-body">
              { topic.latest_message.body }
            </div>
          </div>
          <div className="topicSearchList-item-right">
            { topic.latest_message.display_created }
          </div>
        </Link>
      </li>
    )
  }
}

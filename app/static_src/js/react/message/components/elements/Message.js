import React from 'react'

// TODO:Dynamic embedding of message information
export default class Message extends React.Component {
  render() {
    return (
      <div className="topicDetail-messages-item">
        <div className="topicDetail-messages-item-left">
          <a href="/users/view_goals/user_id:442" className="topicDetail-messages-item-left-profileImg">
            <img className="lazy"
                 src="https://goalous-release2-assets.s3.amazonaws.com/users/441/e6dbe975a33ec9e8141392eecdf2964d_medium.jpeg?AWSAccessKeyId=AKIAJHXVNZZEOX3TD5BA&Expires=1489042271&Signature=kNwNlW%2Fc70zNIJZ6AEj%2B43AgV6g%3D"/>
          </a>
        </div>
        <div className="topicDetail-messages-item-right">
          <div className>
            <a href="/users/view_goals/user_id:441" className="topicDetail-messages-item-userName">
              <span>鳥居 浩行</span>
            </a>
            <span className="topicDetail-messages-item-datetime">
                  03/08 16:37
                </span>
          </div>
          <p className="topicDetail-messages-item-content">
            高くても良いものを！
          </p>
          <div className>
            <a href="#" className="topicDetail-messages-item-read is-on">
              <i className="fa fa-check"/>
            </a>
          </div>
        </div>
      </div>    )
  }
}

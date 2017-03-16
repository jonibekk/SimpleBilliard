import React from "react";
import {Link} from "react-router";
import Messages from "./elements/Messages";
import TopicHeader from "./elements/TopicHeader";

export default class Detail extends React.Component {
  constructor(props) {
    super(props)
  }

  componentWillMount() {
    this.props.fetchInitialData(this.props.params.topic_id)
  }

  render() {
    const {is_fetched_initial, topic, messages, loading_more} = this.props.detail


    return (
      <div className="panel panel-default topicDetail">
        <TopicHeader topic={topic}/>
        <div className="topicDetail-body">
          <Messages
            messages={messages.data}
            paging={messages.paging}
            loading_more={loading_more}
            is_fetched_initial={is_fetched_initial}
          />
        </div>
        <div className="topicDetail-footer">
          <form name className>
            <div className="topicDetail-footer-box">
              <div className="topicDetail-footer-box-left">
                <button type="button" className="btn btnRadiusOnlyIcon mod-upload">
                </button>
              </div>
              <div className="topicDetail-footer-box-center">
                <textarea className="form-control disable-change-warning" rows={1} placeholder="Reply" cols={30}
                          name="message_body" defaultValue={""}/>
                <div className="has-error">
                  <span className="has-error help-block">
                    We have exceeded the maximum number of characters (5,000).
                  </span>
                </div>
              </div>
              <div className="topicDetail-footer-box-right">
                <button className="btn btnRadiusOnlyIcon mod-like" type="button">
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    )
  }
}

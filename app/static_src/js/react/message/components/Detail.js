import React from "react";
import {Link} from "react-router";
import Messages from "./elements/Messages";
import TopicHeader from "./elements/TopicHeader";

export default class Detail extends React.Component {
  constructor(props) {
    super(props);
  }

  componentWillMount() {
    // Set resource ID included in url.
    this.props.setResourceId(this.props.params.topic_id);
    this.props.fetchInitialData(this.props.params.topic_id);
  }

  sendLike(e) {
    this.props.sendLike();
  }

  sendMessage(e) {
    this.props.sendMessage();
  }

  onChangeMessage(e) {
    this.props.onChangeMessage(e.target.value);
  }

  render() {
    const props = this.props.detail;
    return (
      <div className="panel panel-default topicDetail">
        <TopicHeader topic={props.topic}/>
        <div className="topicDetail-body">
          <Messages
            messages={props.messages.data}
            paging={props.messages.paging}
            loading_more={props.loading_more}
            is_fetched_initial={props.is_fetched_initial}
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
                <textarea
                  className="form-control disable-change-warning"
                  rows={1} cols={30} placeholder={__("Reply")}
                  name="message_body" defaultValue=""
                  onChange={this.onChangeMessage.bind(this)}
                />
                {props.err_msg &&
                <div className="has-error">
                    <span className="has-error help-block">
                      {props.err_msg}
                    </span>
                </div>
                }
              </div>
              <div className="topicDetail-footer-box-right">
                {(() => {
                  if (props.input_data.message || props.input_data.file_ids.length > 0) {
                    return (
                      <button type="button"
                              className="btn btnRadiusOnlyIcon mod-send"
                              onClick={this.sendMessage.bind(this)}
                              disabled={props.is_saving && "disabled"}/>
                    )
                  } else {
                    return (
                      <button type="button"
                              className="btn btnRadiusOnlyIcon mod-like"
                              onClick={this.sendLike.bind(this)}
                              disabled={props.is_saving && "disabled"}/>
                    )
                  }
                })()}
              </div>
            </div>
          </form>
        </div>
      </div>
    )
  }
}

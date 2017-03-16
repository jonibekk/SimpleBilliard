import React from "react";
import {Link} from "react-router";
import Messages from "./elements/Messages";
import TopicHeader from "./elements/TopicHeader";

export default class Detail extends React.Component {
  constructor(props) {
    super(props)
  }

  componentWillMount() {
    this.props.fetchInitialData(this.props.params.topic_id);
  }

  sendLike(e) {
    this.props.sendLike(this.props.params.topic_id);
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
                <textarea className="form-control disable-change-warning" rows={1} placeholder="Reply" cols={30}
                          name="message_body" defaultValue={""}/>
                {props.err_msg &&
                  <div className="has-error">
                    <span className="has-error help-block">
                      {props.err_msg}
                    </span>
                  </div>
                }
              </div>
              <div className="topicDetail-footer-box-right">
                <button className="btn btnRadiusOnlyIcon mod-like" type="button" onClick={this.sendLike.bind(this)}
                        disabled={props.is_saving && "disabled"}/>
              </div>
            </div>
          </form>
        </div>
      </div>
    )
  }
}

import React from "react";
import ReactDom from "react-dom";
import {Link} from "react-router";

// TODO:Display loading during fetching initial data
export default class TopicCreate extends React.Component {
  constructor(props) {
    super(props);
  }

  componentDidMount() {
    if (ReactDom.findDOMNode(this.refs.select2Member)) {
      initMemberSelect2();
    }
  }

  render() {
    const props = this.props.topic_create;


    return (
      <div>
        <div className="panel topicCreateForm">
          <div className="topicCreateForm-selectTo ">
            <span className="topicCreateForm-selectTo-label">To:</span>
            <input type="hidden" id="select2Member" style={{width: '85%'}} ref="select2Member" />
          </div>
          <div className="topicCreateForm-msgBody">
              <textarea className="topicCreateForm-msgBody-form" placeholder={__("Write a message...")}
                        defaultValue=""/>
          </div>
          <div className="topicCreateForm-footer">
            <div className="topicCreateForm-footer-left">
              <button type="button" className="btn btnRadiusOnlyIcon mod-upload"/>
              <input type="file" className="hidden"/>
            </div>
            <div className="topicCreateForm-footer-right">
              <button type="button" className="btn btnRadiusOnlyIcon mod-send"/>
            </div>
          </div>
        </div>
        <div className="text-align_r">
          <Link to="/topics" className="btn btn-link design-cancel">
            {__("Cancel")}
          </Link>
        </div>
      </div>
    )
  }
}

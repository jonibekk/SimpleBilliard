import React from "react";
import ReactDom from "react-dom";
import {connect} from "react-redux";
import * as actions from "~/message/actions/detail";
import {TopicTitleSettingStatus} from "~/message/constants/Statuses";
import * as KeyCode from "~/common/constants/KeyCode";
import { Link } from "react-router";

class Header extends React.Component {
  constructor(props) {
    super(props)
    this.cancelTopicTitleSetting = this.cancelTopicTitleSetting.bind(this)
    this.startTopicTitleSetting = this.startTopicTitleSetting.bind(this)
    this.saveTopicTitle = this.saveTopicTitle.bind(this)
  }

  componentDidUpdate() {
    if (this.props.topic_title_setting_status == TopicTitleSettingStatus.EDITING) {
      let input = ReactDom.findDOMNode(this.refs.topic_title);

      input && input.focus();
    }
  }

  startTopicTitleSetting(e) {
    this.props.dispatch(
      actions.startTopicTitleSetting()
    )
  }

  cancelTopicTitleSetting(e) {
    if (this.props.topic_title_setting_status == TopicTitleSettingStatus.SAVING) {
      return;
    }
    this.props.dispatch(
      actions.cancelTopicTitleSetting()
    )
  }

  saveTopicTitle(e) {
    if (this.props.topic_title_setting_status == TopicTitleSettingStatus.SAVING) {
      return;
    }
    const title = ReactDom.findDOMNode(this.refs.topic_title).value
    this.props.dispatch(
      actions.saveTopicTitle(title)
    )
  }

  onKeyDown(e) {
    if (e.keyCode === KeyCode.ESC) {
      this.cancelTopicTitleSetting(e);
    } else if(e.keyCode === KeyCode.ENTER) {
      this.saveTopicTitle(e);
    }
  }

  render() {
    const {topic, topic_title_setting_status, save_topic_title_err_msg} = this.props;
    if (Object.keys(topic).length == 0) {
      return null;
    }

    if (topic_title_setting_status != TopicTitleSettingStatus.NONE)
    {
      return (
        <div className="topicDetail-header">
          <div className="topicDetail-header-left">
            <a href="/topics" className="true"><i className="fa fa-chevron-left topicDetail-header-icon"/></a>
          </div>
          <div className="topicDetail-header-center">
            <input type="text"
                   className="topicDetail-header-setTitle-form form-control"
                   defaultValue={topic.title}
                   maxLength="128"
                   ref="topic_title"
                   placeholder={__("Input topic title")}
                   onKeyDown={this.onKeyDown.bind(this)}
            />
            {save_topic_title_err_msg &&
            <div className="has-error">
                    <span className="has-error help-block">
                      {save_topic_title_err_msg}
                    </span>
            </div>
            }
          </div>
          <div className="topicDetail-header-right mod-setTitle">
            <a href="#"
               className="topicDetail-header-setTitle-save mr_4px"
               onClick={this.saveTopicTitle}
            >
              <span>{__("Save")}</span>
            </a>
            <a href="#"
               className="topicDetail-header-setTitle-close"
               onClick={this.cancelTopicTitleSetting}
            >
              <i className="fa fa-close"/>
            </a>
          </div>
        </div>
      )

    }

    return (
      <div className="topicDetail-header">
        <div className="topicDetail-header-left">
          <Link to="/topics" className>
            <i className="fa fa-chevron-left topicDetail-header-icon"/>
          </Link>
        </div>
        <div className="topicDetail-header-center oneline-ellipsis">
          <a href={`/topics/ajax_get_members_modal/${topic.id}`}
             className="topicDetail-header-center-link modal-ajax-get">
            <span className="topicDetail-header-title">{topic.display_title}</span>
          </a>
        </div>
        <div className="topicDetail-header-right">
          <div className="topicDetail-header-membersCnt">
            ({topic.members_count})
          </div>
          <div className="dropdown disp_ib">
            <a href="#" className="dropdown-toggle" id="topicHeaderMenu" data-toggle="dropdown" aria-expanded="true">
              <i className="fa fa-cog topicDetail-header-icon"/>
            </a>
            <ul className="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="topicHeaderMenu">
              <li>
                <a href="#" role="menuitem" tabIndex="-1">
                  <i className="fa fa-user-plus mr_4px"/>{__("Add member(s)")}
                </a>
              </li>
              <li>
                <a href="#" role="menuitem" tabIndex="-1"
                   onClick={this.startTopicTitleSetting}>
                  <i className="fa fa-edit mr_4px"/>{__("Set topic name")}
                </a>
              </li>
              <li>
                <a href="#" role="menuitem" tabIndex="-1">
                  <i className="fa fa-sign-out mr_4px"/>{__("Leave me")}
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    )
  }
}

Header.propTypes = {
  topic: React.PropTypes.object,
  topic_title_setting_status: React.PropTypes.number,
  save_topic_title_err_msg: React.PropTypes.string,
};

Header.defaultProps = {
  topic: {},
  topic_title_setting_status: TopicTitleSettingStatus.NONE,
  save_topic_title_err_msg: ""
};
export default connect()(Header);

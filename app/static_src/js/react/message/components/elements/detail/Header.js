import React from "react";
import ReactDom from "react-dom";
import {connect} from "react-redux";
import * as actions from "~/message/actions/detail";
import {LeaveTopicStatus, TopicTitleSettingStatus} from "~/message/constants/Statuses";
import * as KeyCode from "~/common/constants/KeyCode";
import {Link} from "react-router";

class Header extends React.Component {
  constructor(props) {
    super(props);
    this.cancelTopicTitleSetting = this.cancelTopicTitleSetting.bind(this)
    this.startTopicTitleSetting = this.startTopicTitleSetting.bind(this)
    this.leaveTopic = this.leaveTopic.bind(this)
    this.saveTopicTitle = this.saveTopicTitle.bind(this)
    this.onTouchMove = this.onTouchMove.bind(this)
    this.onToggleTranslation = this.onToggleTranslation.bind(this);
  }

  componentDidUpdate() {
    if (this.props.topic_title_setting_status == TopicTitleSettingStatus.EDITING) {
      let input = ReactDom.findDOMNode(this.refs.topic_title);

      input && input.focus();
    }
    if (this.props.leave_topic_status == LeaveTopicStatus.ERROR) {
      new Noty({
        type: 'error',
        text: '<h4>' + cake.word.error + '</h4>' + this.props.leave_topic_err_msg,
      }).show();
      this.props.dispatch(
        actions.resetLeaveTopicStatus()
      )
    }
  }

  startTopicTitleSetting(e) {
    this.props.dispatch(
      actions.startTopicTitleSetting()
    )
  }

  leaveTopic(e) {
    if (confirm(__('Are you sure you want to leave this topic?'))) {
      this.props.dispatch(
        actions.leaveTopic()
      )
    }
  }

  cancelTopicTitleSetting(e) {
    if (this.props.topic_title_setting_status == TopicTitleSettingStatus.SAVING) {
      return;
    }
    this.props.leave_topic_err_msg
    this.props.dispatch(
      actions.cancelTopicTitleSetting()
    );
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
    } else if (e.keyCode === KeyCode.ENTER) {
      this.saveTopicTitle(e);
    }
  }

  onTouchMove(e) {
    e.preventDefault()
  }

  onToggleTranslation(e) {
    e.preventDefault();
    e.stopPropagation();

    this.props.onTranslationToggle();
  }

  render() {
    const {back_url, topic, topic_title_setting_status, save_topic_title_err_msg, is_mobile_app} = this.props;
    if (Object.keys(topic).length == 0) {
      return null;
    }

    const sp_class = this.props.is_mobile_app ? "mod-sp" : "";

    const header_styles = {
      top: this.props.mobile_app_layout.header_top
    };

    if (topic_title_setting_status != TopicTitleSettingStatus.NONE) {
      return (
        <div
          className={`topicDetail-header ${sp_class}`}
          onTouchMove={this.onTouchMove}
        >
          <div className="topicDetail-header-left">
            <Link to={back_url} className="true"><i className="fa fa-chevron-left topicDetail-header-icon"/></Link>
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
      <div
        className={`topicDetail-header ${sp_class}`}
        onTouchMove={this.onTouchMove}
      >
        <div className="topicDetail-header-left">
          <Link to={back_url} className>
            <i className="fa fa-chevron-left topicDetail-header-icon"/>
          </Link>
        </div>
        <div className="topicDetail-header-center">
          <a href="#"
             data-url={`/topics/ajax_get_members/${topic.id}`}
             className="topicDetail-header-center-link modal-ajax-get">
            <div className="topicDetail-header-title oneline-ellipsis"><span>{topic.display_title}</span></div>
            <div className="topicDetail-header-membersCnt">
              ({topic.members_count})
            </div>
          </a>
        </div>
        <div className="topicDetail-header-right">
          {this.props.message_translation_enabled &&
            <i id="topicHeaderTranslationButton" onClick={this.onToggleTranslation}
               className={this.props.message_translation_active ? 'material-icons activated' : 'material-icons'}
            >
              g_translate
            </i>
          }
          <div className="dropdown">
            <a href="#" className="topicDetail-header-menuIcon dropdown-toggle" id="topicHeaderMenu"
               data-toggle="dropdown" aria-expanded="true">
              <span className="topicDetail-header-menuIcon-inner"></span>
              <span className="topicDetail-header-menuIcon-inner"></span>
              <span className="topicDetail-header-menuIcon-inner"></span>
            </a>
            <ul className="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="topicHeaderMenu">
              <li className="mtb_8px">
                <Link to={`/topics/${topic.id}/add_members`} role="menuitem" tabIndex="-1">
                  <i className="fa fa-user-plus mr_4px"/>{__("Add member(s)")}
                </Link>
              </li>
              <li className="mtb_8px">
                <a href="#" role="menuitem" tabIndex="-1"
                   onClick={this.startTopicTitleSetting}>
                  <i className="fa fa-edit mr_4px"/>{__("Set topic name")}
                </a>
              </li>
              {topic.members_count > 2 &&
              <li className="mtb_8px">
                <a href="#" role="menuitem" tabIndex="-1"
                   onClick={this.leaveTopic}>
                  <i className="fa fa-sign-out mr_4px"/>{__("Leave this topic")}
                </a>
              </li>
              }
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
  leave_topic_status: React.PropTypes.number,
  leave_topic_err_msg: React.PropTypes.string,
  is_mobile_app: React.PropTypes.bool,
  back_url: React.PropTypes.string,
  message_translation_active: React.PropTypes.bool,
  message_translation_enabled: React.PropTypes.bool
};

Header.defaultProps = {
  topic: {},
  topic_title_setting_status: TopicTitleSettingStatus.NONE,
  save_topic_title_err_msg: "",
  leave_topic_status: LeaveTopicStatus.NONE,
  leave_topic_err_msg: "",
  is_mobile_app: false,
  back_url: '',
  message_translation_active: false,
  message_translation_enabled: false
};
export default connect()(Header);

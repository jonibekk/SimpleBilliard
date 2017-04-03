import React from "react";
import ReactDom from "react-dom";
import {browserHistory, Link} from "react-router";

// TODO:Fix design
export default class TopicMembersAdd extends React.Component {

  constructor(props) {
    super(props)
  }

  componentWillMount() {
    // Set resource ID included in url.
    const topic_id = this.props.params.topic_id;
    this.props.setResourceId(topic_id);
  }

  componentDidMount() {
    // TODO:Remove selected members from suggest
    // HACK:To use select2Member
    $(document).ready(function (e) {
      initMemberSelect2();
    });
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.topic_members_add.redirect) {
      browserHistory.push(`/topics/${nextProps.topic_members_add.topic_id}/detail`);
    }
  }

  componentWillUnmount() {
    this.props.resetStates();
  }

  addMembers(e) {
    this.props.addMembers();
  }

  selectUsers(e) {
    const user_ids = this.getSelectUserIdsByDom();
    this.props.selectUsers(user_ids);
  }

  getSelectUserIdsByDom() {
    let user_ids_str = ReactDom.findDOMNode(this.refs.select2Member).value;
    if (!user_ids_str) {
      return [];
    }
    user_ids_str = user_ids_str.replace(/user_/g, '');
    return user_ids_str.split(',');
  }

  render() {
    const props = this.props.topic_members_add;

    const disableSend = () => {
      if (props.is_saving) {
        return true;
      }
      if (props.user_ids.length == 0) {
        return true;
      }
      return false;
    }
    return (
      <div>
        <div className="panel topicMembersAddForm">
          <span className="hidden js-triggerUpdateToUserIds" onClick={this.selectUsers.bind(this)}/>
          <div className="topicMembersAddForm-selectTo ">
            <input type="hidden" id="select2Member" className="js-changeSelect2Member"
                   style={{width: '100%'}} ref="select2Member"/>
          </div>
          <div className="topicMembersAddForm-footer">
            {(props.err_msg) &&
            <div className="has-error">
                <span className="has-error help-block">
                  {this.props.err_msg}
                </span>
            </div>
            }
            <div className="topicMembersAddForm-footer-right">
              <button
                type="button"
                className="btn btn-primary pull-right"
                onClick={this.addMembers.bind(this)}
              >{__("Add")}</button>
            </div>
          </div>
        </div>
        <div className="text-align_r">
          <Link to={`/topics/${props.topic_id}/detail`} className="btn btn-link design-cancel">
            {__("Cancel")}
          </Link>
        </div>
      </div>
    )
  }
}

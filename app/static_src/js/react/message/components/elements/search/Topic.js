import React from "react";
import AvatarsBox from "~/common/components/AvatarsBox";
import {setTopicOnDetail} from "~/message/actions/search";
import {emptyTopicList} from "~/message/actions/index";
import {browserHistory, Link} from "react-router";
import {connect} from "react-redux";
import {SearchType} from "~/message/constants/Statuses";

class Topic extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      is_taped_item: false,
    }
  }

  onClickLinkToDetail(url) {
    const {dispatch, topic} = this.props
    browserHistory.push({pathname: url, state: {back_url: location.href}});
    dispatch(setTopicOnDetail(topic))
    dispatch(emptyTopicList())
  }

  tapLink(e) {
    this.setState({is_taped_item: true})
  }

  render() {
    const data = this.props.data;
    const keyword = this.props.keyword;
    const type = this.props.type;

    let detail_url = ""
    if (type === SearchType.TOPICS) {
      detail_url = `/topics/${data.topic.id}/detail`;
    } else {
      detail_url = `/topics/${data.topic.id}/search_messages?keyword=${keyword}`;
    }


    let matching_count_word = "";
    if (type === SearchType.TOPICS) {
      if (data.highlight_member_count == 1) {
        matching_count_word = sprintf(__("%d matched member"), data.highlight_member_count)
      } else if (data.highlight_member_count > 1) {
        matching_count_word = sprintf(__("%d matched members"), data.highlight_member_count)
      }
    } else {
      if (data.doc_count == 1) {
        matching_count_word = sprintf(__("%d matched message"), data.doc_count)
      } else if (data.doc_count > 1) {
        matching_count_word = sprintf(__("%d matched messages"), data.doc_count)
      }
    }
    return (
      <li className="topicSearchList-item" key={ data.topic.id }>
        <a href="#"
              className={`topicSearchList-item-link ${this.state.is_taped_item ? "is-hover" : ""}`}
              onClick={ this.onClickLinkToDetail.bind(this, detail_url) }
              onTouchTap={ this.tapLink.bind(this) }>
          <AvatarsBox users={ data.users }/>
          <div className="topicSearchList-item-main">
            <div className="topicSearchList-item-main-header">
              <div className="topicSearchList-item-main-header-title oneline-ellipsis">
                { data.topic.display_title }
              </div>
              <div className="topicSearchList-item-main-header-count">
                { (data.topic.members_count > 2 || data.topic.title) && `(${data.topic.members_count})` }
              </div>
            </div>
            <div className="topicSearchList-item-main-body oneline-ellipsis">
              {matching_count_word}
            </div>
          </div>
          <div className="topicSearchList-item-right">
            { data.topic.display_created }
          </div>
        </a>
      </li>
    )
  }
}

export default connect()(Topic);

import React from "react";
import AvatarsBox from "~/common/components/AvatarsBox";
import {setTopicOnDetail} from "~/message/actions/search";
import {emptyTopicList} from "~/message/actions/index";
import {Link} from "react-router";
import {connect} from "react-redux";
import {SearchType} from "~/message/constants/Statuses";

class Topic extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      is_taped_item: false,
    }
  }

  onClickLinkToDetail() {
    const {dispatch, topic} = this.props
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

    let url = ""
    if (type === SearchType.TOPICS) {
      url = `/topics/${data.topic.id}/detail`;
    } else {
      url = `/topics/${data.topic.id}/search_messages?keyword=${keyword}`;
    }
    return (
      <li className="topicSearchList-item" key={ data.topic.id }>
        <Link to={ url }
              className={`topicSearchList-item-link ${this.state.is_taped_item ? "is-hover" : ""}`}
              onClick={ this.onClickLinkToDetail.bind(this) }
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
                { type === SearchType.TOPICS ? `${data.highlight_member_count} matching_members` : `${data.doc_count} messages hit` }
            </div>
          </div>
          <div className="topicSearchList-item-right">
            { data.topic.display_created }
          </div>
        </Link>
      </li>
    )
  }
}

export default connect()(Topic);

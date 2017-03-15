import React from "react";
import {Link} from "react-router";
import Message from "./elements/Message";
import Messages from "./elements/Messages";

export default class Detail extends React.Component {
  constructor(props) {
    super(props)
    let items = [];
    for (var i = 0; i < 40; i++) {
      items[i] = i;
    }
    this.state =  {
      items,
      loadingMore: false,
      hasMore:true
    };
    // this.updateFilter = this.updateFilter.bind(this);
  }
  _loadMore() {
    let items = [];
    for (var i = 40; i < 80; i++) {
      items[i] = i;
    }
    items = [...items, ...this.state.items];
console.log(items);
    this.setState({loadingMore: true}, () => {
      // CB emulates an ajax request
      this.setState({
        // items: this.state.numOfItems + 40,
        items,
        loadingMore: false,
        hasMore:false
      })
    })
  }

  componentWillMount() {
    console.log({topic_id:this.props.params.topic_id});
    this.props.fetchInitialData(this.props.params.topic_id)
  }
  renderMessages(messages) {
    let uid = this.makeRandomStr()
    return messages.map((message) => {
      return (
        <Message message={message} key={`${uid}-${message.id}`}/>
      )
    })
  }

  render() {
    const {topic, messages} = this.props.detail


    return (
      <div className="panel panel-default topicDetail">
        <div className="topicDetail-header">
          <div className="topicDetail-header-left">
            <a href="#" className>
              <i className="fa fa-chevron-left topicDetail-header-icon"/>
            </a>
            <span className="ml_8px"> 中嶋 あいみ</span>
          </div>
          <div className="topicDetail-header-right">
            <a href="#" className="dropdown">
              <i className="fa fa-cog topicDetail-header-icon" id="dropdownMenu2" data-toggle="dropdown"
                 aria-expanded="true">
              </i>
            </a>
            <ul className="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                aria-labelledby="dropdownMenu1">
              <li role="presentation">
                <ul>
                  <li>
                    <a href="#" role="menuitem" tabIndex={-1}>
                      <i className="fa fa-user-plus "/>メンバーを追加
                    </a>
                  </li>
                  <li>
                    <a href="#" role="menuitem" tabIndex={-1}>
                      <i className="fa fa-edit"/>トピック名を追加
                    </a>
                  </li>
                  <li>
                    <a href="#" role="menuitem" tabIndex={-1}>
                      <i className="fa fa-sign-out"/>このトピックを残す
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
        <div className="topicDetail-body">
          <Messages
            //messages={messages}
            messages={this.state.items}
            loadMore={this._loadMore.bind(this)}
            hasMore={this.state.hasMore}
            loadingMore={this.state.loadingMore}
            showLoader={true}
            threshold={50}
            containerHeight={400}
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

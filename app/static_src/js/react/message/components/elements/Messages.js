import React from "react";
import ReactDOM from "react-dom";
import {connect} from "react-redux";
import * as actions from "~/message/actions/detail";
import Loading from "./Loading";

class Messages extends React.Component {

  constructor(props) {
    super(props);
    this.scrollFunction = this.scrollListener.bind(this);
    this.moveBottom = this.moveBottom.bind(this);
  }

  componentDidMount() {
    this.moveBottom()
    this.attachScrollListener();
  }

  componentDidUpdate() {
    this.attachScrollListener();
  }

  _findElement() {
    return ReactDOM.findDOMNode(this);
  }

  attachScrollListener() {
    if (!this.props.paging.next || this.props.loading_more) return;
    let el = this._findElement();
    el.addEventListener('scroll', this.scrollFunction, true);
    el.addEventListener('resize', this.scrollFunction, true);
    this.scrollListener();
  }

  scrollListener() {
    if (this.props.messages.length <= 0) {
      return;
    }

    let el = this._findElement();
    let top_scroll_pos = el.scrollTop;
    const threshold = 100;
    if (top_scroll_pos < threshold) {
      this.detachScrollListener();
      this.props.dispatch(
        actions.fetchMoreMessages(this.props.paging.next)
      )
    }
  }

  moveBottom() {
    if (this.props.messages.length <= 0) {
      return;
    }

    let el = this._findElement();
    el.scrollTop = el.scrollHeight;
  }

  detachScrollListener() {
    let el = this._findElement();
    el.removeEventListener('scroll', this.scrollFunction, true);
    el.removeEventListener('resize', this.scrollFunction, true);
  }

  componentWillUnmount() {
    this.detachScrollListener();
  }

  render() {
    return (
      <div className="topicDetail-messages">
        {this.props.messages.map((message) => {
          return (
            <Message message={message} key={message.id}/>
          )
        })}
        {this.props.loading_more && <Loading/>}
      </div>
    )
  }
}

Messages.propTypes = {
  loading_more: React.PropTypes.bool,
  messages: React.PropTypes.array,
  paging: React.PropTypes.object,
};

Messages.defaultProps = {
  loading_more: false,
  messages: [],
  paging: {next:""}
};
export default connect()(Messages);

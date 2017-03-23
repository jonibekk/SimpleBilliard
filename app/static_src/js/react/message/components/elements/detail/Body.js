import React from "react";
import ReactDOM from "react-dom";
import {connect} from "react-redux";
import * as actions from "~/message/actions/detail";
import Message from "~/message/components/elements/detail/Message";
import Loading from "~/message/components/elements/detail/Loading";

class Body extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
      scrolled_bottom: false
    }
    this.scrollFunction = this.scrollListener.bind(this);
    this.scrollBottom = this.scrollBottom.bind(this);
  }

  componentDidMount() {
    this.attachScrollListener();
  }

  componentDidUpdate() {
    this.attachScrollListener();
    if (this.props.is_fetched_initial && !this.state.scrolled_bottom) {
      this.scrollBottom();
    }
  }

  _findElement() {
    return ReactDOM.findDOMNode(this);
  }

  // TODO: componentize
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

  scrollBottom() {
    if (this.props.messages.length <= 0) {
      return;
    }

    let el = this._findElement();
    el.scrollTop = el.scrollHeight;
    this.setState({scrolled_bottom: true})
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
      <div className="topicDetail-body">
        <div className="topicDetail-messages">
          {this.props.loading_more && <Loading/>}
          {this.props.messages.map((message) => {
            return (
              <Message message={message} key={message.id}/>
            )
          })}
        </div>
      </div>
    )
  }
}

Body.propTypes = {
  loading_more: React.PropTypes.bool,
  messages: React.PropTypes.array,
  paging: React.PropTypes.object,
  is_fetched_initial:React.PropTypes.bool
};

Body.defaultProps = {
  loading_more: false,
  messages: [],
  paging: {next:""},
  is_fetched_initial:false
};
export default connect()(Body);

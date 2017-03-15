import React from "react";
import ReactDOM from "react-dom";
// import {topPosition, leftPosition} from './Utilities/DOMPositionUtils';
export default class Messages extends React.Component {

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
    if (!this.props.hasMore || this.props.loadingMore) return;
    let el = this._findElement();
    el.addEventListener('scroll', this.scrollFunction, true);
    el.addEventListener('resize', this.scrollFunction, true);
    this.scrollListener();
  }

  _elScrollListener() {
    let el = ReactDOM.findDOMNode(this);

    let topScrollPos = el.scrollTop;
    console.log({topScrollPos});
    // let totalContainerHeight = el.scrollHeight;
    // let containerFixedHeight = el.offsetHeight;
    // let bottomScrollPos = topScrollPos + containerFixedHeight;
    return topScrollPos;


    // let topScrollPos = el.scrollTop;
    // let totalContainerHeight = el.scrollHeight;
    // let containerFixedHeight = el.offsetHeight;
    // let bottomScrollPos = topScrollPos + containerFixedHeight;
    //
    // return (totalContainerHeight - bottomScrollPos);
  }

  scrollListener() {
    // This is to prevent the upcoming logic from toggling a load more before
    // any data has been passed to the component
    if (this.props.messages.length <= 0) {
      return;
    }
    let bottomPosition = this._elScrollListener();


    if (bottomPosition < Number(this.props.threshold)) {
      console.log("-----loadMore");
      this.detachScrollListener();
      this.props.loadMore();
    }
  }

  moveBottom() {
    if (this.props.messages.length <= 0) return;
    let el = ReactDOM.findDOMNode(this);
    el.scrollTop = el.scrollHeight;
  }

  detachScrollListener() {
    let el = this._findElement();
    el.removeEventListener('scroll', this.scrollFunction, true);
    el.removeEventListener('resize', this.scrollFunction, true);
  }

  _renderOptions() {
    let el = [];
    // this.props.messages.map((goal_member) => {
    //   if(goal_member.is_mine) {
    //     return <CoacheeCard goal_member={ goal_member } key={goal_member.id}  />;
    //   } else {
    //     return <CoachCard goal_member={ goal_member } key={goal_member.id} />;
    //   }
    for (var i=this.props.messages.length-1; i >= 0; i--) {
      el.push(
        <div key={`msg_${i}`}>Item #{i}</div>
      )
    }

    return el;
  }

  componentWillUnmount() {
    this.detachScrollListener();
  }

  renderLoader() {
    return this.props.loadingMore ? this.props.loader : null;
  }

  render() {
    console.log(this.props.messages.length);
    return (
      <div className="" style={{
        height: this.props.containerHeight, overflow: 'scroll'
      }}>
        {this._renderOptions()}
        {this.renderLoader()}
      </div>
    )
  }
}

Messages.propTypes = {
  containerHeight: React.PropTypes.oneOfType([
    React.PropTypes.number,
    React.PropTypes.string
  ]),
  threshold: React.PropTypes.number,
  hasMore: React.PropTypes.bool,
  loadingMore: React.PropTypes.bool,
  loader: React.PropTypes.any,
  loadMore: React.PropTypes.func.isRequired,
  messages: React.PropTypes.oneOfType([
    //ImmutablePropTypes.list,
    React.PropTypes.array
  ]),
  children: React.PropTypes.oneOfType([
    //ImmutablePropTypes.list,
    React.PropTypes.array
  ]),
  className: React.PropTypes.oneOfType([
    React.PropTypes.string,
    React.PropTypes.func
  ]),
};

Messages.defaultProps = {
  className: '',
  containerHeight: '100%',
  threshold: 100,
  hasMore: true,
  loadingMore: false,
  loader: <div style={{textAlign: 'center'}}>Loading...</div>,
  children: [],
  messages: [],
};

import React from 'react'
import ReactDOM from 'react-dom'

export default class TopicSearchList extends React.Component {
  constructor(props) {
    super(props);
  }

  componentDidMount() {
    ReactDOM.findDOMNode(this.refs.search).focus()
  }

  render() {
    return (
      <div className="panel panel-default topicSearchList">
        <div className="topicSearchList-header">
          <div className="topicSearchList-header-searchBox">
            <div className="searchBox">
              <div className="searchBox-remove-icon">
                <i className="fa fa-remove"></i>
              </div>
              <input className="searchBox-input"
                     placeholder={__("Search topic")}
                     ref="search" />
              <span className="fa fa-search searchBox-button"></span>
            </div>
          </div>
          <div className="topicSearchList-header-cancel">
            <a className="topicSearchList-header-cancel-button"
               onClick={ this.props.cancelSearchMode.bind(this) }>Cancel</a>
          </div>
        </div>
      </div>
    )
  }
}

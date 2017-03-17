import React from 'react'

export default class Index extends React.Component {
  render() {
    return (
      <div className="panel panel-default topic">
        <div className="topic-header">
          <div className="searchBox">
            <div className="searchBox-icon">
              <i className="fa fa-search"></i>
            </div>
            <input className="searchBox-input"
                   placeholder={__("Search topic")} />
          </div>
          <div className="topic-header-middle">
            <div className="topic-header-middle-label">
              {__("TOPICS")}
            </div>
            <a href="" className="topic-header-middle-add">
              <i className="fa fa-plus-circle"></i> {__("New Message")}
            </a>
          </div>
        </div>
      </div>
    )
  }
}

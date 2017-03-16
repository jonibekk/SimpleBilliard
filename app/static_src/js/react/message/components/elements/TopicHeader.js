import React from "react";

// TODO:display member count
export default class TopicHeader extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const {topic} = this.props;
    if (Object.keys(topic).length == 0) {
      return null;
    }
    return (
      <div className="topicDetail-header">
        <div className="topicDetail-header-left">
          <a href="/topics" className>
            <i className="fa fa-chevron-left topicDetail-header-icon"/>
          </a>
          <span className="ml_8px">{topic.display_title}</span>
        </div>
        <div className="topicDetail-header-right">
          <div className="dropdown">
            <a href="#" className="dropdown-toggle" id="topicHeaderMenu" data-toggle="dropdown" aria-expanded="true">
              <i className="fa fa-cog topicDetail-header-icon" />
            </a>
            <ul className="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="topicHeaderMenu">
              <li>
                <a href="#" role="menuitem" tabIndex={-1}>
                  <i className="fa fa-user-plus mr_4px" />{__("Add member(s)")}
                </a>
              </li>
              <li>
                <a href="#" role="menuitem" tabIndex={-1}>
                  <i className="fa fa-edit mr_4px" />{__("Set topic name")}
                </a>
              </li>
              <li>
                <a href="#" role="menuitem" tabIndex={-1}>
                  <i className="fa fa-sign-out mr_4px" />{__("Leave me")}
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    )
  }
}

TopicHeader.propTypes = {
  topic: React.PropTypes.object,
};

TopicHeader.defaultProps = {
  topic: {}
};

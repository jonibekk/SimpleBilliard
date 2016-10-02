import React from 'react'

export class ViewMoreButton extends React.Component {
  render() {
    return (
      <div className="panel panel-default feed-read-more">
          <div className="panel-body panel-read-more-body-no-data">
              <a className="goals-approval-list-item-view-more-button" onClick={ this.props.handleOnClick }>
                  <span>{__("more")}</span>
                  { this.props.is_loading ? <i className="fa fa-refresh fa-spin goals-approval-list-item-more-view-button-spin"></i> : null }
              </a>
          </div>
      </div>
    )
  }
}

ViewMoreButton.propTypes = {
  is_loading: React.PropTypes.bool.isRequired,
  handleOnClick: React.PropTypes.func.isRequired
}

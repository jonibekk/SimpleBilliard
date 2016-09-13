import React from 'react'

export class ListMoreViewButton extends React.Component {
  render() {
    return (
      <div className="panel panel-default feed-read-more">
          <div className="panel-body panel-read-more-body-no-data">
              <a className="goals-approval-list-item-more-view-button" onClick={ this.props.handleOnClick }>
                  <span>もっと見る</span>
                  {
                    (() => {
                      if(this.props.is_loading) {
                        return <i className="fa fa-refresh fa-spin goals-approval-list-item-more-view-button-spin"></i>;
                      }
                    })()
                  }
              </a>
          </div>
      </div>
    )
  }
}

ListMoreViewButton.propTypes = {
  handleOnClick: React.PropTypes.func.isRequired
}

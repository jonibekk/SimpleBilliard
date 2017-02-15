import React, { PropTypes } from 'react'
import { Link, browserHistory } from 'react-router'

export default class CircleSelect extends React.Component {
  constructor(props) {
    super(props);
  }
  componentWillMount() {
    this.props.fetchCirclesForPost()
  }
  getCircles() {
    return this.props.post.circles
  }
  render() {
    var circles = this.getCircles().map((circle, index) => {
      return (
        <div className="setup-items-item pt_10px mt_12px bd-radius_14px"
          onClick={(e) => {
            e.preventDefault()
            this.props.selectCirclePost(circle.Circle)}
          }
          key={circle.Circle.id}>
          <div className="row">
            <div className="setup-items-select-circle pull-left">{circle.Circle.name}</div>
            <span className="pull-right setup-items-item-to-right mt_12px mr_5px">
              <i className="fa fa-chevron-right font_18px"></i>
            </span>
          </div>
        </div>
      )
    })
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__('Set up Goalous')} <i className="fa fa-angle-right" aria-hidden="true"></i> {__('Post to a circle')}
        </div>
        <div className="setup-items">
          {circles}
        </div>
        <div className="mb_12px">
          <Link to="/setup/circle/create">{__('Create another circle')}<i className="fa fa-angle-right" aria-hidden="true"></i> </Link>
        </div>
        <div>
          <Link to="/setup/post/image" className="btn btn-secondary setup-back-btn-full">{__('Back')}</Link>
        </div>
      </div>
    )
  }
}

CircleSelect.propTypes = {
  fetchCircles: PropTypes.func,
  onClickSelectCircle: PropTypes.func,
  onClickJoinCircle: PropTypes.func,
  selected_circle_id: PropTypes.number,
  circles: PropTypes.array
}

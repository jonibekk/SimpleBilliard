import React, { PropTypes } from 'react'
import { Link, browserHistory } from 'react-router'

export default class CircleSelect extends React.Component {
  constructor(props) {
    super(props);
  }
  getCircles() {
    return this.props.circles.circles
  }
  render() {
    var check_icon = () => {
      return (
        <i className="fa fa-check font_33px" aria-hidden="true"></i>
      )
    }
    var circles = this.getCircles().map((circle) => {
      return (
        <div className="setup-items-item pt_10px mt_12px bd-radius_14px" onClick={(e) => { this.props.onClickSelectCircle(circle.Circle.id)}} key={circle.Circle.id}>
          <div className="row">
            <div className="setup-items-select-circle pull-left">{circle.Circle.name}</div>
            <span className="pull-right setup-items-select-circle-check">
              { this.props.circles.selected_circle_id == circle.Circle.id ? check_icon() : null }
            </span>
          </div>
        </div>
      )
    })
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          Set up Goalous <i className="fa fa-angle-right" aria-hidden="true"></i> Create a circle
        </div>
        <div className="setup-items">
          {circles}
        </div>
        <div className="mb_13px">
          <Link to="/setup/circle/create">Create your own <i className="fa fa-angle-right" aria-hidden="true"></i> </Link>
        </div>
        <div>
          <Link to="/setup/circle/image" className="btn btn-secondary setup-back-btn">Back</Link>
          <Link to="/setup/circle/select"
            className="btn btn-primary setup-next-btn pull-right"
            disabled={!Boolean(this.props.circles.selected_circle_id)}
            onClick={(e) => { this.props.onClickJoinCircle( this.props.circles.selected_circle_id) }}
          >Join a circle</Link>
        </div>
      </div>
    )
  }
}

CircleSelect.propTypes = {
  onClickSelectCircle: PropTypes.func,
  onClickJoinCircle: PropTypes.func,
  selected_circle_id: PropTypes.number,
  circles: PropTypes.array
}

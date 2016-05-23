import React, { PropTypes } from 'react'
import { Link, browserHistory } from 'react-router'

export default class CircleSelect extends React.Component {
  constructor(props) {
    super(props);
  }
  componentWillMount() {
    this.props.fetchCircles()
  }
  getCircles() {
    return this.props.circle.circles
  }
  render() {
    var check_icon = () => {
      return (
        <i className="fa fa-check font_33px" aria-hidden="true"></i>
      )
    }
    var circles = this.getCircles().map((circle) => {
      return (
        <div className="setup-items-item pt_10px mt_12px bd-radius_14px setup-items-circle-item"
             key={circle.Circle.id}
             onClick={(e) => { this.props.onClickSelectCircle(this.props.circle.selected_circle_id_list, circle.Circle.id)}}>
          <div className="row">
            <div className="setup-items-select-circle pull-left">{circle.Circle.name}</div>
            <span className="pull-right setup-items-select-circle-check">
              { this.props.circle.selected_circle_id_list.indexOf(circle.Circle.id) >= 0 ? check_icon() : null }
            </span>
          </div>
        </div>
      )
    })
    return (
      <div>
        <div className="setup-pankuzu font_18px">
          {__('Set up Goalous')} <i className="fa fa-angle-right" aria-hidden="true"></i> {__('Create a circle')}
        </div>
        <div className="setup-items">
          {circles}
        </div>
        <div className="mb_12px">
          <Link to="/setup/circle/create">{__('Create your own')} <i className="fa fa-angle-right" aria-hidden="true"></i> </Link>
        </div>
        <div>
          <Link to="/setup/circle/image" className="btn btn-secondary setup-back-btn">{__('Back')}</Link>
          <Link to="/setup/circle/select"
                className="btn btn-primary setup-next-btn pull-right"
                disabled={!Boolean(this.props.circle.can_join_circle)}
                onClick={(e) => {
                  this.props.onClickJoinCircle( this.props.circle.selected_circle_id_list) }
                }>
            {__('Join a circle')}
          </Link>
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

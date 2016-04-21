import React from 'react'
import ReactDOM from 'react-dom'
import { Link, browserHistory } from 'react-router'

export default class CircleSelect extends React.Component {
  constructor(props, context) {
    super(props, context);
    this.state = { selected: false, show_check: false };
  }
  onSubmitJoin() {
  }
  onClickSelectCircle(e, id) {
    this.setState({ selected: true, show_check: true })
  }
  listData() {
    return ([
      {
        id: 1,
        text: "News"
      },
      {
        id: 2,
        text: "Gourmet"
      },
      {
        id: 3,
        text: "President's room"
      },
    ])
  }
  render() {
    var check_icon = () => {
      return (
        <i className="fa fa-check font_33px" aria-hidden="true"></i>
      )
    }
    var circles = this.listData().map((text) => {
      return (
        <div className="setup-items-item pt_10px mt_12px bd-radius_14px" onClick={this.onClickSelectCircle.bind(this, text.id)} key={text.id}>
          <div className="row">
            <div className="setup-items-select-circle pull-left">{text.text}</div>
            <span className="pull-right setup-items-select-circle-check">
              { this.state.show_check ? check_icon : null }
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
          <Link to="/setup/circle/create" className="btn btn-secondary setup-back-btn">Back</Link>
          <Link to="/setup/circle/select" className="btn btn-primary setup-next-btn pull-right" disabled={!this.state.selected}>Join a circle</Link>
        </div>
      </div>
    )
  }
}

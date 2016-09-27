import React from "react";
import ReactDOM from "react-dom";
import {Comments} from "../../common/components/approval/Comments";
import {GoalCard} from "./elements/GoalCard";
import {Link} from "react-router";

export default class Confirm extends React.Component {
  constructor(props) {
    super(props)
  }

  componentWillMount() {
    if (Object.keys(this.props.goal.goal).length == 0) {
      document.location.href = "/"
    }
    this.props.fetchComments()
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.toNextPage) {
      document.location.href = '/'
    }
  }

  getInputDomData() {
    return {
      comment: ReactDOM.findDOMNode(this.refs.comment).value
    }
  }

  onSubmit(e) {
    e.preventDefault()
    this.props.saveGoal(this.getInputDomData())
  }

  render() {

    const data = this.props.goal

    return (

      <section className="panel panel-default col-sm-8 col-sm-offset-2 clearfix goals-approval">
        <h1 className="goals-approval-heading">{__("Set as a target for evaluation?")}</h1>
        <div className="goals-approval-detail">
          <GoalCard inputData={data.inputData} goal={data.goal} categories={data.categories} units={data.units}/>
          <Comments approvalHistories={ data.approvalHistories }/>
          <form className="p_10px" onSubmit={this.onSubmit.bind(this)}>
            <textarea name="description" ref="comment" placeholder={__("Add your comment (optional)")} className="form-control goals-create-input-form mod-textarea" />
            <div className="row">
              <button type="submit" className="goals-create-btn-next btn">{__("Save and Share")}</button>
              <Link to={`/goals/${this.props.params.goalId}/edit`}
                    className="goals-create-btn-cancel btn">{__("Back")}</Link>
            </div>
          </form>
        </div>
      </section>
    )
  }
}

Confirm.propTypes = {
  goal: React.PropTypes.object.isRequired,
}

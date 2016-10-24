import React from "react";
import { connect } from 'react-redux';
import * as actions from "~/goal_search/actions/goal_actions";
import LabelInput from "~/goal_search/components/elements/LabelInput";

class GoalSearchFilter extends React.Component {
  constructor(props) {
    super(props);
  }

  onChange(e) {
    this.props.dispatch(
      actions.updateFilter({[e.target.name]: e.target.value})
    )
  }
  onClose(e) {
    e.preventDefault()
    this.props.dispatch(
      actions.updateData({show_filter: false})
    )
  }

  render() {
    if (!this.props.show_filter) {
      return null
    }
    const {suggestions, label_keyword, categories, labels, search_conditions} = this.props

    return (
      <div className="panel-block bd-b-sc4 ">
        <div className="gl-form-horizontal">
          <label className="gl-form-horizontal-col gl-form-label" htmlFor>{__("Term")}</label>
          <div className="gl-form-horizontal-col">
            <select name="term" className="form-control gl" onChange={this.onChange.bind(this)}>
              <option value="present">{__("Current Term")}</option>
              <option value="next">{__("Next Term")}</option>
              <option value="previous">{__("Previous Term")}</option>
              <option value="before">{__("More Previous")}</option>
            </select>
          </div>
        </div>
        <div className="gl-form-horizontal">
          <label className="gl-form-horizontal-col gl-form-label" htmlFor>{__("Category")}</label>
          <div className="gl-form-horizontal-col">
            <select name="category" className="form-control gl" onChange={this.onChange.bind(this)}>
              <option value>{__("All")}</option>
              {categories.map((v) => {
                return <option value={v.id} key={v.id}>{v.name}</option>
              })}
            </select>
          </div>
        </div>
        <div className="gl-form-horizontal">
          <label className="gl-form-horizontal-col gl-form-label" htmlFor>{__("Progress")}</label>
          <div className="gl-form-horizontal-col">
            <select name="progress" className="form-control gl" onChange={this.onChange.bind(this)}>
              <option value>{__("All")}</option>
              <option value="complete">{__("Complete")}</option>
              <option value="incomplete">{__("Incomplete")}</option>
            </select>
          </div>
        </div>
        <div className="gl-form">
          <LabelInput
            suggestions={suggestions}
            keyword={label_keyword}
            labels={labels}
            input_labels={search_conditions.labels}
          />
        </div>
        <div className="text-align_c p_4px">
          <a href="#" onClick={this.onClose.bind(this)} >
            {__("Close")} <span className="fa fa-angle-up"/>
          </a>
        </div>
      </div>
    )
  }
}


GoalSearchFilter.propTypes = {
  show_filter: React.PropTypes.bool.isRequired,
  suggestions: React.PropTypes.array,
  label_keyword: React.PropTypes.string,
  categories: React.PropTypes.array,
  labels: React.PropTypes.array,
  search_conditions: React.PropTypes.object,
}
GoalSearchFilter.defaultProps = {
  show_filter: false
}

export default connect()(GoalSearchFilter);


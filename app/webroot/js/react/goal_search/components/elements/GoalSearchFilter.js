import React from "react";
import { connect } from 'react-redux';
import * as actions from "~/goal_search/actions/goal_actions";
import LabelInput from "~/goal_search/components/elements/LabelInput";

class GoalSearchFilter extends React.Component {
  onChange(e) {
    this.props.dispatch(
      actions.updateFilter({[e.target.name]: e.target.value})
    )
  }
  constructor(props) {
    super(props);
  }

  onClose(e) {
    e.preventDefault()
    this.props.dispatch(
      actions.updateData({showFilter: false})
    )
  }

  render() {
    if (!this.props.showFilter) {
      return null
    }
    const {suggestions, label_keyword, categories, labels, search_conditions} = this.props

    return (
      <div className="panel-block bd-b-sc4 ">
        <div className="gl-form-horizontal">
          <label className="gl-form-horizontal-col gl-form-label" htmlFor>評価期間</label>
          <div className="gl-form-horizontal-col">
            <select name="term" className="form-control gl" onChange={this.onChange.bind(this)}>
              <option value="present">今期</option>
              <option value="next">来期</option>
              <option value="previous">前期</option>
              <option value="before">もっと前</option>
            </select>
          </div>
        </div>
        <div className="gl-form-horizontal">
          <label className="gl-form-horizontal-col gl-form-label" htmlFor>カテゴリ</label>
          <div className="gl-form-horizontal-col">
            <select name="category" className="form-control gl" onChange={this.onChange.bind(this)}>
              <option value>すべて</option>
              {categories.map((v) => {
                return <option value={v.id} key={v.id}>{v.name}</option>
              })}
            </select>
          </div>
        </div>
        <div className="gl-form-horizontal">
          <label className="gl-form-horizontal-col gl-form-label" htmlFor>達成/未達成</label>
          <div className="gl-form-horizontal-col">
            <select name="progress" className="form-control gl" onChange={this.onChange.bind(this)}>
              <option value>すべて</option>
              <option value="complete">達成</option>
              <option value="incomplete">未達成</option>
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
            閉じる <span className="fa fa-angle-up"/>
          </a>
        </div>
      </div>
    )
  }
}


GoalSearchFilter.propTypes = {
  showFilter: React.PropTypes.bool.isRequired,
  suggestions: React.PropTypes.array,
  label_keyword: React.PropTypes.string,
  categories: React.PropTypes.array,
  labels: React.PropTypes.array,
  search_conditions: React.PropTypes.object,

}
GoalSearchFilter.defaultProps = {
  showFilter: false
}

export default connect()(GoalSearchFilter);


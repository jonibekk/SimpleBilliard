import React from "react";

export default class CategorySelect extends React.Component {
  constructor(props) {
    super(props);
  }
  render() {
    if (this.props.categories.length == 0) {
      return null;
    }
    let options = this.props.categories.map((v) => {
      return <option value={v.id} key={v.id}>{v.name}</option>;
    });
    return (
      <div>
        <label className="goals-create-input-label">{__("Category")}</label>
        <select
          className="form-control goals-create-input-form"
          name="goal_category_id"
          onChange={this.props.onChange}
          value={this.props.value}>
          {options}
        </select>
      </div>
    )

  }
}
CategorySelect.propTypes = {
  categories: React.PropTypes.array,
  value: React.PropTypes.string
};
CategorySelect.defaultProps = {categories: [], value: ""};
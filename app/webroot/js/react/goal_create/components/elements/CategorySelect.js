import React from "react";

export default class CategorySelect extends React.Component {
  render() {
    console.log("category select")
    console.log(this.props.categories)
    if (this.props.categories.length == 0) {
      return null;
    }
    let options = this.props.categories.map(v => {
      console.log("category")
      console.log(v)
      return <option value={v.id} key={v.id}>{v.name}</option>;
    });
    return (
      <select
        className="form-control goals-create-input-category-select"
        name="category"
        ref="category"
        id="">
        {options}
      </select>
    )

  }
}
CategorySelect.propTypes = {categories: React.PropTypes.array};
CategorySelect.defaultProps = {categories: []};


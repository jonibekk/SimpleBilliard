import React from "react";
import InvalidMessageBox from "~/common/components/InvalidMessageBox";
import {connect} from "react-redux";
import {updateInputData} from "~/payment_apply/actions/index";

class RowMultipleTextBoxes extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    let textboxes_el = [];
    let errors_el = [];
    for (let i = 0; i < this.props.attributes.length; i++) {
      const attribute = this.props.attributes[i]
      let className = `form-control ${attribute.className ? attribute.className : ""} `
      if (i != this.props.attributes.length - 1) {
        className = className + " mr_8px"
      }
      textboxes_el.push(
        <input
          key={`tb_${attribute.id}`}
          id={attribute.id}
          type={attribute.type ? attribute.type : "text"}
          name={attribute.name}
          className={className}
          placeholder={attribute.placeholder}
          maxLength={attribute.max_length}
          onChange={(e) => this.props.onChange(e)}
        />
      )
      errors_el.push(
        <InvalidMessageBox key={`err_${attribute.id}`} message={attribute.err_msg}/>
      )
    }

    return (
      <div className="form-group">
        <label htmlFor={this.props.id} className="circle-create-label">
          {this.props.label}
        </label>
        <div className="flex">
          {textboxes_el}
        </div>
        {errors_el}
      </div>
    )

  }
}

RowMultipleTextBoxes.propTypes = {
  attributes: React.PropTypes.array,
};
RowMultipleTextBoxes.defaultProps = {
  attributes: [],
};
export default connect()(RowMultipleTextBoxes);

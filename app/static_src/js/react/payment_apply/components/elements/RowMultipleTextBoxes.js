import React from "react";
import InvalidMessageBox from "~/common/components/InvalidMessageBox";
import {connect} from "react-redux";
import {updateInputData} from "~/payment_apply/actions/index";

/**
 * Arrange multiple text boxes in one row
 */
class RowMultipleTextBoxes extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    let textboxes_el = [];
    let errors_el = [];
    for (let i = 0; i < this.props.attributes.length; i++) {
      const attribute = this.props.attributes[i]
      let className = `flex-extend ${attribute.className ? attribute.className : ""}`
      if (i != this.props.attributes.length - 1) {
        className = className + " mr_8px"
      }
      textboxes_el.push(
        <div className={className} key={`tb_${attribute.id}`}>
          <label htmlFor={attribute.id} className="circle-create-label">
            {attribute.label}
          </label>
          <input
            id={attribute.id}
            type={attribute.type ? attribute.type : "text"}
            name={attribute.name}
            value={attribute.value}
            className="form-control"
            placeholder={attribute.placeholder}
            disabled={attribute.disabled ? true : false}
            maxLength={attribute.max_length ? attribute.max_length : 255}
            onChange={(e) => this.props.onChange(e)}
          />
        </div>
      )
      errors_el.push(
        <InvalidMessageBox key={`err_${attribute.id}`} message={attribute.err_msg}/>
      )
    }

    return (
      <div className="form-group">
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

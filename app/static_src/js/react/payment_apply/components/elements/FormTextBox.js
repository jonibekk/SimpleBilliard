import React from "react";

export default class FormTextBox extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const {id, label, placeholder} = this.props
    // ビジョンが無かったらエリア非表示
    return (
      <div className="form-group">
        <label htmlFor={id} className="circle-create-label">
          {label}
        </label>
        <input
          className="form-control"
          placeholder={placeholder}
          type="text" id={id}
        />
      </div>
    )

  }
}
FormTextBox.propTypes = {
  id: React.PropTypes.string,
  label: React.PropTypes.string,
  placeholder: React.PropTypes.string,
};
FormTextBox.defaultProps = {
  id: "",
  label: "",
  placeholder:""
};


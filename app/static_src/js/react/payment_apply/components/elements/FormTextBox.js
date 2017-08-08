import React from "react";

export default class FormTextBox extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    // ビジョンが無かったらエリア非表示
    return (
      <div className="form-group">
        <label htmlFor={this.props.id} className="circle-create-label">
          {this.props.label}
        </label>
        <input
          id={this.props.id}
          type={this.props.type}
          name={this.props.name}
          className="form-control"
          placeholder={this.props.placeholder}
          maxLength={this.props.max_length}
        />
      </div>
    )

  }
}
FormTextBox.propTypes = {
  id: React.PropTypes.string,
  name: React.PropTypes.string,
  type: React.PropTypes.string,
  label: React.PropTypes.string,
  placeholder: React.PropTypes.string,
  max_length: React.PropTypes.number,
};
FormTextBox.defaultProps = {
  id: "",
  name:"",
  type: "text",
  label: "",
  placeholder:"",
  max_length: 255
};


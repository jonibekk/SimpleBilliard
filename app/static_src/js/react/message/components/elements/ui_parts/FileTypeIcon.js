import React from "react";

export default class FileTypeIcon extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    let class_name = "";
    switch (this.props.file_ext) {
      case "xls":
      case "xlsx":
        class_name = "fa fa-file-excel-o file-excel-icon";
        break;
      case "doc":
      case "docx":
        class_name = "fa fa-file-word-o file-word-icon";
        break;
      case "ppt":
      case "pptx":
        class_name = "fa a fa-file-powerpoint-o file-powerpoint-icon";
        break;
      case "pdf":
        class_name = "fa fa-file-pdf-o file-other-icon";
        break;
      case "zip":
        class_name = "fa fa-file-zip-o file-other-icon";
        break;
      default:
        class_name = "fa fa fa-file-o file-other-icon";
        break;
    }

    return <i className={class_name}></i>
  }
}
FileTypeIcon.propTypes = {
  file_ext: React.PropTypes.string,
};

FileTypeIcon.defaultProps = {
  file_ext: "",
};

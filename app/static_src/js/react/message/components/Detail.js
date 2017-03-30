import React from "react";
import Header from "~/message/components/elements/detail/Header";
import Body from "~/message/components/elements/detail/Body";
import Footer from "~/message/components/elements/detail/Footer";

// TODO:Display loading during fetching initial data
export default class Detail extends React.Component {
  constructor(props) {
    super(props);
  }

  componentWillMount() {
    // Set resource ID included in url.
    this.props.setResourceId(this.props.params.topic_id);
    this.props.fetchInitialData(this.props.params.topic_id);
  }

  componentWillUnmount() {
    this.props.resetStates();
  }

  render() {
    const {detail, file_upload} = this.props;

    return (
      <div className="panel panel-default topicDetail">
        <Header
          topic={detail.topic}
          topic_title_setting_status={detail.topic_title_setting_status}
          save_topic_title_err_msg={detail.save_topic_title_err_msg}
        />
        <Body
          topic={detail.topic}
          messages={detail.messages.data}
          paging={detail.messages.paging}
          loading_more={detail.loading_more}
          is_fetched_initial={detail.is_fetched_initial}
        />
        <Footer
          body={detail.input_data.body}
          is_saving={detail.is_saving}
          {...file_upload}
        />
      </div>
    )
  }
}

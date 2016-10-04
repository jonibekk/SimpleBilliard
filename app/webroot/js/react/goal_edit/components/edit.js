import React from "react";
import ReactDOM from "react-dom";
import {browserHistory} from "react-router";
import * as KeyCode from "~/common/constants/KeyCode";
import UnitSelect from "~/common/components/goal/UnitSelect";
import PhotoUpload from "~/common/components/goal/PhotoUpload";
import InvalidMessageBox from "~/common/components/InvalidMessageBox";
import ValueStartEndInput from "~/common/components/goal/ValueStartEndInput";
import CategorySelect from "~/common/components/goal/CategorySelect";
import LabelInput from "~/common/components/goal/LabelInput";

export default class Edit extends React.Component {
  constructor(props) {
    super(props)
    this.state = {}

    this.onChange = this.onChange.bind(this)
    this.deleteLabel = this.deleteLabel.bind(this)
    this.addLabel = this.addLabel.bind(this)
  }

  componentWillMount() {
    this.props.fetchInitialData(this.props.params.goalId)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.toNextPage) {
      browserHistory.push(`/goals/${this.props.params.goalId}/edit/confirm`)
    }
  }

  onSubmit(e) {
    e.preventDefault()
    if (e.keyCode == KeyCode.ENTER) {
      return false
    }
    this.props.validateGoal(this.props.params.goalId, this.getInputDomData())
  }

  deleteLabel(e) {
    const label = e.currentTarget.getAttribute("data-label")
    this.props.deleteLabel(label)
  }

  addLabel(e) {
    // Enterキーを押した時にラベルとして追加
    if (e.keyCode == KeyCode.ENTER) {
      this.props.addLabel(e.target.value)
    }
  }

  getInputDomData() {
    const photoNode = this.refs.innerPhoto.refs.photo
    const photo = ReactDOM.findDOMNode(photoNode).files[0]
    if (!photo) {
      return {}
    }
    return {photo}
  }

  onKeyPress(e) {
    // ラベル入力でEnterキーを押した場合submitさせない
    // e.keyCodeはonKeyPressイベントでは取れないのでe.charCodeを使用
    if (e.charCode == KeyCode.ENTER) {
      e.preventDefault()
      return false
    }
  }

  onChange(e, childKey = "") {
    this.props.updateInputData({[e.target.name]: e.target.value}, childKey)
  }

  render() {
    const {suggestions, keyword, validationErrors, inputData, goal} = this.props.goal
    const tkrValidationErrors = validationErrors.key_result ? validationErrors.key_result : {};

    return (
      <div className="panel panel-default col-sm-8 col-sm-offset-2 goals-create">
        <form className="goals-create-input"
              encType="multipart/form-data"
              method="post"
              acceptCharset="utf-8"
              onSubmit={(e) => this.onSubmit(e)}>
          <section className="mb_12px">
            <h1 className="goals-create-heading">{__("What is your goal ?")}</h1>
            <p
              className="goals-create-description">{__("Imagine an ambitious outcome that you want to achieve. If your organization has a vision, you should follow it.")}</p>

            <label className="goals-create-input-label">{__("Goal name")}</label>
            <input name="name" className="form-control goals-create-input-form" type="text"
                   placeholder={__("eg. Spread Goalous users in the world")} ref="name"
                   onChange={this.onChange} value={inputData.name}/>
            <InvalidMessageBox message={validationErrors.name}/>

            <CategorySelect
              onChange={(e) => this.props.updateInputData({goal_category_id: e.target.value})}
              categories={this.props.goal.categories}
              value={inputData.goal_category_id}/>
            <InvalidMessageBox message={validationErrors.goal_category_id}/>

            <LabelInput
              suggestions={suggestions}
              keyword={keyword}
              inputLabels={inputData.labels}
              onSuggestionsFetchRequested={this.props.onSuggestionsFetchRequested}
              onSuggestionsClearRequested={this.props.onSuggestionsClearRequested}
              renderSuggestion={(s) => <span>{s.name}</span>}
              getSuggestionValue={(s) => this.props.onSuggestionsFetchRequested}
              onChange={this.props.onChangeAutoSuggest}
              onSuggestionSelected={this.props.onSuggestionSelected}
              shouldRenderSuggestions={() => true}
              onDeleteLabel={this.deleteLabel}
              onKeyDown={this.addLabel}
              onKeyPress={this.onKeyPress}
            />
            <InvalidMessageBox message={validationErrors.labels}/>

            <PhotoUpload uploadPhoto={inputData.photo} imgUrl={goal.medium_large_img_url} ref="innerPhoto"/>
            <InvalidMessageBox message={validationErrors.photo}/>

            <label className="goals-create-input-label">{__("Description")}</label>
            <textarea className="goals-create-input-form mod-textarea" name="description" onChange={this.onChange}
                      value={inputData.description}/>
            <InvalidMessageBox message={validationErrors.description}/>

            <label className="goals-create-input-label">{__("End date")}</label>
            <input className="goals-create-input-form" type="date" name="end_date" onChange={this.onChange}
                   value={inputData.end_date}/>
            <InvalidMessageBox message={validationErrors.end_date}/>
            <label className="goals-create-input-label">{__("Weight")}</label>
            <select className="goals-create-input-form mod-select" name="priority" ref="priority"
                    value={inputData.priority} onChange={this.onChange}>
              {
                this.props.goal.priorities.map((v) => {
                  return (
                    <option key={v.id} value={v.id}>{v.label}</option>
                  )
                })
              }
            </select>
            <InvalidMessageBox message={validationErrors.priority}/>
          </section>
          <section className="mb_32px">
            <h1 className="goals-create-heading">{__("Set Top Key Result")}</h1>
            <p className="goals-create-description">{__("Create a clear and most important Key Result for your goal.")}</p>
            <label className="goals-create-input-label">{__("Top Key Result")}</label>
            <input name="name" type="text" value={inputData.key_result.name}
                   className="form-control goals-create-input-form goals-create-input-form-tkr-name"
                   placeholder={__("eg. Increase Goalous weekly active users")} onChange={(e) => this.onChange(e, "key_result")}/>
            <InvalidMessageBox message={tkrValidationErrors.name}/>

            <UnitSelect value={inputData.key_result.value_unit} units={this.props.goal.units} onChange={(e) => this.onChange(e, "key_result")}/>
            <InvalidMessageBox message={tkrValidationErrors.value_unit}/>

            <ValueStartEndInput inputData={inputData.key_result} onChange={(e) => this.onChange(e, "key_result")}/>
            <InvalidMessageBox message={tkrValidationErrors.start_value}/>
            <InvalidMessageBox message={tkrValidationErrors.target_value}/>

            <label className="goals-create-input-label">{__("Description")}</label>
            <textarea name="description" value={inputData.key_result.description}
                      className="form-control goals-create-input-form mod-textarea"
                      onChange={(e) => this.onChange(e, "key_result")}/>
            <InvalidMessageBox message={tkrValidationErrors.description}/>

          </section>


          <button type="submit" className="goals-create-btn-next btn">{__("Confirm")} ></button>
          <a className="goals-create-btn-cancel btn" href="/">{__("Cancel")}</a>
        </form>
      </div>
    )
  }
}

Edit.propTypes = {
  goal: React.PropTypes.object.isRequired,
}

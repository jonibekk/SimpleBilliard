import React from "react";
import ReactDOM from "react-dom";
import * as KeyCode from "~/common/constants/KeyCode";
import {KeyResult} from "~/common/constants/Model";
import UnitSelect from "~/common/components/goal/UnitSelect";
import PhotoUpload from "~/common/components/goal/PhotoUpload";
import InvalidMessageBox from "~/common/components/InvalidMessageBox";
import ValueStartEndInput from "~/common/components/goal/ValueStartEndInput";
import CategorySelect from "~/common/components/goal/CategorySelect";
import LabelInput from "~/common/components/goal/LabelInput";
import {MaxLength} from "~/common/constants/App";
import { generateTermRangeFormat } from "~/util/date";

export default class Edit extends React.Component {
  constructor(props) {
    super(props)
    this.state = {}

    this.onChange = this.onChange.bind(this)
    this.deleteLabel = this.deleteLabel.bind(this)
    this.addLabel = this.addLabel.bind(this)
  }

  componentWillMount() {
    const referrer = document.referrer || '/'
    let redirect_to = referrer

    // 認定ページからゴール編集した場合、リダイレクト先を認定リストページにした方が良いと判断.
    if (referrer.match(/goals\/approval\/detail/)) {
      redirect_to = '/goals/approval/list'
    }
    this.props.init({redirect_to})
    this.props.fetchInitialData(this.props.params.goalId)
  }

  componentWillReceiveProps(nextProps) {
    if (nextProps.goal.toNextPage) {
      document.location.href = nextProps.goal.redirect_to
    }
  }

  onSubmit(e) {
    e.preventDefault()
    if (e.keyCode == KeyCode.ENTER) {
      return false
    }
    this.props.saveGoal(this.props.params.goalId, this.getInputDomData())
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
    // termを変更する場合はゴールのend_dateも同時に更新する
    if (e.target.name == "term_type") {
      this.props.updateInputData({end_date: this.props.goal.terms[e.target.value].end_date})
    }
    this.props.updateInputData({[e.target.name]: e.target.value}, childKey)
  }

  toggleGroup(groupId) {
    const {groups} = this.props.goal.inputData;

    if (groups[groupId]) {
      delete groups[groupId]
    } else {
      groups[groupId] = true
    }
    
    this.props.updateInputData(groups, 'groups')
  }

  shouldDisplayGroupSelection() {
    const { goal, groups_enabled } = this.props.goal

    if (goal.groups && Object.keys(goal.groups).length === 0) {
      return false
    }

    return groups_enabled
  }

  render() {
    const {suggestions, keyword, validationErrors, inputData, goal, isDisabledSubmit, redirect_to, terms, groups} = this.props.goal
    const tkrValidationErrors = validationErrors.key_result ? validationErrors.key_result : {};

    let progress_reset_warning = null
    const change_unit = goal.top_key_result && inputData.key_result.value_unit != goal.top_key_result.value_unit
    if (change_unit) {
      progress_reset_warning = <div className="warning">{__("If you change the unit, all progress of KR will be reset.")}</div>
    }

    let current_value_input = null
    if (inputData.key_result.value_unit != KeyResult.ValueUnit.NONE) {
      current_value_input = (
        <div>
          <label className="goals-create-input-label">{__("Current")}</label>
          <input name="current_value" type="text" value={inputData.key_result.current_value}
                 className="form-control goals-create-input-form"
                 placeholder={inputData.key_result.current_value}
                 onChange={(e) => this.onChange(e, "key_result")}
          />
          <InvalidMessageBox message={tkrValidationErrors.current_value}/>
        </div>
      )
    }

    const term_options = []
    if (Object.keys(terms).length) {
      term_options.push(
        <option value="current" key={terms.current.start_date}>
          { `${__("This Term")} ( ${generateTermRangeFormat(terms.current.start_date, terms.current.end_date)} ) ` }
        </option>
      )
      // 来期 -> 今期 の変更は許可するが、今期 -> 来期の変更は許可しないため、
      // 今期のゴールの場合は来期の選択肢を出さない
      if (goal.term_type === 'next') {
        term_options.push(
          <option value="next" key={terms.next.start_date}>
            { `${__("Next Term")} ( ${generateTermRangeFormat(terms.next.start_date, terms.next.end_date)} ) ` }
          </option>
        )
      }
    }

    const is_next_to_current = goal.term_type === 'next' && inputData.term_type == 'current'

    return (
      <div className="panel panel-default col-sm-8 col-sm-offset-2 goals-create">
        <form className="goals-create-input"
              encType="multipart/form-data"
              method="post"
              acceptCharset="utf-8"
              onSubmit={(e) => this.onSubmit(e)}>
          <section className="mb_12px">
            <h1 className="goals-approval-heading">{__("Edit goal & Top Key Result")}</h1>

            <h2 className="goals-edit-subject"><i className="fa fa-flag"></i> { __("Goal") }</h2>

            {/* ゴール名 */}
            <label className="goals-create-input-label">{__("Goal name")}</label>
            <input name="name"
                   className="form-control goals-create-input-form" type="text"
                   placeholder={__("eg. Spread Goalous users in the world")} ref="name"
                   value={inputData.name}
                   maxLength={MaxLength.Name}
                   onChange={this.onChange}
            />
            <InvalidMessageBox message={validationErrors.name}/>

            {/* カテゴリ選択 */}
            <CategorySelect
              onChange={(e) => this.props.updateInputData({goal_category_id: e.target.value})}
              categories={this.props.goal.categories}
              value={inputData.goal_category_id}/>
            <InvalidMessageBox message={validationErrors.goal_category_id}/>

            {/* ラベル */}
            <LabelInput
              suggestions={suggestions}
              keyword={keyword}
              inputLabels={inputData.labels}
              onSuggestionsFetchRequested={this.props.onSuggestionsFetchRequested}
              onSuggestionsClearRequested={this.props.onSuggestionsClearRequested}
              getSuggestionValue={(s) => this.props.onSuggestionsFetchRequested}
              onChange={this.props.onChangeAutoSuggest}
              onSuggestionSelected={this.props.onSuggestionSelected}
              shouldRenderSuggestions={() => true}
              onDeleteLabel={this.deleteLabel}
              onKeyDown={this.addLabel}
              onKeyPress={this.onKeyPress}
            />
            <InvalidMessageBox message={validationErrors.labels}/>

            {/* ゴール画像 */}
            <PhotoUpload uploadPhoto={inputData.photo} imgUrl={goal.medium_large_img_url} ref="innerPhoto"/>
            <InvalidMessageBox message={validationErrors.photo}/>

            {/* ゴール詳細 */}
            <label className="goals-create-input-label">{__("Description")}</label>
            <textarea name="description"
                      className="goals-create-input-form mod-textarea"
                      value={inputData.description || ""}
                      placeholder={__("Optional")}
                      maxLength={MaxLength.Description}
                      onChange={this.onChange}
            />
            <InvalidMessageBox message={validationErrors.description}/>

            {/* 期 */}
            <label className="goals-create-input-label">{__("Term")}</label>
            { is_next_to_current && <span className="warning">
                { __("If you change the goal term from the next term to this term, the goal and all KRs’ deadlines will be changed to the last day of the current term.") }
            </span>}
            <select name="term_type" className="form-control goals-create-input-form mod-select"
                    ref="term_type"
                    value={inputData.term_type}
                    onChange={this.onChange}>
              { term_options }
            </select>

            {/* ゴール期限 */}
            <label className="goals-create-input-label">{__("End date")}</label>
            <input className="goals-create-input-form" type="date" name="end_date"
                   onChange={this.onChange}
                   value={inputData.end_date}
                   disabled={ is_next_to_current } />
            <InvalidMessageBox message={validationErrors.end_date}/>

            {/* ゴール重要度 */}
            <label className="goals-create-input-label">{__("Weight")}</label>
            <select className="goals-create-input-form" name="priority" ref="priority"
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

          {/* TKR */}
          <section className="goals-edit-tkr">
            <h2 className="goals-edit-subject"><i className="fa fa-key"></i> { __("Top Key Result") }</h2>

            {/* TKR名 */}
            <label className="goals-create-input-label">{__("Top Key Result name")}</label>
            <input name="name" type="text" value={inputData.key_result.name}
                   className="form-control goals-create-input-form goals-create-input-form-tkr-name"
                   placeholder={__("eg. Increase Goalous weekly active users")}
                   maxLength={MaxLength.Name}
                   onChange={(e) => this.onChange(e, "key_result")}
            />
            <InvalidMessageBox message={tkrValidationErrors.name}/>

            {/* 測定単位 */}
            <label className="goals-create-input-label">{__("Measurement type")}</label>
            {progress_reset_warning}
            <div className="goals-create-layout-flex">
              <UnitSelect
                value={inputData.key_result.value_unit}
                units={this.props.goal.units}
                onChange={(e) => this.onChange(e, "key_result")}
              />
              {/* 測定値 */}
              <ValueStartEndInput
                inputData={inputData.key_result}
                kr={goal.top_key_result}
                onChange={(e) => this.onChange(e, "key_result")}
              />
            </div>
            <InvalidMessageBox message={tkrValidationErrors.value_unit}/>
            <InvalidMessageBox message={tkrValidationErrors.start_value}/>
            <InvalidMessageBox message={tkrValidationErrors.target_value}/>

            {current_value_input}

            {/* TKR詳細 */}
            <label className="goals-create-input-label">{__("Description")}</label>
            <textarea name="description"
                      className="form-control goals-create-input-form mod-textarea"
                      value={inputData.key_result.description || ""}
                      placeholder={__("Optional")}
                      maxLength={MaxLength.Description}
                      onChange={(e) => this.onChange(e, "key_result")}
            />
            <InvalidMessageBox message={tkrValidationErrors.description}/>

          </section>

          {
              this.shouldDisplayGroupSelection() ? 
              <section className="goals-edit-groups">
                <h2 className="goals-edit-subject"><i className="fa fa-user-circle"></i> { __("Shared Groups") }</h2>
                <p className="goals-create-description">{__('You can only add groups to share a Goal.')}</p>
                <div className="goals-create-list">
                  {
                    groups ?  groups.map(group => {
                      const alreadyPresent = goal.groups.findIndex(g => g.id === group.id) !== -1
                      return (
                        <div className="goals-create-list-item" ref={`group-item-${group.id}`}>
                          <div className='left'>
                            <input 
                              type="checkbox" 
                              className="goal-create-checkbox" 
                              disabled={alreadyPresent}
                              onChange={() => this.toggleGroup(group.id)}
                              checked={alreadyPresent || inputData.groups[group.id] === true}
                           />
                          </div>
                          <div className='right'>
                            <div className="goals-create-list-item-title">
                              {group.name}
                            </div>
                            <div className="goals-create-list-item-subtitle">
                              {group.member_count} members
                            </div>
                          </div>
                        </div>
                      )
                    }
                    ) : null
                  }
                </div>
              </section>
            : null
          }
          <button type="submit" className="goals-create-btn-next btn"
                  disabled={`${isDisabledSubmit ? "disabled" : ""}`}>{ goal.is_approvable ? __("Save & Reapply") : __("Save changes")}</button>
          <a className="goals-create-btn-cancel btn" href={ redirect_to }>{__("Cancel")}</a>
        </form>
      </div>
    )
  }
}

Edit.propTypes = {
  goal: React.PropTypes.object.isRequired
}

import React from 'react'
import ReactDOM from 'react-dom'
import Routes from './config/routes'
import KrColumn from '~/kr_column/components/KrColumn'

ReactDOM.render(<Routes />, document.getElementById("GoalSearch"));
ReactDOM.render(<KrColumn />, document.getElementById("kr-column"));

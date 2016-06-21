import React from 'react'
import ReactDOM from 'react-dom'
import Routes, {listen} from './config/routes'

ReactDOM.render(<Routes />, document.getElementById("setup-guide-app"));

listen()

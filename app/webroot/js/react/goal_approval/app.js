import React from 'react'
import ReactDOM from 'react-dom'
import Routes, { unlisten } from './config/routes'

ReactDOM.render(<Routes />, document.getElementById("goal-approval-app"));

// Stop to listen browzer history changing
unlisten()

import React from 'react';
import {DevTools, DebugPanel, LogMonitor} from 'redux-devtools';

export function renderDevTools(store) {
  if (__DEV__) {
    return (
      <DebugPanel top right bottom>
        <DevTools store={store} monitor={LogMonitor} />
      </DebugPanel>
    );
  }

  return null;
}

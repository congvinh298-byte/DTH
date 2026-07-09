import React from 'react';
import { vexo } from 'vexo-analytics';

// Initialize Vexo at the root level, outside of any component
// Recommended to wrap in production-only check
if (__DEV__ === false) {
  vexo('6aba5bac-5e7a-470b-83e2-c31d203e465d');
}

import MainApp from './src/MainApp';

export default function App() {
  return <MainApp />;
}

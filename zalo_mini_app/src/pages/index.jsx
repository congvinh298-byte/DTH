import React from 'react';
import { Page } from 'zmp-ui';

const HomePage = () => {
  return (
    <Page className="page" hideScrollbar>
      <iframe 
        src="https://dienmayhieu.com/index.php" 
        style={{
          width: '100vw',
          height: '100vh',
          border: 'none',
          display: 'block'
        }}
        title="Điện Máy Hiếu"
        allow="geolocation; microphone; camera"
      />
    </Page>
  );
};

export default HomePage;

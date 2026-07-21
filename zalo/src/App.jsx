import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import HomePage from './pages/index';
import CartPage from './pages/cart';
import './css/app.scss';

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/cart" element={<CartPage />} />
        <Route path="*" element={<HomePage />} />
      </Routes>
    </Router>
  );
}

export default App;

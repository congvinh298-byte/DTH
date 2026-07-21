import { useState } from 'react';
import HomePage from './pages/index';
import CartPage from './pages/cart';
import './css/app.scss';

function App() {
  const [page, setPage] = useState('home');

  return page === 'cart' ? <CartPage onBack={() => setPage('home')} /> : <HomePage onCart={() => setPage('cart')} />;
}

export default App;

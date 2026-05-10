import { Routes, Route } from 'react-router-dom'
import { Header } from './components/layout/Header'
import { Footer } from './components/layout/Footer'
import Home from './pages/public/Home'
import Contact from './pages/public/Contact'
import Shop from './pages/public/Shop'
import ProductDetail from './pages/public/ProductDetail'
import Cart from './pages/public/Cart'
import LoginSignup from './pages/auth/LoginSignup'
import './App.css'

function App() {
  return (
    <div className="app-shell">
      <Header />
      
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/contact" element={<Contact />} />
        <Route path="/shop" element={<Shop />} />
        <Route path="/product/:id" element={<ProductDetail />} />
        <Route path="/cart" element={<Cart />} />
        <Route path="/login" element={<LoginSignup />} />
      </Routes>

      <Footer />
    </div>
  )
}

export default App

import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'

export default function Cart() {
  const [cart, setCart] = useState([])
  const [loading, setLoading] = useState(false)

  // Normally we would fetch cart from /api/cart, but since auth is required, we'll mock an empty state
  // to allow the user to see the design.
  
  return (
    <div className="container py-5">
      <h2 className="fw-bold mb-4">Your Shopping Cart</h2>
      
      {cart.length === 0 ? (
        <div className="text-center py-5 bg-white rounded-4 shadow-sm">
          <i className="bi bi-cart-x text-muted" style={{ fontSize: '4rem' }}></i>
          <h4 className="mt-3 text-muted">Your cart is empty</h4>
          <p className="mb-4">Looks like you haven't added anything yet.</p>
          <Link to="/shop" className="btn btn-dark rounded-pill px-5">Continue Shopping</Link>
        </div>
      ) : (
        <div className="row">
          <div className="col-lg-8">
            <div className="card border-0 shadow-sm rounded-4 mb-4">
                {/* Cart items list goes here */}
            </div>
          </div>
          <div className="col-lg-4">
             <div className="card border-0 shadow-sm rounded-4 p-4">
                 <h5 className="fw-bold mb-4">Order Summary</h5>
                 <div className="d-flex justify-content-between mb-2">
                     <span>Subtotal</span>
                     <span>0 ₫</span>
                 </div>
                 <hr />
                 <div className="d-flex justify-content-between mb-4">
                     <span className="fw-bold">Total</span>
                     <span className="fw-bold text-primary">0 ₫</span>
                 </div>
                 <button className="btn btn-dark w-100 rounded-pill">Proceed to Checkout</button>
             </div>
          </div>
        </div>
      )}
    </div>
  )
}

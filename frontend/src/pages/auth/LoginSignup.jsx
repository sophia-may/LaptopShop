import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'

export default function LoginSignup() {
  const [isLogin, setIsLogin] = useState(true)
  const [formData, setFormData] = useState({ email: '', password: '', fullname: '', phone: '' })
  const [error, setError] = useState('')
  const navigate = useNavigate()

  const handleChange = (e) => setFormData({ ...formData, [e.target.name]: e.target.value })

  const handleSubmit = async (e) => {
    e.preventDefault()
    setError('')
    const url = isLogin ? '/api/auth/login' : '/api/auth/register'
    
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
      })
      const data = await res.json()
      
      if (res.ok && data.success) {
        // Save token
        localStorage.setItem('token', data.data.token)
        navigate('/')
      } else {
        setError(data.message || 'Authentication failed')
      }
    } catch (err) {
      setError('Network error')
    }
  }

  return (
    <div className="container py-5">
      <div className="row justify-content-center">
        <div className="col-md-5">
          <div className="card shadow-sm border-0 rounded-4 p-4">
            <div className="d-flex justify-content-between align-items-center mb-4">
              <h2 className="fw-bold m-0">{isLogin ? 'Welcome Back' : 'Create Account'}</h2>
              <button 
                className="btn btn-link text-decoration-none text-muted" 
                onClick={() => { setIsLogin(!isLogin); setError(''); }}
              >
                {isLogin ? 'Need an account?' : 'Already have one?'}
              </button>
            </div>
            
            {error && <div className="alert alert-danger">{error}</div>}

            <form onSubmit={handleSubmit}>
              {!isLogin && (
                <>
                  <div className="mb-3">
                    <label className="form-label">Full Name</label>
                    <input type="text" name="fullname" className="form-control rounded-pill px-4" onChange={handleChange} required />
                  </div>
                  <div className="mb-3">
                    <label className="form-label">Phone</label>
                    <input type="text" name="phone" className="form-control rounded-pill px-4" onChange={handleChange} required />
                  </div>
                </>
              )}
              
              <div className="mb-3">
                <label className="form-label">Email Address</label>
                <input type="email" name="email" className="form-control rounded-pill px-4" onChange={handleChange} required />
              </div>
              
              <div className="mb-4">
                <label className="form-label">Password</label>
                <input type="password" name="password" className="form-control rounded-pill px-4" onChange={handleChange} required />
              </div>

              <button type="submit" className="btn btn-dark w-100 rounded-pill py-2 fw-bold">
                {isLogin ? 'Sign In' : 'Sign Up'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}

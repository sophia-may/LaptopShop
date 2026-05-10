import { useEffect, useState } from 'react'
import { useParams, Link } from 'react-router-dom'

export default function ProductDetail() {
  const { id } = useParams()
  const [product, setProduct] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    // Backend API uses slug, but since we are porting, we might have ID or slug
    // Let's assume the backend supports /api/products/:slug
    fetch(`/api/products/${id}`)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
            setProduct(data.data)
        } else {
            setProduct(data) // fallback if directly returned
        }
      })
      .catch(err => console.warn(err))
      .finally(() => setLoading(false))
  }, [id])

  const imageUrl = (url) => {
    if (!url) return 'https://placehold.co/600x600?text=No+Image'
    if (url.startsWith('http')) return url
    return url.startsWith('/') ? url : `/${url}`
  }

  if (loading) return <div className="container py-5 text-center">Loading product...</div>
  if (!product || !product.id) return <div className="container py-5 text-center">Product not found.</div>

  return (
    <div className="product-detail-page py-5">
      <div className="container bg-white p-5 rounded-4 shadow-sm">
        <nav aria-label="breadcrumb" className="mb-4">
          <ol className="breadcrumb">
            <li className="breadcrumb-item"><Link to="/">Home</Link></li>
            <li className="breadcrumb-item"><Link to="/shop">Shop</Link></li>
            <li className="breadcrumb-item active">{product.name}</li>
          </ol>
        </nav>

        <div className="row">
          <div className="col-md-6 mb-4 text-center">
            <img 
                src={imageUrl(product.image || product.image_url || product.img_url)} 
                alt={product.name} 
                className="img-fluid rounded"
                style={{ maxHeight: '500px', objectFit: 'contain' }}
            />
          </div>
          
          <div className="col-md-6">
            <h1 className="fw-bold mb-3">{product.name}</h1>
            <p className="text-muted fs-5 mb-4">{product.category_name || product.category || 'Laptop'}</p>
            
            <h2 className="text-primary fw-bold mb-4">
                {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.min_price || product.price || product.base_price || 0)}
            </h2>

            <div className="mb-4">
                <h5 className="fw-bold">Description</h5>
                <p className="text-muted" style={{ lineHeight: '1.8' }}>
                    {product.detail_description || product.short_description || 'High quality product carefully crafted with top materials.'}
                </p>
            </div>

            <hr className="my-4" />

            <div className="d-flex gap-3 mt-4">
                <button className="btn btn-dark btn-lg px-5 rounded-pill flex-grow-1">
                    <i className="bi bi-cart-plus me-2"></i> Add to Cart
                </button>
                <button className="btn btn-outline-danger btn-lg rounded-circle" style={{ width: '50px', height: '50px', padding: 0 }}>
                    <i className="bi bi-heart"></i>
                </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

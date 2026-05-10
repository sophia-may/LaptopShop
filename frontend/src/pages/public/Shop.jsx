import { useEffect, useState } from 'react'
import { Link, useSearchParams } from 'react-router-dom'

export default function Shop() {
  const [products, setProducts] = useState([])
  const [totalPages, setTotalPages] = useState(1)
  const [searchParams, setSearchParams] = useSearchParams()
  const page = parseInt(searchParams.get('page') || '1')

  useEffect(() => {
    // Note: The API is JSON. We fetch the products and display them.
    // The endpoint is /api/products
    fetch(`/api/products?${searchParams.toString()}`)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
            setProducts(data.data.items || [])
            setTotalPages(data.data.total_pages || 1)
        } else if (data.items) {
            setProducts(data.items || []) // fallback
            setTotalPages(data.total_pages || 1)
        }
      })
      .catch(err => console.warn(err))
  }, [searchParams])

  const imageUrl = (url) => {
    if (!url) return 'https://placehold.co/400x400?text=No+Image'
    if (url.startsWith('http')) return url
    return url.startsWith('/') ? url : `/${url}`
  }

  const handlePageChange = (p) => {
    const newParams = new URLSearchParams(searchParams)
    newParams.set('page', p)
    setSearchParams(newParams)
  }

  return (
    <div className="shop-page-wrapper" style={{ paddingTop: '20px' }}>
      <section className="shop-banner text-white py-5" style={{ background: 'linear-gradient(135deg, #1e3c72 0%, #2a5298 100%)' }}>
        <div className="container text-center">
            <h1 className="fw-bold display-4">All Products</h1>
            <p className="lead">Find the best laptop for your needs</p>
        </div>
      </section>

      <section className="shop-page-section py-5">
        <div className="container">
          <div className="row">
            {/* Simple Filter Sidebar */}
            <div className="col-lg-3 mb-4">
               <div className="card shadow-sm border-0">
                  <div className="card-body">
                     <h5 className="fw-bold mb-4">Filters</h5>
                     {/* Placeholder for filters, fully implementing this requires fetching brands/categories */}
                     <p className="text-muted small">Category, Brand, and Price filters will go here.</p>
                     <button className="btn btn-outline-dark w-100">Clear Filters</button>
                  </div>
               </div>
            </div>

            {/* Product Grid */}
            <div className="col-lg-9">
                <div className="row g-4">
                    {products.length > 0 ? products.map(product => (
                    <div className="col-md-4 col-sm-6" key={product.id}>
                        <div className="card h-100 border-0 shadow-sm product-card-hover">
                            <Link to={`/product/${product.id}`} className="text-decoration-none">
                                <img src={imageUrl(product.image || product.image_url || product.img_url)} className="card-img-top p-3" alt={product.name} style={{ objectFit: 'contain', height: '200px' }} />
                            </Link>
                            <div className="card-body d-flex flex-column">
                                <h6 className="card-title fw-bold text-dark text-truncate">
                                    <Link to={`/product/${product.id}`} className="text-decoration-none text-dark">{product.name}</Link>
                                </h6>
                                <p className="text-muted small mb-2">{product.category_name || product.category || 'Laptop'}</p>
                                <div className="mt-auto d-flex justify-content-between align-items-center">
                                    <span className="fw-bold text-primary fs-5">
                                        {new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.min_price || product.price || product.base_price || 0)}
                                    </span>
                                    <button className="btn btn-sm btn-dark rounded-circle" style={{ width: '35px', height: '35px' }}>
                                        <i className="bi bi-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    )) : (
                        <div className="col-12 text-center py-5">
                            <h4 className="text-muted">No products found.</h4>
                        </div>
                    )}
                </div>

                {/* Pagination */}
                {totalPages > 1 && (
                    <div className="d-flex justify-content-center mt-5">
                        <div className="btn-group shadow-sm">
                            <button className="btn btn-outline-dark" disabled={page <= 1} onClick={() => handlePageChange(page - 1)}>Prev</button>
                            <button className="btn btn-dark">{page}</button>
                            <button className="btn btn-outline-dark" disabled={page >= totalPages} onClick={() => handlePageChange(page + 1)}>Next</button>
                        </div>
                    </div>
                )}
            </div>
          </div>
        </div>
      </section>
    </div>
  )
}

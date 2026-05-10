import { useEffect, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import logoCellphoneS from '../../assets/Logo_CPS.webp'
import './Header.css'

export function Header() {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const [isScrolled, setIsScrolled] = useState(false)
  const [locationMenuOpen, setLocationMenuOpen] = useState(false)
  const [selectedLocation, setSelectedLocation] = useState('Hồ Chí Minh')
  const scrollIntentRef = useRef('idle')
  const scrollIntentTimerRef = useRef(null)
  const locationMenuRef = useRef(null)

  useEffect(() => {
    const handleResize = () => {
      if (window.innerWidth >= 992) {
        setMobileMenuOpen(false)
      }
    }

    window.addEventListener('resize', handleResize)
    return () => window.removeEventListener('resize', handleResize)
  }, [])

  useEffect(() => {
    const handlePointerDown = (event) => {
      if (locationMenuRef.current && !locationMenuRef.current.contains(event.target)) {
        setLocationMenuOpen(false)
      }
    }

    const handleKeyDown = (event) => {
      if (event.key === 'Escape') {
        setLocationMenuOpen(false)
      }
    }

    document.addEventListener('pointerdown', handlePointerDown)
    document.addEventListener('keydown', handleKeyDown)

    return () => {
      document.removeEventListener('pointerdown', handlePointerDown)
      document.removeEventListener('keydown', handleKeyDown)
    }
  }, [])

  useEffect(() => {
    const setScrollIntent = (intent) => {
      scrollIntentRef.current = intent

      if (scrollIntentTimerRef.current) {
        window.clearTimeout(scrollIntentTimerRef.current)
      }

      scrollIntentTimerRef.current = window.setTimeout(() => {
        scrollIntentRef.current = 'idle'
      }, 180)
    }

    const handleWheel = (event) => {
      if (event.deltaY > 0) {
        setScrollIntent('down')
        setIsScrolled(true)
      } else if (event.deltaY < 0) {
        setScrollIntent('up')
        setIsScrolled(false)
      }
    }

    const handleScroll = () => {
      if (window.scrollY <= 0) {
        setIsScrolled(false)
        return
      }

      if (scrollIntentRef.current === 'up') {
        setIsScrolled(false)
        return
      }

      if (scrollIntentRef.current === 'down') {
        setIsScrolled(true)
      }
    }

    handleScroll()
    window.addEventListener('wheel', handleWheel, { passive: true })
    document.addEventListener('wheel', handleWheel, { passive: true })
    window.addEventListener('scroll', handleScroll, { passive: true })

    return () => {
      if (scrollIntentTimerRef.current) {
        window.clearTimeout(scrollIntentTimerRef.current)
      }

      window.removeEventListener('wheel', handleWheel)
      document.removeEventListener('wheel', handleWheel)
      window.removeEventListener('scroll', handleScroll)
    }
  }, [])

  const promoItems = [
    {
      icon: 'bi-check-circle-fill',
      parts: ['Sản phẩm', 'Chính hãng - Xuất VAT', 'đầy đủ'],
    },
    {
      icon: 'bi-truck',
      parts: ['Giao nhanh - Miễn phí', 'cho đơn 300k'],
    },
    {
      icon: 'bi-arrow-repeat',
      parts: ['Thu cũ', 'giá ngon - Lên đời', 'tiết kiệm'],
    },
  ]

  const promoLinks = [
    { icon: 'bi-geo-alt-fill', label: 'Cửa hàng gần bạn', href: '#' },
    { icon: 'bi-file-earmark-check', label: 'Tra cứu đơn hàng', href: '#' },
    { icon: 'bi-telephone-fill', label: '1800 2097', href: 'tel:18002097' },
  ]

  const locationOptions = ['Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng']

  return (
    <header id="header" className={`cellphones-header sticky-top${isScrolled ? ' is-scrolled' : ''}`}>
      <div className="header-promo d-none d-lg-flex">
        <div className="container">
          <div className="promo-marquee">
            <div className="promo-track">
              {[...promoItems, ...promoItems].map((item, index) => (
                <div key={`${item.icon}-${index}`} className="promo-item">
                  <i className={`bi ${item.icon}`} aria-hidden="true"></i>
                  {item.parts.map((part, partIndex) => {
                    const shouldStrong = item.parts.length === 2 ? partIndex === 0 : partIndex === 1
                    const isLast = partIndex === item.parts.length - 1

                    if (shouldStrong) {
                      return <strong key={partIndex}>{part}</strong>
                    }

                    return <span key={partIndex}>{part}{!isLast ? ' ' : ''}</span>
                  })}
                </div>
              ))}
            </div>
          </div>

          <div className="promo-links d-none d-xl-flex">
            {promoLinks.map((item) => (
              <a key={item.label} href={item.href} className="promo-link">
                <i className={`bi ${item.icon}`} aria-hidden="true"></i>
                <span>{item.label}</span>
              </a>
            ))}
          </div>
        </div>
      </div>

      <nav className="header-main">
        <div className="container">
          <Link to="/" className="brand-logo me-3" aria-label="cellphoneS home">
            <img src={logoCellphoneS} alt="CellphoneS" className="logo-image" />
          </Link>

          <div className="header-nav-desktop flex-grow-1 align-items-center">
            <button type="button" className="btn-header category-btn">
              <i className="bi bi-grid-3x3-gap-fill" aria-hidden="true"></i>
              <span>Danh mục</span>
              <i className="bi bi-chevron-down ms-auto" aria-hidden="true"></i>
            </button>

            <div ref={locationMenuRef} className="location-select">
              <button
                type="button"
                className="btn-header location-btn"
                onClick={() => setLocationMenuOpen((current) => !current)}
                aria-expanded={locationMenuOpen}
                aria-haspopup="menu"
              >
                <i className="bi bi-geo-alt" aria-hidden="true"></i>
                <span className="btn-location-text">{selectedLocation}</span>
                <i className="bi bi-chevron-down ms-auto" aria-hidden="true"></i>
              </button>

              {locationMenuOpen && (
                <div className="location-menu" role="menu" aria-label="Chọn khu vực">
                  {locationOptions.map((option) => (
                    <button
                      key={option}
                      type="button"
                      className={`location-menu-item${selectedLocation === option ? ' active' : ''}`}
                      onClick={() => {
                        setSelectedLocation(option)
                        setLocationMenuOpen(false)
                      }}
                      role="menuitemradio"
                      aria-checked={selectedLocation === option}
                    >
                      <i className="bi bi-geo-alt" aria-hidden="true"></i>
                      <span>{option}</span>
                      {selectedLocation === option && <i className="bi bi-check2 ms-auto" aria-hidden="true"></i>}
                    </button>
                  ))}
                </div>
              )}
            </div>

            <div className="header-search">
              <i className="bi bi-search" aria-hidden="true"></i>
              <input type="text" placeholder="Bạn muốn mua gì hôm nay?" />
            </div>

            <div className="header-actions">
              <Link to="/gio-hang" className="action-link">
                <span>Giỏ hàng</span>
                <span className="icon-with-badge">
                  <i className="bi bi-cart3" aria-hidden="true"></i>
                  <span className="badge">0</span>
                </span>
              </Link>

              <button type="button" className="action-link account-box">
                <span>Đăng nhập</span>
                <span className="icon-with-badge">
                  <i className="bi bi-person-circle" aria-hidden="true"></i>
                  <span className="badge">2</span>
                </span>
              </button>
            </div>
          </div>

          <div className="header-mobile-actions d-lg-none ms-auto">
            <Link to="/gio-hang" className="mobile-cart-link" aria-label="Giỏ hàng">
              <i className="bi bi-cart3" aria-hidden="true"></i>
            </Link>

            <button
              type="button"
              className="mobile-menu-toggle"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              aria-label="Mở menu"
            >
              <i className="bi bi-list" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        {mobileMenuOpen && (
          <div className="header-mobile-panel d-lg-none">
            <div className="header-search mb-2">
              <i className="bi bi-search" aria-hidden="true"></i>
              <input type="text" placeholder="Bạn tìm gì hôm nay?" />
            </div>

            <div className="d-grid gap-2">
              <button type="button" className="btn-header justify-content-center">
                <i className="bi bi-grid-3x3-gap-fill" aria-hidden="true"></i>
                <span>Danh mục</span>
              </button>

              <Link to="/gio-hang" className="btn-header justify-content-center">
                <i className="bi bi-cart3" aria-hidden="true"></i>
                <span>Giỏ hàng</span>
              </Link>
            </div>
          </div>
        )}
      </nav>
    </header>
  )
}

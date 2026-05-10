import React from 'react'
import './HomeHero.css'

const LEFT_CATS = [
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-mobile.svg', parts: [{text:'Điện thoại', href:'/mobile.html'}, {text:'Tablet', href:'/tablet.html'}] },
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-laptop.svg', parts: [{text:'Laptop', href:'/laptop.html'}] },
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-audio-2.svg', parts: [{text:'Âm thanh', href:'/thiet-bi-am-thanh.html'}, {text:'Mic thu âm', href:'/thiet-bi-am-thanh/micro-thu-am.html'}] },
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-watch.svg', parts: [{text:'Đồng hồ', href:'/do-choi-cong-nghe.html'}, {text:'Camera', href:'/phu-kien/camera.html'}] },
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-home-appliances.svg', parts: [{text:'Đồ gia dụng', href:'/do-gia-dung.html'}, {text:'Làm đẹp', href:'/nha-thong-minh/suc-khoe-lam-dep.html'}] },
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-accessories.svg', parts: [{text:'Phụ kiện', href:'/phu-kien.html'}] },
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-pc.svg', parts: [{text:'PC', href:'/may-tinh-de-ban.html'}, {text:'Màn hình', href:'/man-hinh.html'}, {text:'Máy in', href:'/may-in.html'}] },
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-tv.svg', parts: [{text:'Tivi', href:'/tivi.html'}, {text:'Điện máy', href:'/dien-may.html'}] },
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-trade-in.svg', parts: [{text:'Thu cũ đổi mới', href:'/thu-cu-doi-moi'}] },
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-used-goods.svg', parts: [{text:'Hàng cũ', href:'/hang-cu.html'}] },
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-promotions.svg', parts: [{text:'Khuyến mãi', href:'/danh-sach-khuyen-mai'}] },
  { src: 'https://dashboard.cellphones.com.vn/storage/icon-homepage-tech-news.svg', parts: [{text:'Tin công nghệ', href:'/sforum'}] },
]

export default function HomeHero() {
  return (
    <section className="home-hero container my-4">
      <div className="row gx-4">
        <aside className="col-lg-3 d-none d-lg-block left-col">
          <div className="shadow-bottom-50 flex w-56 flex-col overflow-x-hidden rounded-xl bg-white py-2 text-neutral-800 sidebar-card">
            {LEFT_CATS.map((c, idx) => (
              <div key={idx} className="group flex h-10 cursor-pointer items-center px-3 hover:bg-neutral-100 d-flex align-items-center category-item">
                <img alt={c.parts.map(p=>p.text).join(', ')} loading="lazy" width="28" height="28" decoding="async" className="mr-2" src={c.src} />
                <div className="d-flex align-items-center parts-wrapper">
                  <span className="text-xs font-semibold">
                    {c.parts.map((p, i) => (
                      <React.Fragment key={i}>
                        <a className="hover-text-primary" href={p.href}>{p.text}</a>{i < c.parts.length - 1 ? ', ' : ''}
                      </React.Fragment>
                    ))}
                  </span>
                </div>
                <svg stroke="currentColor" fill="currentColor" strokeWidth="0" viewBox="0 0 512 512" className="ml-auto size-5 text-neutral-400" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" strokeLinecap="round" strokeLinejoin="round" strokeWidth="48" d="m184 112 144 144-144 144"></path></svg>
              </div>
            ))}
          </div>
        </aside>

        <main className="col-lg-6">
          <div className="hero-card mb-3">
            <div className="hero-inner d-flex align-items-center">
              <div className="hero-content flex-grow-1">
                <div className="hot-tags d-flex gap-2 mb-2">
                  <span className="tag">MACBOOK NEO</span>
                  <span className="tag">GALAXY S26 ULTRA</span>
                  <span className="tag">OPPO FIND X9</span>
                </div>
                <h2 className="hero-title">TECNO SPARK Go 3</h2>
                <p className="hero-sub text-muted">Bền Mượt 4 Năm • 5000mAh • Sạc nhanh 15W</p>
                <div className="d-flex align-items-center gap-3 mt-3">
                  <div className="price-box">
                    <div className="price">3.49 Triệu</div>
                    <div className="price-note text-muted small">Giá từ</div>
                  </div>
                  <button className="btn btn-danger">MUA NGAY</button>
                </div>
              </div>
              <div className="hero-image ms-3 flex-shrink-0">
                <img src="/images/banner.png" alt="TECNO" />
              </div>
            </div>
          </div>

          <div className="row small-cards g-3">
            <div className="col-md-4">
              <div className="card small-card p-3 text-center">MacBook Pro<br/><small className="text-muted">Nay với M5</small></div>
            </div>
            <div className="col-md-4">
              <div className="card small-card p-3 text-center">Galaxy A17 5G<br/><small className="text-muted">Ưu đãi</small></div>
            </div>
            <div className="col-md-4">
              <div className="card small-card p-3 text-center">Mua Laptop Online<br/><small className="text-muted">Giảm thêm 5 Triệu</small></div>
            </div>
          </div>

        </main>

        <aside className="col-lg-3 d-none d-lg-block">
          <div className="right-card p-3">
            <div className="user-panel mb-3">
              <div className="d-flex align-items-center">
                <div className="avatar me-3"> <img src="/images/avatar.png" alt="user" onError={(e)=>{e.currentTarget.style.display='none'}}/> </div>
                <div>
                  <div className="fw-bold">Trần Anh Tài</div>
                  <div className="text-muted small">039*****81</div>
                </div>
              </div>
            </div>
            <ul className="list-unstyled small-links mb-0">
              <li><i className="bi bi-gift me-2 text-muted"/>Ưu đãi cho giáo dục</li>
              <li><i className="bi bi-arrow-repeat me-2 text-muted"/>Thu cũ lên đời</li>
              <li><i className="bi bi-clipboard-check me-2 text-muted"/>Chính sách ưu đãi</li>
            </ul>
          </div>
        </aside>
      </div>

      <div className="promo-strip mt-4 p-3 text-center rounded-3">
        <strong>Say Hi! S-STUDENT S-TEACHER — Trợ giá lên đến 5 Triệu</strong>
      </div>
    </section>
  )
}

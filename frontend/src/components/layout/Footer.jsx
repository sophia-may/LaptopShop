import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'

export function Footer() {
  const [settings, setSettings] = useState({})

  useEffect(() => {
    fetch('/api/site-settings/public')
      .then(res => res.json())
      .then(res => {
        if (res.success) setSettings(res.data)
        else setSettings(res)
      })
      .catch(err => console.warn(err))
  }, [])

  return (
    <section className="contact-footer-section">
      <div className="contact-container">
        <div className="contact-grid">
          <div>
            <div className="contact-brand-name">{settings['general.site_name'] || 'PhoneStore'}.</div>
            <p className="contact-address">
              {settings['footer.about_text'] || 'Your trusted partner for quality phones.'}
            </p>
          </div>

          <div>
            <div className="contact-column-title">Links</div>
            <ul className="contact-link-list">
              <li><Link className="contact-link" to="/">Home</Link></li>
              <li><Link className="contact-link" to="/shop">Shop</Link></li>
              <li><Link className="contact-link" to="/about">About</Link></li>
              <li><Link className="contact-link" to="/contact">Contact</Link></li>
            </ul>
          </div>

          <div>
            <div className="contact-column-title">Help</div>
            <ul className="contact-link-list">
              <li><Link className="contact-link" to="/qna">Payment Options</Link></li>
              <li><Link className="contact-link" to="/qna">Returns</Link></li>
              <li><Link className="contact-link" to="/qna">Privacy Policies</Link></li>
            </ul>
          </div>

          <div>
            <div className="contact-column-title">Follow Us</div>
            <ul className="contact-link-list">
              {settings['footer.social_facebook'] && (
                <li><a className="contact-link" href={settings['footer.social_facebook']} target="_blank" rel="noreferrer">Facebook</a></li>
              )}
              {settings['footer.social_instagram'] && (
                <li><a className="contact-link" href={settings['footer.social_instagram']} target="_blank" rel="noreferrer">Instagram</a></li>
              )}
              {settings['footer.social_twitter'] && (
                <li><a className="contact-link" href={settings['footer.social_twitter']} target="_blank" rel="noreferrer">Twitter</a></li>
              )}
            </ul>
          </div>
        </div>

        <div className="contact-bottom-line">
          {new Date().getFullYear()} {settings['general.site_name'] || 'PhoneStore'}. All rights reserved
        </div>
      </div>
    </section>
  )
}

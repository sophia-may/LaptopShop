import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'

const getJson = async (path) => {
  const response = await fetch(path)
  if (!response.ok) throw new Error(`API error: ${response.status}`)
  return response.json()
}

export default function Contact() {
  const [settings, setSettings] = useState({})
  const [formData, setFormData] = useState({ name: '', email: '', subject: '', message: '' })
  const [status, setStatus] = useState({ type: '', message: '' })

  useEffect(() => {
    document.title = `${settings.site_name || 'PhoneStore'} - Contact`
  }, [settings])

  useEffect(() => {
    const loadSettings = async () => {
      try {
        const res = await getJson('/api/site-settings/public')
        if (res.success) {
            setSettings(res.data || {})
        } else {
            setSettings(res || {}) // fallback
        }
      } catch (error) {
        console.warn('Failed to load settings', error)
      }
    }
    loadSettings()
  }, [])

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setStatus({ type: 'info', message: 'Sending...' })
    
    try {
      // In the FE backend, the API expects x-www-form-urlencoded or multipart
      // The backend/ API expects JSON.
      const response = await fetch('/api/contacts', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            customer_name: formData.name,
            customer_email: formData.email,
            subject: formData.subject,
            message: formData.message
        })
      })
      
      const result = await response.json()
      if (result.success || response.ok) {
        setStatus({ type: 'success', message: 'Message sent successfully! We will contact you soon.' })
        setFormData({ name: '', email: '', subject: '', message: '' })
      } else {
        setStatus({ type: 'danger', message: result.message || 'Failed to send message.' })
      }
    } catch (err) {
      setStatus({ type: 'danger', message: 'An error occurred. Please try again.' })
    }
  }

  return (
    <main className="contact-page">
      <section className="contact-hero">
        <div className="contact-hero-banner"></div>
        <div className="contact-hero-overlay">
          <div className="contact-hero-inner">
            <p className="contact-hero-label">Contact</p>
            <h1 className="contact-hero-title">Contact</h1>
            <nav className="contact-breadcrumb">
              <Link to="/">Home</Link>
              <span>›</span>
              <span>Contact</span>
            </nav>
          </div>
        </div>
      </section>

      <section className="contact-main-section">
        <div className="contact-container">
          <header className="contact-main-header text-center">
            <h2>{settings['contact.page_title'] || 'Get In Touch With Us'}</h2>
            <p>
              {settings['contact.page_subtitle'] || "For more information about our products & services, feel free to drop us an email. Our staff will always be there to help you out. Don't hesitate!"}
            </p>
          </header>

          <div className="contact-main-grid">
            <div className="contact-info-column">
              <div className="contact-info-block">
                <div className="contact-info-icon">
                  <i className="bi bi-geo-alt-fill"></i>
                </div>
                <div>
                  <h3 className="contact-info-title">Address</h3>
                  <p className="contact-info-text">
                    {settings['contact.address'] || '123 Street, City'}
                  </p>
                </div>
              </div>

              <div className="contact-info-block">
                <div className="contact-info-icon">
                  <i className="bi bi-telephone-fill"></i>
                </div>
                <div>
                  <h3 className="contact-info-title">Phone</h3>
                  <p className="contact-info-text mb-1">
                    {settings['contact.phone'] || '+84 123 456 789'}
                  </p>
                  <p className="contact-info-text mb-1">
                    Email: {settings['contact.email'] || 'info@phonestore.com'}
                  </p>
                </div>
              </div>

              <div className="contact-info-block">
                <div className="contact-info-icon">
                  <i className="bi bi-clock-fill"></i>
                </div>
                <div>
                  <h3 className="contact-info-title">Working Time</h3>
                  <p className="contact-info-text mb-1">
                    {settings['contact.working_hours'] || 'Mon-Fri: 9AM - 6PM'}
                  </p>
                </div>
              </div>
            </div>

            <div className="contact-form-column">
              {status.message && (
                <div className={`alert alert-${status.type} mb-3`}>
                  {status.message}
                </div>
              )}
              <form onSubmit={handleSubmit}>
                <div className="mb-3">
                  <label htmlFor="contact_name" className="form-label">Your name</label>
                  <input
                    type="text"
                    className="form-control contact-input"
                    id="contact_name"
                    name="name"
                    placeholder="Abc"
                    value={formData.name}
                    onChange={handleChange}
                    required
                  />
                </div>

                <div className="mb-3">
                  <label htmlFor="contact_email" className="form-label">Email address</label>
                  <input
                    type="email"
                    className="form-control contact-input"
                    id="contact_email"
                    name="email"
                    placeholder="abc@def.com"
                    value={formData.email}
                    onChange={handleChange}
                    required
                  />
                </div>

                <div className="mb-3">
                  <label htmlFor="contact_subject" className="form-label">Subject</label>
                  <input
                    type="text"
                    className="form-control contact-input"
                    id="contact_subject"
                    name="subject"
                    placeholder="This is optional"
                    value={formData.subject}
                    onChange={handleChange}
                  />
                </div>

                <div className="mb-4">
                  <label htmlFor="contact_message" className="form-label">Message</label>
                  <textarea
                    className="form-control contact-input contact-textarea"
                    id="contact_message"
                    name="message"
                    rows="4"
                    placeholder="Hi! I'd like to ask about..."
                    value={formData.message}
                    onChange={handleChange}
                    required
                  ></textarea>
                </div>

                <button type="submit" className="contact-submit-btn">
                  Submit
                </button>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>
  )
}

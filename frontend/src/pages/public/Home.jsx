import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import HomeHero from '../../components/layout/HomeHero'

const getJson = async (path) => {
  const response = await fetch(path)
  if (!response.ok) throw new Error(`API error: ${response.status}`)
  return response.json()
}

export default function Home() {
  const [settings, setSettings] = useState({})

  useEffect(() => {
    document.title = settings.site_name || 'PhoneStore - Home'
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

  return (
    <main className="home-page-scroll" style={{ backgroundColor: '#f4f6f8', minHeight: '100vh', paddingBottom: '20px' }}>
      <HomeHero />
    </main>
  )
}

import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest'
import { getCompanies, type Company } from '@/services/companies'

const g = globalThis as typeof globalThis & { fetch: ReturnType<typeof vi.fn> }

describe('companies service', () => {
  beforeEach(() => {
    g.fetch = vi.fn()
  })

  afterEach(() => {
    vi.restoreAllMocks()
  })

  it('gets companies list', async () => {
    const companies: Company[] = [
      { id: 1, name: 'BingBong' },
      { id: 2, name: 'BongBing' },
    ]
    g.fetch.mockResolvedValueOnce(
      new Response(JSON.stringify({ success: true, data: companies }), {
        status: 200,
        headers: { 'Content-Type': 'application/json' },
      })
    )

    const res = await getCompanies()
    expect(res).toEqual(companies)
    expect(g.fetch).toHaveBeenCalledWith(expect.stringMatching(/\/api\/companies$/), expect.any(Object))
  })
})


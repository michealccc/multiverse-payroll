import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest'
import { http } from '@/services/http'

const g = globalThis as typeof globalThis & { fetch: ReturnType<typeof vi.fn> }

describe('http service', () => {
  beforeEach(() => {
    g.fetch = vi.fn()
  })

  afterEach(() => {
    vi.restoreAllMocks()
  })

  it('returns data when API responds with success', async () => {
    g.fetch.mockResolvedValueOnce(
      new Response(JSON.stringify({ success: true, data: { ok: true } }), {
        status: 200,
        headers: { 'Content-Type': 'application/json' },
      }),
    )
    const res = await http<{ ok: boolean }>('/employees')
    expect(res.ok).toBe(true)
    expect(g.fetch).toHaveBeenCalledWith(
      expect.stringMatching(/\/api\/employees$/),
      expect.any(Object),
    )
  })

  it('throws when API indicates failure (success=false)', async () => {
    g.fetch.mockResolvedValueOnce(
      new Response(JSON.stringify({ success: false, message: 'failed' }), {
        status: 200,
        headers: { 'Content-Type': 'application/json' },
      }),
    )
    await expect(http('/employees')).rejects.toThrow(/failed/)
  })

  it('throws on non-200 response', async () => {
    g.fetch.mockResolvedValueOnce(new Response('fail', { status: 500 }))
    await expect(http('/employees')).rejects.toThrow(/HTTP 500/)
  })
})

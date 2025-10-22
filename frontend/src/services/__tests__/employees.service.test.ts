import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest'
import { getEmployees, updateEmployeeEmail, type Employee } from '@/services/employees'

const g = globalThis as typeof globalThis & { fetch: ReturnType<typeof vi.fn> }

describe('employees service', () => {
  beforeEach(() => {
    g.fetch = vi.fn()
  })

  afterEach(() => {
    vi.restoreAllMocks()
  })

  it('gets employees list', async () => {
    const employees: Employee[] = [
      { id: 1, company_id: 1, full_name: 'John Doe', email: 'john@acme.com', salary: 50000 },
    ]
    g.fetch.mockResolvedValueOnce(
      new Response(JSON.stringify({ success: true, data: employees }), {
        status: 200,
        headers: { 'Content-Type': 'application/json' },
      }),
    )

    const res = await getEmployees()
    expect(res).toEqual(employees)
    expect(g.fetch).toHaveBeenCalledWith(
      expect.stringMatching(/\/api\/employees$/),
      expect.any(Object),
    )
  })

  it('updates employee email', async () => {
    const updated: Employee = {
      id: 2,
      company_id: 1,
      full_name: 'Jane Doe',
      email: 'new@acme.com',
      salary: 60000,
    }
    g.fetch.mockResolvedValueOnce(
      new Response(JSON.stringify({ success: true, data: updated }), {
        status: 200,
        headers: { 'Content-Type': 'application/json' },
      }),
    )

    const res = await updateEmployeeEmail(2, 'new@acme.com')
    expect(res.email).toBe('new@acme.com')
    const [, opts] = g.fetch.mock.calls[0]
    expect(opts.method).toBe('PUT')
    expect(JSON.parse(opts.body).email).toBe('new@acme.com')
  })
})

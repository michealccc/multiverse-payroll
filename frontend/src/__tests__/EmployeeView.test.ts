import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import EmployeeView from '@/views/EmployeeView.vue'

vi.mock('@/services/employees', () => ({
  getEmployees: vi.fn(async () => [
    { id: 1, company_id: 1, full_name: 'John Doe', email: 'old@acme.com', salary: 50000 },
  ]),
  updateEmployeeEmail: vi.fn(async (_id: number, email: string) => ({
    id: 1,
    company_id: 1,
    full_name: 'John Doe',
    email,
    salary: 50000,
  })),
}))

describe('EmployeeView', () => {
  it('renders employees and lets you update email', async () => {
    const wrapper = mount(EmployeeView)
    // wait for onMounted fetch
    await new Promise((r) => setTimeout(r, 0))

    const table = wrapper.get('[data-testid="employees-table"]')
    expect(table.text()).toContain('John Doe')

    const input = wrapper.get('input[aria-label="email-input"]')
    expect((input.element as HTMLInputElement).value).toBe('old@acme.com')
    await input.setValue('new@acme.com')
    await input.trigger('change')

    await new Promise((r) => setTimeout(r, 0))
    expect((wrapper.get('input[aria-label="email-input"]').element as HTMLInputElement).value).toBe('new@acme.com')
  })
})

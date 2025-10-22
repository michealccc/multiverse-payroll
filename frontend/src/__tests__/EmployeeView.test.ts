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

    // Click edit button to enter edit mode
    await wrapper.get('button[aria-label="edit-email"]').trigger('click')

    const input = wrapper.get('input[aria-label="email-edit-input"]')
    expect((input.element as HTMLInputElement).value).toBe('old@acme.com')
    await input.setValue('new@acme.com')

    // Click save button
    await wrapper.get('button[aria-label="save-email"]').trigger('click')

    await new Promise((r) => setTimeout(r, 0))
    // Back to read mode with updated text
    expect(wrapper.get('[aria-label="email-text"]').text()).toBe('new@acme.com')
  })
})

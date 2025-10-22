import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import EmployeeView from '@/views/EmployeeView.vue'

const { mockedUpdate } = vi.hoisted(() => ({
  mockedUpdate: vi.fn(async (_id: number, email: string) => ({
    id: 1,
    company_id: 1,
    full_name: 'John Doe',
    email,
    salary: 50000,
  })),
}))

vi.mock('@/services/employees', () => ({
  getEmployees: vi.fn(async () => [
    { id: 1, company_id: 1, full_name: 'John Doe', email: 'old@acme.com', salary: 50000 },
  ]),
  updateEmployeeEmail: mockedUpdate,
}))

describe('EmployeeView edit/save', () => {
  it('allows entering edit mode and saving email', async () => {
    const wrapper = mount(EmployeeView)
    await new Promise((r) => setTimeout(r, 0))

    // shows email text and an Edit button
    expect(wrapper.text()).toContain('old@acme.com')
    const editBtn = wrapper.get('button[aria-label="edit-email"]')
    await editBtn.trigger('click')

    // input appears with current value and Save/Cancel buttons
    const input = wrapper.get('input[aria-label="email-edit-input"]')
    expect((input.element as HTMLInputElement).value).toBe('old@acme.com')
    await input.setValue('new@acme.com')
    await wrapper.get('button[aria-label="save-email"]').trigger('click')

    // wait for save
    await new Promise((r) => setTimeout(r, 0))
    expect(mockedUpdate).toHaveBeenCalledWith(1, 'new@acme.com')
    expect(wrapper.text()).toContain('new@acme.com')
  })

  it('validates email and blocks save when invalid', async () => {
    mockedUpdate.mockClear()
    const wrapper = mount(EmployeeView)
    await new Promise((r) => setTimeout(r, 0))

    await wrapper.get('button[aria-label="edit-email"]').trigger('click')
    const input = wrapper.get('input[aria-label="email-edit-input"]')
    await input.setValue('not-an-email')
    const saveBtn = wrapper.get('button[aria-label="save-email"]')
    await saveBtn.trigger('click')

    // should be disabled and not call update
    expect((saveBtn.element as HTMLButtonElement).disabled).toBe(true)
    expect(mockedUpdate).not.toHaveBeenCalled()
    // live validation error shown
    expect(wrapper.get('[data-testid="email-error"]').text()).toMatch(/valid email/i)
    // still in edit mode
    expect(wrapper.find('input[aria-label="email-edit-input"]').exists()).toBe(true)
  })
})

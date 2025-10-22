import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import CompaniesSummary from '@/components/CompaniesSummary.vue'

vi.mock('@/services/companies', () => ({
  getCompanies: vi.fn(async () => [
    { id: 1, name: 'ACME Corporation', employee_count: 4, average_salary: 52500 },
    { id: 2, name: 'Stark Industries', employee_count: 3, average_salary: 65000 },
  ]),
}))

describe('CompaniesSummary', () => {
  it('renders companies from API with counts', async () => {
    const wrapper = mount(CompaniesSummary)
    await new Promise((r) => setTimeout(r, 0))

    const list = wrapper.get('[data-testid="companies-summary-list"]')
    expect(list.text()).toContain('ACME Corporation')
    expect(list.text()).toContain('4')
    expect(list.text()).toContain('Stark Industries')
    expect(list.text()).toContain('3')
  })

  it('renders companies with average salaries', async () => {
    const wrapper = mount(CompaniesSummary)
    await new Promise((r) => setTimeout(r, 0))

    const list = wrapper.get('[data-testid="companies-summary-list"]')
    expect(list.text()).toContain('Average Salary: $52,500')
    expect(list.text()).toContain('Average Salary: $65,000')
  })
})

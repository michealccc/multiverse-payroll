import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import CompanyView from '@/views/CompanyView.vue'

// Mock the companies service to isolate the view
vi.mock('@/services/companies', () => ({
  getCompanies: vi.fn(async () => [
    { id: 1, name: 'Acme' },
    { id: 2, name: 'Stark' },
  ]),
}))

describe('CompanyView', () => {
  it('renders a list of companies from the API', async () => {
    const wrapper = mount(CompanyView)

    // allow onMounted to resolve
    await new Promise((r) => setTimeout(r, 0))

    expect(wrapper.text()).toContain('Acme')
    expect(wrapper.text()).toContain('Stark')
  })
})

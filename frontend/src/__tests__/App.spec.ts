import { describe, it, expect } from 'vitest'

import { mount } from '@vue/test-utils'
import { defineComponent } from 'vue'
import App from '../App.vue'

describe('App', () => {
  it('mounts renders properly', () => {
    const wrapper = mount(App, {
      global: {
        stubs: {
          RouterLink: defineComponent({
            props: ['to'],
            template: '<a><slot /></a>',
          }),
          RouterView: defineComponent({ template: '<div />' }),
        },
      },
    })
    expect(wrapper.text()).toContain('Multiverse Payroll')
  })
})

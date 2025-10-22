import { http } from '@/services/http'

export interface Employee {
  id: number
  company_id: number
  full_name: string
  email: string
  salary: number
  created_at?: string
  updated_at?: string
}

export async function getEmployees(): Promise<Employee[]> {
  return http<Employee[]>('/employees')
}

export async function updateEmployeeEmail(id: number, email: string): Promise<Employee> {
  return http<Employee>(`/employees/${id}`, { method: 'PUT', body: { email } })
}

import { http } from '@/services/http'

export interface Company {
  id: number
  name: string
  employee_count?: number
  average_salary?: number
}

export async function getCompanies(): Promise<Company[]> {
  return http<Company[]>('/companies')
}

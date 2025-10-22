import { http } from './http'

export interface CsvUploadResponse {
  success: boolean
  total_rows: number
  companies_created: number
  employees_imported: number
  employees_failed: number
  errors: string[]
}

/**
 * Upload CSV content to import employees and companies
 * @param csvContent The CSV file content as a string
 * @returns Upload statistics and any errors
 */
export async function uploadCsv(csvContent: string): Promise<CsvUploadResponse> {
  return http<CsvUploadResponse>('/csv/upload', {
    method: 'POST',
    body: { csv: csvContent },
  })
}

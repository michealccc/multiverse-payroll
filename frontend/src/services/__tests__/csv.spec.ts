import { describe, it, expect, vi, beforeEach } from 'vitest'
import { uploadCsv } from '../csv'

// Mock the http module
vi.mock('../http', () => ({
  http: vi.fn(),
}))

import { http } from '../http'

describe('CSV Service', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  describe('uploadCsv', () => {
    it('should successfully upload CSV content', async () => {
      const mockResponse = {
        success: true,
        total_rows: 3,
        companies_created: 1,
        employees_imported: 3,
        employees_failed: 0,
        errors: [],
      }

      vi.mocked(http).mockResolvedValue(mockResponse)

      const csvContent =
        'Company Name,Employee Name,Email Address,Salary\nACME,John Doe,john@acme.com,50000'
      const result = await uploadCsv(csvContent)

      expect(http).toHaveBeenCalledWith('/csv/upload', {
        method: 'POST',
        body: { csv_content: csvContent },
      })
      expect(result).toEqual(mockResponse)
    })

    it('should handle upload with validation errors', async () => {
      const mockResponse = {
        success: true,
        total_rows: 5,
        companies_created: 1,
        employees_imported: 3,
        employees_failed: 2,
        errors: ['Row 2: Invalid email format', 'Row 4: Missing required fields'],
      }

      vi.mocked(http).mockResolvedValue(mockResponse)

      const csvContent =
        'Company Name,Employee Name,Email Address,Salary\nACME,John,invalid-email,50000'
      const result = await uploadCsv(csvContent)

      expect(result.employees_failed).toBe(2)
      expect(result.errors).toHaveLength(2)
    })

    it('should handle HTTP errors', async () => {
      vi.mocked(http).mockRejectedValue(new Error('HTTP 400: Invalid CSV format'))

      const csvContent = 'invalid csv content'

      await expect(uploadCsv(csvContent)).rejects.toThrow('HTTP 400: Invalid CSV format')
    })

    it('should handle network errors', async () => {
      vi.mocked(http).mockRejectedValue(new Error('Network error'))

      const csvContent = 'Company Name,Employee Name,Email Address,Salary'

      await expect(uploadCsv(csvContent)).rejects.toThrow('Network error')
    })
  })
})

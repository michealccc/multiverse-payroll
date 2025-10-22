export type HttpMethod = 'GET' | 'POST' | 'PUT' | 'DELETE'

export interface ApiResponse<T> {
  success: boolean
  data?: T
  message?: string
}

const BASE_URL = (import.meta?.env?.VITE_API_BASE as string) || '/api'

export async function http<T>(
  path: string,
  options: { method?: HttpMethod; body?: unknown; headers?: Record<string, string> } = {}
): Promise<T> {
  const url = path.startsWith('http') ? path : `${BASE_URL}${path}`
  const { method = 'GET', body, headers = {} } = options

  const res = await fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      ...headers,
    },
    body: body !== undefined ? JSON.stringify(body) : undefined,
  })

  if (!res.ok) {
    const text = await res.text().catch(() => '')
    throw new Error(`HTTP ${res.status}: ${text || res.statusText}`)
  }

  const json = (await res.json()) as ApiResponse<T>
  if ((json as any)?.success === false) {
    throw new Error(json.message || 'API error')
  }
  return (json?.data as T) ?? (json as unknown as T)
}


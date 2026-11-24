import { PUBLIC_API_URL } from '$env/static/public';

export class ApiError extends Error {
	status: number;

	constructor(message: string, status: number) {
		super(message);
		this.name = 'ApiError';
		this.status = status;
	}
}

interface RequestOptions {
	method?: 'GET' | 'POST' | 'PUT' | 'PATCH' | 'DELETE';
	body?: Record<string, unknown>;
	token?: string | null;
}

/**
 * Wrapper fetch tipis ke backend CodeIgniter. Melempar ApiError bila
 * responsnya bukan 2xx, dengan pesan dari body JSON backend jika ada.
 */
export async function apiFetch<T>(path: string, options: RequestOptions = {}): Promise<T> {
	const { method = 'GET', body, token } = options;

	const headers: Record<string, string> = {
		Accept: 'application/json'
	};

	if (body) {
		headers['Content-Type'] = 'application/json';
	}

	if (token) {
		headers.Authorization = `Bearer ${token}`;
	}

	const response = await fetch(`${PUBLIC_API_URL}${path}`, {
		method,
		headers,
		body: body ? JSON.stringify(body) : undefined
	});

	const isJson = response.headers.get('content-type')?.includes('application/json');
	const data = isJson ? await response.json() : null;

	if (!response.ok) {
		const message =
			(data && typeof data === 'object' && 'message' in data && String(data.message)) ||
			'Terjadi kesalahan. Silakan coba lagi.';
		throw new ApiError(message, response.status);
	}

	return data as T;
}
